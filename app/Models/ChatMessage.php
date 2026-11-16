<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['user_id', 'sender_id', 'body', 'read_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user()   { return $this->belongsTo(User::class); }
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }

    // Bentuk data untuk tampilan dan JavaScript. $milik = pesan ini ditulis oleh si pembaca halaman.
    public function untukTampilan(bool $milik): array
    {
        return [
            'id' => $this->id,
            'mine' => $milik,
            'body' => $this->body,
            'waktu' => $this->created_at->format('d M, H:i'),
        ];
    }
}