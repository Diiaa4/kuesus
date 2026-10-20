<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /** Stok sampai batas ini dianggap menipis. */
    public const LOW_STOCK = 10;

    protected $fillable = ['name', 'description', 'price', 'stock', 'image', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Produk tampil di toko yang stoknya menipis atau habis. */
    public function scopeLowStock(Builder $query): void
    {
        $query->where('is_active', true)->where('stock', '<=', self::LOW_STOCK);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('media/'.$this->image)
            : asset('images/placeholder.svg');
    }
}
