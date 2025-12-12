<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentPurchaseController extends Controller
{
    public function index()
    {
        $books = \App\Models\Book::where('stock', '>', 0)->get();
        $uniforms = \App\Models\Uniform::where('stock', '>', 0)->get();
        return view('student.purchase.index', compact('books', 'uniforms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.type' => 'required|in:book,uniform',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($request->items as $itemData) {
                if ($itemData['type'] == 'book') {
                    $item = \App\Models\Book::find($itemData['id']);
                } else {
                    $item = \App\Models\Uniform::find($itemData['id']);
                }

                if (!$item || $item->stock < $itemData['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk " . ($item->title ?? $item->name));
                }

                $price = $item->price;
                $quantity = $itemData['quantity'];
                $totalAmount += $price * $quantity;

                // Decrement stock
                $item->decrement('stock', $quantity);

                $orderItems[] = [
                    'item' => $item,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }

            $order = \App\Models\Order::create([
                'user_id' => auth()->id(),
                'invoice_number' => 'INV-' . time() . '-' . auth()->id(),
                'total_amount' => $totalAmount,
                'status' => 'completed', // Assuming immediate success for now
            ]);

            foreach ($orderItems as $orderItem) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => get_class($orderItem['item']),
                    'item_id' => $orderItem['item']->id,
                    'quantity' => $orderItem['quantity'],
                    'price' => $orderItem['price'],
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('my-student.index')->with('success', 'Pembelian berhasil!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
