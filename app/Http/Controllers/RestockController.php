<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Restock;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RestockController extends Controller
{
    public function index(): View
    {
        $restocks = Restock::with(['supplier', 'user'])
            ->withCount('items')
            ->latest('restocked_at')
            ->latest('id')
            ->paginate(10);

        return view('restocks.index', compact('restocks'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'stock']);

        return view('restocks.create', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id'        => ['required', 'integer', 'exists:suppliers,id'],
            'items'              => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.cost'       => ['required', 'integer', 'min:1', 'max:1000000000'],
        ], [
            'supplier_id.required'        => 'Pilih supplier.',
            'items.required'              => 'Tambahkan minimal satu item restock.',
            'items.*.product_id.required' => 'Pilih produk untuk setiap baris.',
            'items.*.product_id.distinct' => 'Produk yang sama tidak boleh dipilih dua kali.',
            'items.*.quantity.min'        => 'Jumlah minimal 1.',
            'items.*.cost.min'            => 'Harga beli minimal 1.',
        ]);

        $restock = DB::transaction(function () use ($data, $request) {
            $total = collect($data['items'])
                ->sum(fn ($item) => $item['quantity'] * $item['cost']);

            $restock = Restock::create([
                'user_id'      => $request->user()->id,
                'supplier_id'  => $data['supplier_id'],
                'total'        => $total,
                'restocked_at' => now(),
            ]);

            foreach ($data['items'] as $item) {
                $restock->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'cost'       => $item['cost'],
                ]);

                Product::whereKey($item['product_id'])->increment('stock', $item['quantity']);
            }

            return $restock;
        });

        return redirect()->route('restocks.show', $restock)
            ->with('success', 'Restock berhasil dicatat dan stok diperbarui.');
    }

    public function show(Restock $restock): View
    {
        $restock->load('supplier', 'user', 'items.product');

        return view('restocks.show', compact('restock'));
    }
}