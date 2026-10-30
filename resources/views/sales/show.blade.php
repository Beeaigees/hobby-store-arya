@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-1">{{ $sale->invoice_no }}</h4>
                <div class="text-muted">
                    {{ $sale->sold_at->format('d M Y H:i') }} &middot; Kasir: {{ $sale->user->name }}
                </div>
            </div>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $item->product->sku }} - {{ $item->product->name }}</td>
                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end">Rp {{ number_format($sale->total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection