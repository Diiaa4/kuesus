<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'code', 'customer_name', 'phone', 'address', 'note',
        'payment_method', 'payment_status', 'status', 'total', 'payment_proof',
    ];

    public const STATUSES = [
        'new' => 'Baru',
        'processing' => 'Diproses',
        'delivered' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid' => 'Belum bayar',
        'waiting_verification' => 'Menunggu verifikasi',
        'paid' => 'Lunas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status;
    }

    /** Tautan wa.me ke pemesan; nomor 08xx diubah ke format 62xx. */
    public function getWhatsappUrlAttribute(): string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone);
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return 'https://wa.me/'.$digits.'?text='.rawurlencode("Halo {$this->customer_name}, soal pesanan {$this->code}: ");
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return $this->payment_method === 'qris' ? 'QRIS' : 'Bayar di tempat (COD)';
    }
}
