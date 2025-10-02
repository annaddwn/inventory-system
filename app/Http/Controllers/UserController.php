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
        // Check if user info is in session
        if (!session()->has('borrower_name') || !session()->has('borrower_function')) {
            return redirect()->route('user.info');
        }

        $items = Item::where('available_stock', '>', 0)->get();
        return view('user.index', compact('items'));
    }

    public function showInfo()
    {
        return view('user.info');
    }

    public function storeInfo(Request $request)
    {
        $request->validate([
            'borrower_name' => 'required|string|max:255',
            'function' => 'required|string|max:255'
        ]);

        // Store in session
        session([
            'borrower_name' => $request->borrower_name,
            'borrower_function' => $request->function
        ]);

        return redirect()->route('user.index');
    }

    public function cart()
    {
        // Check if user info is in session
        if (!session()->has('borrower_name') || !session()->has('borrower_function')) {
            return redirect()->route('user.info');
        }

        return view('user.cart');
    }

    public function borrowItems(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        // Get borrower info from session
        $borrowerName = session('borrower_name');
        $borrowerFunction = session('borrower_function');

        if (!$borrowerName || !$borrowerFunction) {
            return redirect()->route('user.info')->with('error', 'Silakan isi data diri Anda terlebih dahulu');
        }

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
                    'borrower_name' => $borrowerName,
                    'function' => $borrowerFunction,
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'status' => 'requested',
                    'borrowed_at' => now()
                ]);

                // Update stock
                $item->available_stock -= $itemData['quantity'];
                $item->unavailable_stock += $itemData['quantity'];
                $item->most_borrowed += $itemData['quantity'];
                $item->save();
            }

            DB::commit();
            
            // Clear session after successful borrowing
            session()->forget(['borrower_name', 'borrower_function']);
            
            return redirect()->route('user.info')->with('success', 'Berhasil request barang');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.cart')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetSession()
    {
        session()->forget(['borrower_name', 'borrower_function']);
        return redirect()->route('user.info');
    }
}