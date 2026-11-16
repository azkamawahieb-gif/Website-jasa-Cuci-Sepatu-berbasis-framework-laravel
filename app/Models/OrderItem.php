<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'service_id', 'shoe_name', 'shoe_color', 'shoe_size',
        'condition_notes', 'before_photo', 'after_photo', 'price',
    ];

    public function order()   { return $this->belongsTo(Order::class); }
    public function service() { return $this->belongsTo(Service::class); }
}