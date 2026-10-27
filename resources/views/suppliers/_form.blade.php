<div class="mb-3">
    <label for="name" class="form-label">Nama Supplier</label>
    <input type="text" name="name" id="name"
           value="{{ old('name', $supplier->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror" required autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="phone" class="form-label">Telepon</label>
    <input type="text" name="phone" id="phone"
           value="{{ old('phone', $supplier->phone ?? '') }}"
           class="form-control @error('phone') is-invalid @enderror">
    @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="address" class="form-label">Alamat</label>
    <textarea name="address" id="address" rows="3"
              class="form-control @error('address') is-invalid @enderror">{{ old('address', $supplier->address ?? '') }}</textarea>
    @error('address')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>