@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Riwayat Penjualan</h4>
    <a href="{{ route('sales.create') }}" class="btn btn-primary">+ Penjualan Baru</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th class="text-end">Item</th>
                    <th class="text-end">Total</th>
                    <th style="width: 90px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->sold_at->format('d M Y H:i') }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td class="text-end">{{ $sale->items_count }}</td>
                        <td class="text-end">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $sales->links('pagination::bootstrap-5') }}
</div>
@endsection