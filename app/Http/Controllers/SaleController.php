<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::with('user')
            ->withCount('items')
            ->latest('sold_at')
            ->latest('id')
            ->paginate(10);

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $products = Product::where('stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'price', 'stock']);

        return view('sales.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items'              => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1', 'max:1000'],
        ], [
            'items.required'              => 'Tambahkan minimal satu item penjualan.',
            'items.*.product_id.required' => 'Pilih produk untuk setiap baris.',
            'items.*.product_id.distinct' => 'Produk yang sama tidak boleh dipilih dua kali.',
            'items.*.product_id.exists'   => 'Produk yang dipilih tidak valid.',
            'items.*.quantity.min'        => 'Jumlah minimal 1.',
        ]);

        $sale = DB::transaction(function () use ($data, $request) {
            // Kunci baris produk yang terlibat sampai transaksi selesai.
            $ids = collect($data['items'])->pluck('product_id');
            $products = Product::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');

            // Cek stok dan hitung total memakai HARGA DARI DATABASE.
            $total = 0;
            foreach ($data['items'] as $index => $item) {
                $product = $products[$item['product_id']];

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Stok {$product->name} tidak cukup (tersisa {$product->stock}).",
                    ]);
                }

                $total += $product->price * $item['quantity'];
            }

            $sale = Sale::create([
                'user_id'    => $request->user()->id,
                'invoice_no' => $this->nextInvoiceNumber(),
                'total'      => $total,
                'sold_at'    => now(),
            ]);

            foreach ($data['items'] as $item) {
                $product = $products[$item['product_id']];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price, // snapshot harga saat transaksi
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', "Penjualan {$sale->invoice_no} berhasil disimpan.");
    }

    public function show(Sale $sale): View
    {
        $sale->load('user', 'items.product');

        return view('sales.show', compact('sale'));
    }

    /**
     * Format: INV-YYYYMMDD-0001 (urutan di-reset tiap hari).
     * Dipanggil di dalam transaksi; kolom invoice_no juga UNIQUE sebagai pengaman.
     */
    private function nextInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';

        $last = Sale::where('invoice_no', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('invoice_no')
            ->value('invoice_no');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}