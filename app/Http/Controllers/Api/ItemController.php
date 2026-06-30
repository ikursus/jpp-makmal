<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::query()
            ->with('category')
            ->where('is_active', true)
            ->where('status', 'tersedia')
            ->where('available_quantity', '>', 0)
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->latest()
            ->paginate(15);

        return ItemResource::collection($items);
    }

    public function show(Item $item)
    {
        return new ItemResource($item->load('category'));
    }
}
