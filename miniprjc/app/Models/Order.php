<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    // Tabel tujuan harus 'orders'
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'receiver_address',
        'receiver_phone',
        'total_price',
        'status', // tambahkan agar bisa update status (Confirmed, Pending, dll)
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }

    // Accessor total harga otomatis
    public function getTotalPriceAttribute()
    {
        return $this->quantity * ($this->product->price ?? 0);
    }
}
