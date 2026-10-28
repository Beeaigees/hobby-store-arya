@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Daftar Produk</h4>

    @if (auth()->user()->isOwner())
        <a href="{{ route('products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
    @endif
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px">Foto</th>
                    <th>SKU</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Stok</th>
                    @if (auth()->user()->isOwner())
                        <th style="width: 160px">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            @if ($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}"
                                     alt="{{ $product->name }}"
                                     class="rounded" style="width: 56px; height: 56px; object-fit: cover">
                            @else
                                <div class="bg-secondary-subtle text-muted rounded d-flex align-items-center justify-content-center small"
                                     style="width: 56px; height: 56px">N/A</div>
                            @endif
                        </td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td class="text-end">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="text-end">{{ $product->stock }}</td>

                        @if (auth()->user()->isOwner())
                            <td>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
@endsection