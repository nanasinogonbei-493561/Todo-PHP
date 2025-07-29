<?php

namespace App\Http\Controllers;

use App\Models\ShoppingItem;
use Illuminate\Http\Request;

class ShoppingItemController extends Controller
{
    public function index()
    {
        $items = ShoppingItem::all();
        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ShoppingItem::create($request->only('name'));

        return redirect()->route('items.index');
    }

    public function edit($id)
    {
        $item = ShoppingItem::findOrFail($id);
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $item = ShoppingItem::findOrFail($id);
        $item->update($request->only('name'));

        return redirect()->route('items.index');
    }

    public function destroy($id)
    {
        $item = ShoppingItem::findOrFail($id);
        $item->delete();

        return redirect()->route('items.index');
    }
}