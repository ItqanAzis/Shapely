<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // 1. GET: Fetch all items (for the User Grid)
    public function index()
    {
        // Return items sorted by newest first
        return Item::orderBy('created_at', 'desc')->get();
    }

    // 2. POST: Create a new item (for the Admin Portal)
    public function store(Request $request)
    {
        // VALIDATION 
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shape' => 'required|string',
            'color' => 'required|string',
        ]);

        // Create the item
        $item = Item::create($validated);

        return response()->json($item, 201);
    }

    // 3. PUT: Update an item 
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->update($request->all());
        return response()->json($item, 200);
    }

    // 4. DELETE: Remove an item
    public function destroy($id)
    {
        Item::destroy($id);
        return response()->json(null, 204);
    }
}