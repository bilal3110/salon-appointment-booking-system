<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'bookings';
    protected $fillable = [
        'customer_name',
        'customer_contact',
        'customer_email',
        'staff_id',
        'service_id',
        'booking_date',
        'booking_time',
        'status',
    ];

    public function staff(){
        return $this->belongsTo(Staff::class,'staff_id');
    }

    public function service(){
        return $this->belongsTo(Service::class,'service_id');
    }
}
