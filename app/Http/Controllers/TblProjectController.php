<?php
namespace App\Http\Controllers;

use App\Models\ProjectTbl;
use App\Models\User;
use App\Models\Kerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TblProjectController extends Controller
{
    public function index()
    {
        $projects = ProjectTbl::with(['client', 'kerjaan', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
        $listclient = User::where('role_id', 2)->get();
        $listkerjaan = Kerjaan::select('id', 'nama_kerjaan')->get();
        return view('projects.index', compact('projects', 'listclient', 'listkerjaan'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nama_project' => 'required|string|max:100',
        'no_project' => 'required|string|unique:projects',
        'client_id' => 'required|exists:users,id',
        'kerjaan_id' => 'required|exists:kerjaans,id',
        'deskripsi' => 'nullable|string',
        'start' => 'nullable|date',
        'end' => 'nullable|date|after_or_equal:start'
    ]);

    $validated['created_by'] = Auth::id();

    ProjectTbl::create($validated);

    if ($request->ajax()) {
        return response()->json(['success' => true]);
    }

    return redirect()->route('projects.tampilan')
        ->with('success', 'Project berhasil ditambahkan');
}

    public function show(ProjectTbl $project)
    {
        return view('projects.show', compact('project'));
 
    }

    public function update(Request $request, ProjectTbl $project)
    {
        $validated = $request->validate([
            'nama_project' => 'required|string|max:100',
            'no_project' => 'required|string|unique:projects,no_project,' . $project->id,
            'client_id' => 'required|exists:users,id',
            'kerjaan_id' => 'required|exists:kerjaans,id',
            'deskripsi' => 'nullable|string',
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start'
        ]);

        $project->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('projects.tampilan')
            ->with('success', 'Project berhasil diperbarui');
    }

    public function destroy(ProjectTbl $project)
    {
        $project->delete();

        return redirect()->route('projects.tampilan')
            ->with('success', 'Project berhasil dihapus');
    }
}
