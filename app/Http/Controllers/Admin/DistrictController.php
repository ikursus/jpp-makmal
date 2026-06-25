<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index()
    {
        return view('admin.districts.index');
    }

    public function create()
    {
        return view('admin.districts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:districts',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        District::create($validated);

        return redirect()->route('admin.districts.index')
            ->with('success', 'Daerah berjaya ditambah.');
    }

    public function show(District $district)
    {
        return view('admin.districts.show', compact('district'));
    }

    public function edit(District $district)
    {
        return view('admin.districts.edit', compact('district'));
    }

    public function update(Request $request, District $district)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:districts,code,' . $district->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $district->update($validated);

        return redirect()->route('admin.districts.index')
            ->with('success', 'Daerah berjaya dikemaskini.');
    }

    public function destroy(District $district)
    {
        if ($district->users()->count() > 0 || $district->loanApplications()->count() > 0) {
            return back()->with('error', 'Daerah tidak boleh dipadam kerana masih mempunyai rekod berkaitan.');
        }

        $district->delete();

        return redirect()->route('admin.districts.index')
            ->with('success', 'Daerah berjaya dipadam.');
    }
}
