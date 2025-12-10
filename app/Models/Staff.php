<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;
    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'bio',
        'photo',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'staff_services', 'staff_id', 'service_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'staff_id');
    }
}
