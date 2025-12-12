<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = \App\Models\Order::with('user')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $students = \App\Models\User::all(); // Should actully filter by role student if possible
        $books = \App\Models\Book::where('stock', '>', 0)->get();
        $uniforms = \App\Models\Uniform::where('stock', '>', 0)->get();
        return view('admin.orders.create', compact('students', 'books', 'uniforms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
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

                if (!$item) {
                     throw new \Exception("Item tidak ditemukan.");
                }

                // Check stock just in case
                if ($item->stock < $itemData['quantity']) {
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
                'user_id' => $request->user_id,
                'invoice_number' => 'INV-' . time() . '-' . $request->user_id . '-' . rand(100,999),
                'total_amount' => $totalAmount,
                'status' => 'completed',
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

            return redirect()->route('orders.index')->with('success', 'Pembelian berhasil dicatat!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(\App\Models\Order $order)
    {
        $order->load(['user', 'items.item']);
        return view('admin.orders.show', compact('order'));
    }

    public function printInvoice(\App\Models\Order $order)
    {
        $order->load(['user', 'items.item']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.invoice_pdf', compact('order'));
        return $pdf->stream('Invoice-' . $order->invoice_number . '.pdf');
    }

    // Optional: Destroy to rollback stock if needed
    public function destroy(\App\Models\Order $order)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach($order->items as $orderItem) {
                // Restore stock
                if ($orderItem->item) {
                     $orderItem->item->increment('stock', $orderItem->quantity);
                }
            }
            $order->delete();
            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order dihapus dan stok dikembalikan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal membatalkan order: ' . $e->getMessage());
        }
    }
}
