<?php

namespace App\Http\Controllers;

use App\Models\Daily;
use App\Models\DailyComment;
use App\Models\TimelineTahunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DailyController extends Controller
{
    /**
     * Display the index page.
     */
    public function index()
    {
        return view('daily.index');
    }

    /**
     * Get list of daily records (for API or AJAX).
     */
    public function getList(Request $request)
    {
        $tanggal = $request->get('tanggal');

        $query = Daily::with(['user'])
            ->withCount('comments') // tambahkan ini
            ->orderBy('tanggal', 'desc');

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        } else {
            $query->whereDate('tanggal', now());
        }

        return response()->json([
            'data' => $query->get(),
            'auth_user_id' => auth()->id(),
        ]);
    }
    public function dataDailyComments(Daily $daily)
    {
        return $daily->comments()->with('user')->latest()->get();
    }

    public function storeDailyComments(Request $request, $daily)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        DailyComment::create([
            'daily_id' => $daily,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroyDailyComments(DailyComment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Komentar berhasil dihapus']);
    }


    /**
     * Store a newly created daily record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'plan_today' => 'required',
            'plan_tomorrow' => 'nullable',
            'problem' => 'nullable',
            'upload_file' => 'nullable|file|max:2048', // max 2MB
        ]);

        $data = $request->only(['user_id', 'tanggal', 'plan_today', 'plan_tomorrow', 'problem']);

        if ($request->hasFile('upload_file')) {
            $data['upload_file'] = $request->file('upload_file')->store('uploads/daily', 'public');
        }

        $daily = Daily::create($data);

        return response()->json([
            'message' => 'Daily created successfully',
            'data' => $daily
        ], 201);
    }

    /**
     * Show the form for editing the specified daily record.
     */
    public function edit($id)
    {
        $daily = Daily::findOrFail($id);
        return response()->json($daily);
    }

    /**
     * Update the specified daily record.
     */
    public function update(Request $request, $id)
    {
        $daily = Daily::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'plan_today' => 'required',
            'plan_tomorrow' => 'nullable',
            'problem' => 'nullable',
            'upload_file' => 'nullable|file|max:2048',
        ]);

        $data = $request->only(['tanggal', 'plan_today', 'plan_tomorrow', 'problem']);

        if ($request->hasFile('upload_file')) {
            // Delete old file
            if ($daily->upload_file && Storage::disk('public')->exists($daily->upload_file)) {
                Storage::disk('public')->delete($daily->upload_file);
            }
            $data['upload_file'] = $request->file('upload_file')->store('uploads/daily', 'public');
        }

        $daily->update($data);

        return response()->json([
            'message' => 'Daily updated successfully',
            'data' => $daily
        ]);
    }

    /**
     * Delete the specified daily record.
     */
    public function destroy($id)
    {
        $daily = Daily::findOrFail($id);

        // Delete file if exists
        if ($daily->upload_file && Storage::disk('public')->exists($daily->upload_file)) {
            Storage::disk('public')->delete($daily->upload_file);
        }

        $daily->delete();

        return response()->json(['message' => 'Daily deleted successfully']);
    }

    /**
     * Ambil semua data timeline (opsional filter tahun)
     */
    public function getDataTimeline(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        $timeline = TimelineTahunan::where('tahun', $tahun)
            ->orderBy('start_date', 'asc')
            ->get();

        return response()->json($timeline);
    }

    /**
     * Tambah data timeline baru
     */
    public function tambahListTimeline(Request $request)
    {
        $validated = $request->validate([
            'tahun'       => 'required|integer',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'required|string',
            'is_action'   => 'nullable|boolean'
        ]);

        $timeline = TimelineTahunan::create($validated);

        return response()->json([
            'message' => 'Data timeline berhasil ditambahkan',
            'data'    => $timeline
        ]);
    }

    /**
     * Update data timeline berdasarkan ID
     */
    public function updateListTimeline(Request $request, $id)
    {
        $timeline = TimelineTahunan::findOrFail($id);

        $validated = $request->validate([
            'tahun'       => 'required|integer',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'required|string',
            'is_action'   => 'nullable|boolean'
        ]);

        $timeline->update($validated);

        return response()->json([
            'message' => 'Data timeline berhasil diperbarui',
            'data'    => $timeline
        ]);
    }

    /**
     * Hapus data timeline berdasarkan ID
     */
    public function deleteListTimeline($id)
    {
        $timeline = TimelineTahunan::findOrFail($id);
        $timeline->delete();

        return response()->json([
            'message' => 'Data timeline berhasil dihapus'
        ]);
    }
}
