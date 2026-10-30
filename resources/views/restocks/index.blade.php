@extends('layouts.app')

@section('title', 'Restock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Riwayat Restock</h4>
    <a href="{{ route('restocks.create') }}" class="btn btn-primary">+ Restock Baru</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Dicatat oleh</th>
                    <th class="text-end">Item</th>
                    <th class="text-end">Total</th>
                    <th style="width: 90px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($restocks as $restock)
                    <tr>
                        <td>{{ $restock->id }}</td>
                        <td>{{ $restock->restocked_at->format('d M Y H:i') }}</td>
                        <td>{{ $restock->supplier->name }}</td>
                        <td>{{ $restock->user->name }}</td>
                        <td class="text-end">{{ $restock->items_count }}</td>
                        <td class="text-end">Rp {{ number_format($restock->total, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('restocks.show', $restock) }}" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada restock.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $restocks->links('pagination::bootstrap-5') }}
</div>
@endsection