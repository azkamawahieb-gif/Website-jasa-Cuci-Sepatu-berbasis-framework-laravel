<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'code', 'user_id', 'status', 'payment_status', 'payment_method', 'payment_channel',
        'subtotal', 'delivery_fee_total', 'voucher_code', 'discount_total', 'total',
        'estimated_done_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'estimated_done_at' => 'date',
        ];
    }

    // Kode otomatis, contoh: SC-261114-001
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (! $order->code) {
                $urutan = static::whereDate('created_at', today())->count() + 1;
                $order->code = 'SC-' . now()->format('ymd') . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function logs()
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    public function jemput()
    {
        return $this->deliveries->firstWhere('type', 'jemput');
    }

    public function antar()
    {
        return $this->deliveries->firstWhere('type', 'antar');
    }

    // Label status yang jelas: "Siap" berbeda artinya bila diantar atau diambil sendiri
    public function statusLabel(?OrderStatus $status = null): string
    {
        $status ??= $this->status;

        if ($status === OrderStatus::Siap) {
            return $this->antar() ? 'Siap Diantar' : 'Siap Diambil';
        }

        return $status->label();
    }

    // Hitung ulang subtotal, biaya antar/jemput, diskon voucher, dan total
    public function hitungUlang(): void
    {
        $subtotal = (int) $this->items()->sum('price');
        $ongkir = (int) $this->deliveries()->sum('fee');
        $diskon = min((int) $this->discount_total, $subtotal);

        $this->update([
            'subtotal' => $subtotal,
            'delivery_fee_total' => $ongkir,
            'discount_total' => $diskon,
            'total' => $subtotal - $diskon + $ongkir,
        ]);
    }

    // Nama metode pembayaran untuk ditampilkan
    public function metodeLabel(): string
    {
        $label = match ($this->payment_method) {
            'cod', 'tunai' => 'COD (bayar di tempat)',
            'transfer' => 'Transfer bank',
            'qris' => 'QRIS',
            default => 'Belum dipilih',
        };

        return $this->payment_channel ? $label . ' - ' . $this->payment_channel : $label;
    }

    // Catat perubahan status sekaligus ke riwayat
    public function ubahStatus(OrderStatus $status, ?string $catatan = null, ?int $userId = null): void
    {
        $this->update(['status' => $status]);

        $this->logs()->create([
            'status' => $status->value,
            'note' => $catatan,
            'user_id' => $userId,
        ]);
    }

    // Tombol "Kabari via WhatsApp" untuk petugas
    public function whatsappUrl(): string
    {
        $nomor = preg_replace('/\D/', '', $this->user->phone ?? '');

        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        $teks = "Halo {$this->user->name}, pesanan sepatu Anda ({$this->code}) sudah siap. Terima kasih!";

        return 'https://wa.me/' . $nomor . '?text=' . rawurlencode($teks);
    }
}
