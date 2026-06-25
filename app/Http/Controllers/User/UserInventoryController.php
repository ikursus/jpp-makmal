<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class UserInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'storageLocation'])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $items = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        return view('user.inventory', compact('items', 'categories'));
    }
}
