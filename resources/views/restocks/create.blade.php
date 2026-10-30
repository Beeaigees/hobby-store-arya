@extends('layouts.app')

@section('title', 'Restock Baru')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="mb-3">Restock Baru</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('restocks.store') }}" method="POST">
            @csrf

            <div class="mb-3 col-md-5 px-0">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-select" required>
                    <option value="">-- Pilih supplier --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th style="width: 120px">Qty</th>
                            <th style="width: 190px">Harga Beli / unit (Rp)</th>
                            <th class="text-end" style="width: 170px">Subtotal</th>
                            <th style="width: 60px"></th>
                        </tr>
                    </thead>
                    <tbody id="item-rows"></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end" id="grand-total">Rp 0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button type="button" id="add-row" class="btn btn-outline-secondary btn-sm mb-3">+ Tambah Item</button>

            <div>
                <button type="submit" class="btn btn-primary">Simpan Restock</button>
                <a href="{{ route('restocks.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    const products = {{ Illuminate\Support\Js::from($products) }};
    const oldItems = {{ Illuminate\Support\Js::from(old('items', [['product_id' => '', 'quantity' => 1, 'cost' => '']])) }};

    const tbody = document.getElementById('item-rows');
    const grandTotal = document.getElementById('grand-total');
    const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');
    let rowIndex = 0;

    function recalc() {
        let total = 0;

        tbody.querySelectorAll('tr').forEach(tr => {
            const qty = parseInt(tr.querySelector('.input-qty').value, 10) || 0;
            const cost = parseInt(tr.querySelector('.input-cost').value, 10) || 0;
            const subtotal = qty * cost;

            tr.querySelector('.cell-subtotal').textContent = rupiah(subtotal);
            total += subtotal;
        });

        grandTotal.textContent = rupiah(total);
    }

    function addRow(item = {}) {
        const i = rowIndex++;
        const tr = document.createElement('tr');

        // Kolom produk
        const tdProduct = document.createElement('td');
        const select = document.createElement('select');
        select.name = `items[${i}][product_id]`;
        select.className = 'form-select';
        select.required = true;
        select.add(new Option('-- Pilih produk --', ''));
        products.forEach(p => {
            select.add(new Option(`${p.sku} - ${p.name} (stok ${p.stock})`, p.id));
        });
        select.value = item.product_id ?? '';
        tdProduct.appendChild(select);

        // Kolom qty
        const tdQty = document.createElement('td');
        const qty = document.createElement('input');
        qty.type = 'number';
        qty.name = `items[${i}][quantity]`;
        qty.className = 'form-control input-qty';
        qty.min = 1;
        qty.required = true;
        qty.value = item.quantity ?? 1;
        qty.addEventListener('input', recalc);
        tdQty.appendChild(qty);

        // Kolom harga beli per unit
        const tdCost = document.createElement('td');
        const cost = document.createElement('input');
        cost.type = 'number';
        cost.name = `items[${i}][cost]`;
        cost.className = 'form-control input-cost';
        cost.min = 1;
        cost.required = true;
        cost.value = item.cost ?? '';
        cost.addEventListener('input', recalc);
        tdCost.appendChild(cost);

        // Kolom subtotal (hanya tampilan)
        const tdSubtotal = document.createElement('td');
        tdSubtotal.className = 'text-end cell-subtotal';

        // Tombol hapus baris
        const tdRemove = document.createElement('td');
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-sm btn-outline-danger';
        remove.textContent = '×';
        remove.addEventListener('click', () => {
            tr.remove();
            if (tbody.children.length === 0) addRow();
            recalc();
        });
        tdRemove.appendChild(remove);

        tr.append(tdProduct, tdQty, tdCost, tdSubtotal, tdRemove);
        tbody.appendChild(tr);
        recalc();
    }

    document.getElementById('add-row').addEventListener('click', () => addRow());
    Object.values(oldItems).forEach(addRow);
</script>
@endsection