<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalItems = Item::sum('total_stock');
        $availableStock = Item::sum('available_stock');
        $unavailableStock = Item::sum('unavailable_stock');
        $mostBorrowed = Item::sum('most_borrowed');

        $items = Item::all();
        $recentBorrowings = Borrowing::with('item')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalItems',
            'availableStock',
            'unavailableStock',
            'mostBorrowed',
            'items',
            'recentBorrowings'
        ));
    }

    public function items()
    {
        $items = Item::all();
        return view('admin.items', compact('items'));
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0'
        ]);

        Item::create([
            'name' => $request->name,
            'stock' => $request->stock,
            'total_stock' => $request->stock,
            'available_stock' => $request->stock,
            'unavailable_stock' => 0,
            'most_borrowed' => 0
        ]);

        return back()->with('success', 'Barang berhasil ditambahkan!');
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $item = Item::findOrFail($id);
        $addedStock = $request->stock;

        $item->stock += $addedStock;
        $item->total_stock += $addedStock;
        $item->available_stock += $addedStock;
        $item->save();

        return back()->with('success', 'Stok berhasil ditambahkan!');
    }

    public function borrowings()
    {
        $borrowings = Borrowing::with('item')->orderBy('created_at', 'desc')->get();
        return view('admin.borrowings', compact('borrowings'));
    }

    public function returnItem($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        
        if ($borrowing->status === 'dikembalikan') {
            return back()->with('error', 'Barang sudah dikembalikan!');
        }

        DB::beginTransaction();
        try {
            $borrowing->status = 'dikembalikan';
            $borrowing->returned_at = now();
            $borrowing->save();

            $item = $borrowing->item;
            $item->available_stock += $borrowing->quantity;
            $item->unavailable_stock -= $borrowing->quantity;
            $item->save();

            DB::commit();
            return back()->with('success', 'Barang berhasil dikembalikan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}