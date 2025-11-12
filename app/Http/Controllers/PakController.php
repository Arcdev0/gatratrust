<?php

namespace App\Http\Controllers;

use App\Models\KaryawanData;
use Illuminate\Http\Request;
use App\Models\Pak;
use App\Models\PakItem;
use Illuminate\Support\Facades\DB;

class PakController extends Controller
{
    public function index()
    {
        $paks = Pak::with('items')->orderBy('created_at', 'desc')->get();
        return view('pak.index', compact('paks'));
    }

    public function create()
    {
        $employees = KaryawanData::orderBy('nama_lengkap', 'asc')->get();
        return view('pak.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_number' => 'required|string|max:100',
            'project_value' => 'required|numeric|min:0',
            'location_project' => 'required|string|max:255',
            'date' => 'required|date',
            'employee' => 'required|array|min:1',
            'employee.*' => 'exists:karyawan_data,id',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|string',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.total_cost' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Create PAK
            $pak = Pak::create([
                'project_name' => $validated['project_name'],
                'project_number' => $validated['project_number'],
                'project_value' => $validated['project_value'],
                'location_project' => $validated['location_project'],
                'date' => $validated['date'],
                'employee' => json_encode($validated['employee']),
            ]);

            // Create PAK Items
            foreach ($validated['items'] as $item) {
                PakItem::create([
                    'pak_id' => $pak->pak_id,
                    'category_id' => $item['category_id'],
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['total_cost'],
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('pak.index')
                ->with('success', 'PAK berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pak = Pak::with('items.category')->findOrFail($id);
        return view('pak.show', compact('pak'));
    }

    public function edit($id)
    {
        $pak = Pak::with('items')->findOrFail($id);
        return view('pak.edit', compact('pak'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_number' => 'required|string|max:100',
            'project_value' => 'required|numeric|min:0',
            'location_project' => 'required|string|max:255',
            'date' => 'required|date',
            'employee' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|string',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.total_cost' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $pak = Pak::findOrFail($id);

            // Update PAK
            $pak->update([
                'project_name' => $validated['project_name'],
                'project_number' => $validated['project_number'],
                'project_value' => $validated['project_value'],
                'location_project' => $validated['location_project'],
                'date' => $validated['date'],
                'employee' => $validated['employee'],
            ]);

            // Delete old items and create new ones
            $pak->items()->delete();

            foreach ($validated['items'] as $item) {
                PakItem::create([
                    'pak_id' => $pak->pak_id,
                    'category_id' => $item['category_id'],
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['total_cost'],
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('pak.index')
                ->with('success', 'PAK berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $pak = Pak::findOrFail($id);
            $pak->items()->delete();
            $pak->delete();

            return redirect()->route('pak.index')
                ->with('success', 'PAK berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}