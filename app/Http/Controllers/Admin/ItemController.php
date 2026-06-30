<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\StorageLocation;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['category', 'storageLocation'])->latest()->paginate(10);

        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $storageLocations = StorageLocation::where('is_active', true)->get();

        return view('admin.items.create', compact('categories', 'storageLocations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'condition' => 'required|in:baik,rosak,service',
            'status' => 'required|in:tersedia,dipinjam,disimpan,rosak',
            'category_id' => 'required|exists:categories,id',
            'storage_location_id' => 'required|exists:storage_locations,id',
            'expiry_date' => 'nullable|date',
            'image' => 'nullable|string|max:255',
        ]);

        $validated['available_quantity'] = $validated['quantity'];

        Item::create($validated);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berjaya ditambah.');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'storageLocation', 'itemConditions.changedBy']);

        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $storageLocations = StorageLocation::where('is_active', true)->get();

        return view('admin.items.edit', compact('item', 'categories', 'storageLocations'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'condition' => 'required|in:baik,rosak,service',
            'status' => 'required|in:tersedia,dipinjam,disimpan,rosak',
            'category_id' => 'required|exists:categories,id',
            'storage_location_id' => 'required|exists:storage_locations,id',
            'expiry_date' => 'nullable|date',
            'image' => 'nullable|string|max:255',
        ]);

        $item->update($validated);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berjaya dikemaskini.');
    }

    public function destroy(Item $item)
    {
        if ($item->loanItems()->count() > 0) {
            return back()->with('error', 'Barang tidak boleh dipadam kerana masih mempunyai rekod pinjaman.');
        }

        $item->delete();

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berjaya dipadam.');
    }
}
