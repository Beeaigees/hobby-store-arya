@extends('layouts.app')

@section('title', 'Penjualan Baru')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h4 class="mb-3">Penjualan Baru</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sales.store') }}" method="POST">
            @csrf

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th style="width: 120px">Qty</th>
                            <th class="text-end" style="width: 160px">Harga</th>
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
                <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    const products = {{ Illuminate\Support\Js::from($products) }};
    const oldItems = {{ Illuminate\Support\Js::from(old('items', [['product_id' => '', 'quantity' => 1]])) }};

    const tbody = document.getElementById('item-rows');
    const grandTotal = document.getElementById('grand-total');
    const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');
    let rowIndex = 0;

    function findProduct(id) {
        return products.find(p => String(p.id) === String(id));
    }

    function recalc() {
        let total = 0;

        tbody.querySelectorAll('tr').forEach(tr => {
            const product = findProduct(tr.querySelector('select').value);
            const qtyInput = tr.querySelector('input[type=number]');
            const qty = parseInt(qtyInput.value, 10) || 0;

            tr.querySelector('.cell-price').textContent = product ? rupiah(product.price) : '-';
            qtyInput.max = product ? product.stock : '';

            const subtotal = product ? product.price * qty : 0;
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
        select.addEventListener('change', recalc);
        tdProduct.appendChild(select);

        // Kolom qty
        const tdQty = document.createElement('td');
        const qty = document.createElement('input');
        qty.type = 'number';
        qty.name = `items[${i}][quantity]`;
        qty.className = 'form-control';
        qty.min = 1;
        qty.required = true;
        qty.value = item.quantity ?? 1;
        qty.addEventListener('input', recalc);
        tdQty.appendChild(qty);

        // Kolom harga dan subtotal (hanya tampilan)
        const tdPrice = document.createElement('td');
        tdPrice.className = 'text-end cell-price';
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

        tr.append(tdProduct, tdQty, tdPrice, tdSubtotal, tdRemove);
        tbody.appendChild(tr);
        recalc();
    }

    document.getElementById('add-row').addEventListener('click', () => addRow());
    Object.values(oldItems).forEach(addRow);
</script>
@endsection