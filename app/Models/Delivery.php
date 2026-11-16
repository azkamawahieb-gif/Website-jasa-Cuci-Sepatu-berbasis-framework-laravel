<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'order_id', 'type', 'address', 'maps_url', 'delivery_zone_id', 'fee', 'scheduled_at',
        'handled_by', 'status', 'completed_at', 'proof_photo', 'note',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function order()   { return $this->belongsTo(Order::class); }
    public function zone()    { return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id'); }
    public function petugas() { return $this->belongsTo(User::class, 'handled_by'); }
}