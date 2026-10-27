<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo. role di-set lewat forceFill karena 'role' tidak ada di $fillable.
        $owner = new User();
        $owner->forceFill([
            'name'     => 'Owner Toko',
            'email'    => 'owner@hobbystore.test',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ])->save();

        $staff = new User();
        $staff->forceFill([
            'name'     => 'Staff Toko',
            'email'    => 'staff@hobbystore.test',
            'password' => Hash::make('password'),
            'role'     => 'staff',
        ])->save();

        // Kategori
        $gunpla = Category::create(['name' => 'Gunpla & Model Kit']);
        $figure = Category::create(['name' => 'Action Figure']);
        $diecast = Category::create(['name' => 'Diecast']);
        $tools = Category::create(['name' => 'Tools & Cat']);

        // Supplier
        Supplier::create([
            'name'    => 'Toko Grosir Hobi Jaya',
            'phone'   => '081234567890',
            'address' => 'Jakarta',
        ]);
        Supplier::create([
            'name'    => 'Nusantara Toys Distributor',
            'phone'   => '081298765432',
            'address' => 'Surabaya',
        ]);

        // Produk (stok awal / opening stock)
        $products = [
            [$gunpla->id, 'GNP-001', 'HG Gundam Aerial 1/144', 'Model kit HG skala 1/144.', 185000, 12],
            [$gunpla->id, 'GNP-002', 'RG Nu Gundam 1/144', 'Model kit Real Grade skala 1/144.', 520000, 5],
            [$figure->id, 'FIG-001', 'Figma Action Figure Standard', 'Action figure articulated.', 750000, 4],
            [$figure->id, 'FIG-002', 'Nendoroid Mini Figure', 'Figure chibi koleksi.', 650000, 6],
            [$diecast->id, 'DIE-001', 'Hot Wheels Premium', 'Diecast skala 1/64.', 95000, 20],
            [$diecast->id, 'DIE-002', 'Tomica Limited Vintage', 'Diecast Tomica edisi terbatas.', 210000, 8],
            [$tools->id, 'TLS-001', 'Nipper Gunpla Hobby', 'Tang potong untuk model kit.', 85000, 15],
            [$tools->id, 'TLS-002', 'Cat Acrylic Set 12 Warna', 'Cat akrilik untuk model kit.', 120000, 10],
        ];

        foreach ($products as [$categoryId, $sku, $name, $description, $price, $stock]) {
            Product::create([
                'category_id' => $categoryId,
                'sku'         => $sku,
                'name'        => $name,
                'description' => $description,
                'price'       => $price,
                'stock'       => $stock,
            ]);
        }
    }
}