<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $items = Item::where('available_stock', '>', 0)->get();
        return view('user.index', compact('items'));
    }

    public function cart()
    {
        return view('user.cart');
    }

    public function borrowItems(Request $request)
    {
        $request->validate([
            'borrower_name' => 'required|string|max:255',
            'function' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                
                // Check stock availability
                if ($item->available_stock < $itemData['quantity']) {
                    DB::rollBack();
                    return redirect()->route('user.cart')->with('error', "Stok tidak cukup untuk {$item->name}. Tersedia: {$item->available_stock}");
                }

                // Create borrowing record
                Borrowing::create([
                    'borrower_name' => $request->borrower_name,
                    'function' => $request->function,
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'status' => 'dipinjam',
                    'borrowed_at' => now()
                ]);

                // Update stock
                $item->available_stock -= $itemData['quantity'];
                $item->unavailable_stock += $itemData['quantity'];
                $item->most_borrowed += $itemData['quantity'];
                $item->save();
            }

            DB::commit();
            return redirect()->route('user.index')->with('success', 'Barang berhasil dipinjam!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.cart')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}