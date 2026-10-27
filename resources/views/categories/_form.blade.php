<div class="mb-3">
    <label for="name" class="form-label">Nama Kategori</label>
    <input type="text" name="name" id="name"
           value="{{ old('name', $category->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror" required autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>