<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:sale,refund',
            'transaction_date' => 'nullable|date'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;
        $totalPrice = $product->price * $quantity;
        $transactionDate = $request->transaction_date ?? now();

        // Adjust stock based on transaction type
        if ($request->type === 'sale') {
            if ($product->stock < $quantity) {
                return response()->json(['message' => 'Insufficient stock'], 400);
            }
            $product->decrement('stock', $quantity);
        } elseif ($request->type === 'refund') {
            $product->increment('stock', $quantity);
        }

        // Create the transaction
        $transaction = Transaction::create([
            'id' => Str::uuid(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'total_price' => $totalPrice,
            'type' => $request->type,
            'transaction_date' => $transactionDate
        ]);

        return response()->json(['message' => 'Transaction processed successfully', 'transaction' => $transaction], 201);
    }

    public function report(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $transactions = Transaction::whereBetween('transaction_date', [
            $request->start_date,
            $request->end_date
        ])->get();

        return response()->json([
            'message' => 'Transaction report generated successfully',
            'transactions' => $transactions
        ]);
    }

}
