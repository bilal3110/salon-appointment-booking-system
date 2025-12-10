<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_name',
        'description',
        'price',
        'service_type',
    ];

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'staff_services', 'service_id', 'staff_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'service_id');
    }
}
