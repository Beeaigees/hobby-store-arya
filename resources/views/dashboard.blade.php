@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h4 class="mb-1">Dashboard Keuangan</h4>
<p class="text-muted">Selamat datang, {{ auth()->user()->name }}.</p>

{{-- Ringkasan bulan berjalan --}}
<h6 class="text-uppercase text-muted mb-2">Bulan ini ({{ $monthLabel }})</h6>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Pemasukan (penjualan)</div>
                <div class="fs-4 fw-bold text-success">
                    Rp {{ number_format($summary['month']['income'], 0, ',', '.') }}
                </div>
                <div class="small text-muted">{{ $summary['month']['sales_count'] }} transaksi</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Pengeluaran (restock)</div>
                <div class="fs-4 fw-bold text-danger">
                    Rp {{ number_format($summary['month']['expense'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        @php $monthNet = $summary['month']['income'] - $summary['month']['expense']; @endphp
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Selisih kas</div>
                <div class="fs-4 fw-bold {{ $monthNet >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $monthNet < 0 ? '-' : '' }}Rp {{ number_format(abs($monthNet), 0, ',', '.') }}
                </div>
                <div class="small text-muted">Pemasukan dikurangi pengeluaran</div>
            </div>
        </div>
    </div>
</div>

{{-- Ringkasan semua waktu --}}
<h6 class="text-uppercase text-muted mb-2">Semua waktu</h6>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total pemasukan</div>
                <div class="fs-5 fw-bold">Rp {{ number_format($summary['all']['income'], 0, ',', '.') }}</div>
                <div class="small text-muted">{{ $summary['all']['sales_count'] }} transaksi</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total pengeluaran</div>
                <div class="fs-5 fw-bold">Rp {{ number_format($summary['all']['expense'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        @php $allNet = $summary['all']['income'] - $summary['all']['expense']; @endphp
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Selisih kas</div>
                <div class="fs-5 fw-bold {{ $allNet >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $allNet < 0 ? '-' : '' }}Rp {{ number_format(abs($allNet), 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Stok menipis --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Stok menipis (≤ {{ $lowStockLimit }})</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Produk</th><th>Kategori</th><th class="text-end">Stok</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->sku }} - {{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td class="text-end">
                                    <span class="badge {{ $product->stock === 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Semua stok aman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Produk terlaris --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Produk terlaris bulan ini</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Produk</th><th class="text-end">Terjual</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($topProducts as $row)
                            <tr>
                                <td>{{ $row->product->sku }} - {{ $row->product->name }}</td>
                                <td class="text-end">{{ $row->total_qty }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">Belum ada penjualan bulan ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Penjualan terbaru --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between">
                <span class="fw-semibold">Penjualan terbaru</span>
                <a href="{{ route('sales.index') }}" class="small">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Invoice</th><th>Kasir</th><th class="text-end">Total</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($latestSales as $sale)
                            <tr>
                                <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_no }}</a></td>
                                <td>{{ $sale->user->name }}</td>
                                <td class="text-end">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada penjualan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Restock terbaru --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between">
                <span class="fw-semibold">Restock terbaru</span>
                <a href="{{ route('restocks.index') }}" class="small">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Tanggal</th><th>Supplier</th><th class="text-end">Total</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($latestRestocks as $restock)
                            <tr>
                                <td><a href="{{ route('restocks.show', $restock) }}">{{ $restock->restocked_at->format('d M Y') }}</a></td>
                                <td>{{ $restock->supplier->name }}</td>
                                <td class="text-end">Rp {{ number_format($restock->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada restock.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection