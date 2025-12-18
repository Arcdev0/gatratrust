<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use App\Models\ProcedureRevision;
use App\Models\ProcedureFile;
use App\Models\ProcedureSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ProcedureController extends Controller
{
    public function index()
    {
        return view('procedures.index');
    }

    public function create()
    {
        return view('procedures.create');
    }

    public function datatable(Request $request)
    {
        $query = Procedure::query()
            ->with([
                'currentRevision:id,procedure_id,rev_no,status,updated_at',
            ])
            ->select(['id', 'no_dok', 'nama_dok', 'tanggal_berlaku', 'current_revision_id', 'created_at'])
            ->orderByDesc('id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal_berlaku', function ($row) {
                return $row->tanggal_berlaku ? $row->tanggal_berlaku->format('d-m-Y') : '-';
            })
            ->addColumn('rev_no', function ($row) {
                $rev = $row->currentRevision?->rev_no;
                return $rev === null ? '-' : 'REV ' . str_pad((string)$rev, 2, '0', STR_PAD_LEFT);
            })
            ->addColumn('status', function ($row) {
                $status = $row->currentRevision?->status ?? '-';
                return $this->renderStatusBadge($status);
            })
            ->addColumn('action', function ($row) {
                $showBtn = '<button type="button" class="btn btn-sm btn-info btn-show" data-id="' . $row->id . '">
                                <i class="fas fa-eye"></i>
                            </button>';

                $editBtn = '<a href="' . route('procedures.edit', $row->id) . '" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>';

                $deleteForm = '
                    <form action="' . route('procedures.destroy', $row->id) . '" method="POST"
                          style="display:inline-block" onsubmit="return confirm(\'Hapus prosedur ini?\')">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';

                return $showBtn . ' ' . $editBtn . ' ' . $deleteForm;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Store master procedure (metadata only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_dok' => ['required', 'string', 'max:100', 'unique:procedures,no_dok'],
            'nama_dok' => ['required', 'string', 'max:255'],
            'tanggal_berlaku' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = Auth::id();

        $procedure = Procedure::create($validated);

        return redirect()->route('procedures.index')
            ->with('success', 'Prosedur berhasil dibuat. Silakan upload dokumen (REV 00).');
    }

    /**
     * Show detail untuk modal / halaman detail
     * return partial HTML (buat modal)
     */
    public function show(Procedure $procedure)
    {
        $procedure->load([
            'creator:id,name',
            'revisions' => function ($q) {
                $q->orderByDesc('rev_no')
                    ->with([
                        'creator:id,name',
                        'rejector:id,name',
                        'files:id,procedure_revision_id,file_name,file_path,created_at',
                        'signatures.user:id,name',
                    ]);
            },
            'currentRevision',
        ]);

        return view('procedures.partials.show', compact('procedure'));
    }

    public function edit(Procedure $procedure)
    {
        return view('procedures.edit', compact('procedure'));
    }

    public function update(Request $request, Procedure $procedure)
    {
        $validated = $request->validate([
            'no_dok' => ['required', 'string', 'max:100', Rule::unique('procedures', 'no_dok')->ignore($procedure->id)],
            'nama_dok' => ['required', 'string', 'max:255'],
            'tanggal_berlaku' => ['nullable', 'date'],
        ]);

        $procedure->update($validated);

        return redirect()->route('procedures.index')->with('success', 'Prosedur berhasil diperbarui.');
    }

    public function destroy(Procedure $procedure)
    {
        $procedure->delete();
        return redirect()->route('procedures.index')->with('success', 'Prosedur berhasil dihapus.');
    }

    /**
     * Upload dokumen (multi-file) -> otomatis create revision baru (REV 00 / next)
     * - Status awal revision: pending
     * - File lama tidak dihapus (karena beda revision)
     */
    public function upload(Request $request, Procedure $procedure)
    {
        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:20480'], // 20MB per file (sesuaikan)
            'change_note' => ['nullable', 'string'],
            'tanggal_rev' => ['nullable', 'date'],
        ]);

        return DB::transaction(function () use ($procedure, $validated, $request) {
            // next rev_no
            $lastRev = ProcedureRevision::where('procedure_id', $procedure->id)->max('rev_no');
            $nextRev = is_null($lastRev) ? 0 : ((int)$lastRev + 1);

            // create revision
            $revision = ProcedureRevision::create([
                'procedure_id' => $procedure->id,
                'rev_no' => $nextRev,
                'tanggal_rev' => $validated['tanggal_rev'] ?? now()->toDateString(),
                'change_note' => $validated['change_note'] ?? null,
                'status' => ProcedureRevision::STATUS_PENDING,
                'created_by' => Auth::id(),
                'reject_reason' => null,
                'rejected_by' => null,
                'rejected_at' => null,
            ]);

            // create 3 signature rows (pending, kosong user_id dulu)
            $this->ensureSignatureRows($revision);

            // store files
            foreach ($request->file('files') as $file) {
                $original = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $mime = $file->getClientMimeType();
                $size = $file->getSize();

                $path = $file->store(
                    "procedures/{$procedure->id}/rev-" . str_pad((string)$nextRev, 2, '0', STR_PAD_LEFT),
                    'public'
                );

                ProcedureFile::create([
                    'procedure_revision_id' => $revision->id,
                    'file_path' => $path,
                    'file_name' => $original,
                    'file_ext' => $ext ?: null,
                    'file_size' => $size ?: null,
                    'mime_type' => $mime ?: null,
                    'uploaded_by' => Auth::id(),
                ]);
            }

            // update current revision pointer
            $procedure->update(['current_revision_id' => $revision->id]);

            return redirect()->route('procedures.index')
                ->with('success', 'Dokumen berhasil diupload. ' . $this->revText($nextRev) . ' status: PENDING.');
        });
    }

    /**
     * Submit revision ke tahap "pending_checked_by"
     * Dipakai kalau kamu mau tombol "Submit" setelah upload, tapi kalau tidak perlu
     * kamu bisa langsung set pending_checked_by setelah upload.
     */
    public function submitToCheck(ProcedureRevision $revision)
    {
        $this->guardRevisionEditable($revision);

        if ($revision->status !== ProcedureRevision::STATUS_PENDING) {
            return back()->with('error', 'Status tidak valid untuk submit ke checker.');
        }

        $revision->update([
            'status' => ProcedureRevision::STATUS_PENDING_CHECKED_BY,
            'reject_reason' => null,
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        return back()->with('success', 'Revision masuk tahap Pending Checked By.');
    }

    /**
     * Checked By sign -> status jadi pending_approved_by
     */
    public function checkedBy(Request $request, ProcedureRevision $revision)
    {
        $request->validate([
            'note' => ['nullable', 'string'],
        ]);

        $this->guardRevisionEditable($revision);

        if (!in_array($revision->status, [
            ProcedureRevision::STATUS_PENDING,
            ProcedureRevision::STATUS_PENDING_CHECKED_BY,
        ], true)) {
            return back()->with('error', 'Status tidak valid untuk Checked By.');
        }

        return DB::transaction(function () use ($revision, $request) {
            $this->sign($revision, ProcedureSignature::ROLE_CHECKED_BY, $request->note);

            $revision->update([
                'status' => ProcedureRevision::STATUS_PENDING_APPROVED_BY,
                'reject_reason' => null,
                'rejected_by' => null,
                'rejected_at' => null,
            ]);

            // optional: set current revision pointer ke revision ini
            $revision->procedure()->update(['current_revision_id' => $revision->id]);

            return back()->with('success', 'Checked By berhasil. Status menjadi Pending Approved By.');
        });
    }

    /**
     * Approved By sign -> status jadi approved
     */
    public function approvedBy(Request $request, ProcedureRevision $revision)
    {
        $request->validate([
            'note' => ['nullable', 'string'],
        ]);

        $this->guardRevisionEditable($revision);

        if ($revision->status !== ProcedureRevision::STATUS_PENDING_APPROVED_BY) {
            return back()->with('error', 'Status tidak valid untuk Approved By.');
        }

        return DB::transaction(function () use ($revision, $request) {
            $this->sign($revision, ProcedureSignature::ROLE_APPROVED_BY, $request->note);

            $revision->update([
                'status' => ProcedureRevision::STATUS_APPROVED,
                'reject_reason' => null,
                'rejected_by' => null,
                'rejected_at' => null,
            ]);

            // set current revision ke approved revision
            $revision->procedure()->update(['current_revision_id' => $revision->id]);

            return back()->with('success', 'Approved By berhasil. Status revision: APPROVED.');
        });
    }

    /**
     * Reject revision (wajib alasan)
     */
    public function reject(Request $request, ProcedureRevision $revision)
    {
        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'min:5'],
        ]);

        $this->guardRevisionEditable($revision);

        // boleh reject di tahap mana pun sebelum approved
        if ($revision->status === ProcedureRevision::STATUS_APPROVED) {
            return back()->with('error', 'Revision sudah approved, tidak bisa di-reject.');
        }

        $revision->update([
            'status' => ProcedureRevision::STATUS_REJECTED,
            'reject_reason' => $validated['reject_reason'],
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Revision berhasil di-reject.');
    }

    /* =========================
     * Helpers
     * ========================= */

    private function ensureSignatureRows(ProcedureRevision $revision): void
    {
        $roles = [
            ProcedureSignature::ROLE_PREPARED_BY,
            ProcedureSignature::ROLE_CHECKED_BY,
            ProcedureSignature::ROLE_APPROVED_BY,
        ];

        foreach ($roles as $role) {
            ProcedureSignature::firstOrCreate(
                ['procedure_revision_id' => $revision->id, 'role' => $role],
                ['user_id' => null, 'signed_at' => null, 'note' => null]
            );
        }

        // prepared_by otomatis diisi creator saat upload (optional, saya isi otomatis biar jelas)
        $this->sign($revision, ProcedureSignature::ROLE_PREPARED_BY, 'Auto: upload dokumen');
    }

    private function sign(ProcedureRevision $revision, string $role, ?string $note = null): void
    {
        $sig = ProcedureSignature::where('procedure_revision_id', $revision->id)
            ->where('role', $role)
            ->firstOrFail();

        $sig->update([
            'user_id' => Auth::id(),
            'signed_at' => now(),
            'note' => $note,
        ]);
    }

    private function guardRevisionEditable(ProcedureRevision $revision): void
    {
        // Rule umum: kalau approved sudah final, tidak boleh ubah status (kecuali kamu mau allow superseded)
        // Untuk saat ini: approved tidak boleh diubah.
        // Reject juga final.
        if (in_array($revision->status, [ProcedureRevision::STATUS_APPROVED, ProcedureRevision::STATUS_REJECTED], true)) {
            abort(403, 'Revision sudah final (approved/rejected).');
        }
    }

    private function renderStatusBadge(string $status): string
    {
        $status = strtolower($status);

        $map = [
            'pending' => ['warning', 'PENDING'],
            'pending_checked_by' => ['info', 'PENDING CHECKED BY'],
            'pending_approved_by' => ['primary', 'PENDING APPROVAL BY'],
            'approved' => ['success', 'APPROVED'],
            'rejected' => ['danger', 'REJECTED'],
        ];

        [$cls, $label] = $map[$status] ?? ['secondary', strtoupper($status ?: '-')];

        return '<span class="badge badge-' . $cls . '">' . $label . '</span>';
    }

    private function revText(int $revNo): string
    {
        return 'REV ' . str_pad((string)$revNo, 2, '0', STR_PAD_LEFT);
    }
}
