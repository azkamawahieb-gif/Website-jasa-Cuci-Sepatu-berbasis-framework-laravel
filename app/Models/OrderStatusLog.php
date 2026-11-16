<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $fillable = ['order_id', 'status', 'note', 'user_id'];

    protected function casts(): array
    {
        return ['status' => OrderStatus::class];
    }

    public function order() { return $this->belongsTo(Order::class); }
    public function user()  { return $this->belongsTo(User::class); }
}