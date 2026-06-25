<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorageLocation;
use Illuminate\Http\Request;

class StorageLocationController extends Controller
{
    public function index()
    {
        return view('admin.storage-locations.index');
    }

    public function create()
    {
        return view('admin.storage-locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:storage_locations',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        StorageLocation::create($validated);

        return redirect()->route('admin.storage-locations.index')
            ->with('success', 'Lokasi penyimpanan berjaya ditambah.');
    }

    public function edit(StorageLocation $storageLocation)
    {
        return view('admin.storage-locations.edit', compact('storageLocation'));
    }

    public function update(Request $request, StorageLocation $storageLocation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:storage_locations,code,'.$storageLocation->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $storageLocation->update($validated);

        return redirect()->route('admin.storage-locations.index')
            ->with('success', 'Lokasi penyimpanan berjaya dikemaskini.');
    }

    public function destroy(StorageLocation $storageLocation)
    {
        if ($storageLocation->items()->count() > 0) {
            return back()->with('error', 'Lokasi tidak boleh dipadam kerana masih mempunyai barang.');
        }

        $storageLocation->delete();

        return redirect()->route('admin.storage-locations.index')
            ->with('success', 'Lokasi penyimpanan berjaya dipadam.');
    }
}
