<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'sku', 'name', 'description',
        'image_path', 'price', 'stock',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function restockItems(): HasMany
    {
        return $this->hasMany(RestockItem::class);
    }

    /**
     * URL publik foto produk, mengikuti konfigurasi disk 'public'.
     * Dipakai di view sebagai $product->image_url.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->image_path
                ? Storage::disk('public')->url($this->image_path)
                : null
        );
    }
}