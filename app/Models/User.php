<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'address'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Pembeli = pernah punya pesanan berstatus Selesai. Syarat untuk menulis ulasan. */
    public function hasCompletedOrder(): bool
    {
        return $this->orders()->where('status', 'delivered')->exists();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(Message::class, 'customer_id');
    }
}
