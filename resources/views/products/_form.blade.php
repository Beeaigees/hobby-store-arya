<div class="mb-3">
    <label for="category_id" class="form-label">Kategori</label>
    <select name="category_id" id="category_id"
            class="form-select @error('category_id') is-invalid @enderror" required>
        <option value="">-- Pilih kategori --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" name="sku" id="sku"
               value="{{ old('sku', $product->sku ?? '') }}"
               class="form-control @error('sku') is-invalid @enderror" required>
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input type="text" name="name" id="name"
               value="{{ old('name', $product->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Deskripsi</label>
    <textarea name="description" id="description" rows="3"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="price" class="form-label">Harga Jual (Rp)</label>
        <input type="number" name="price" id="price" min="0" step="1"
               value="{{ old('price', $product->price ?? '') }}"
               class="form-control @error('price') is-invalid @enderror" required>
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        @isset($product)
            <label for="stock" class="form-label">Stok Saat Ini</label>
            <input type="number" id="stock" value="{{ $product->stock }}" class="form-control" disabled>
            <div class="form-text">Stok berubah otomatis lewat penjualan dan restock.</div>
        @else
            <label for="stock" class="form-label">Stok Awal</label>
            <input type="number" name="stock" id="stock" min="0" step="1"
                   value="{{ old('stock', 0) }}"
                   class="form-control @error('stock') is-invalid @enderror" required>
            @error('stock')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endisset
    </div>
</div>

<div class="mb-3">
    <label for="image" class="form-label">Foto Produk</label>

    @isset($product)
        @if ($product->image_path)
            <div class="mb-2">
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}"
                     class="img-thumbnail" style="max-height: 140px">
            </div>
        @endif
    @endisset

    <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.webp"
           class="form-control @error('image') is-invalid @enderror">
    <div class="form-text">Format JPG, PNG, atau WEBP. Maksimal 2 MB.@isset($product) Kosongkan jika tidak ingin mengganti foto.@endisset</div>
    @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>