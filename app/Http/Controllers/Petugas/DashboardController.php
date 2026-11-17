<?php

namespace App\Http\Controllers\Petugas;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    // Warna segmen bilah alur status (diambil dari palet)
    private const WARNA_STATUS = [
        'menunggu_konfirmasi' => '#F2B84B',
        'menunggu_jemput'     => '#A0F9DB',
        'diterima'            => '#80C6D8',
        'dicuci'              => '#2D5BB0',
        'siap'                => '#2FA98C',
        'dalam_pengantaran'   => '#6394D3',
        'selesai'             => '#4A5D78',
        'dibatalkan'          => '#D9534F',
    ];

    public function __invoke(Request $request)
    {
        $hariPendek  = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $hariPanjang = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulanPendek = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $bulanPanjang = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Jumlah pesanan per status (satu query)
        $hitung = DB::table('orders')
            ->select('status', DB::raw('count(*) as n'))
            ->groupBy('status')
            ->pluck('n', 'status');

        // Pesanan 7 hari terakhir (satu query), tidak termasuk yang dibatalkan
        $awal = now()->subDays(6)->startOfDay();
        $perTanggal = Order::query()
            ->where('status', '!=', 'dibatalkan')
            ->where('created_at', '>=', $awal)
            ->selectRaw("DATE(created_at) as tgl, COUNT(*) as jumlah, SUM(CASE WHEN payment_status = 'lunas' THEN total ELSE 0 END) as omzet")
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('tgl');

        $harian = [];
        for ($i = 0; $i < 7; $i++) {
            $t = $awal->copy()->addDays($i);
            $baris = $perTanggal->get($t->toDateString());
            $harian[] = [
                'label'   => $hariPendek[$t->dayOfWeek],
                'panjang' => $hariPanjang[$t->dayOfWeek] . ', ' . $t->day . ' ' . $bulanPendek[$t->month - 1],
                'jumlah'  => (int) ($baris->jumlah ?? 0),
                'omzet'   => (int) ($baris->omzet ?? 0),
                'hariIni' => $i === 6,
            ];
        }
        $maksHarian = max(1, max(array_column($harian, 'jumlah')));
        $totalHarian = array_sum(array_column($harian, 'jumlah'));

        // Bilah alur status: hanya status yang ada isinya
        $alurStatus = collect(OrderStatus::cases())
            ->map(fn (OrderStatus $s) => [
                'label'  => $s->label(),
                'jumlah' => (int) ($hitung[$s->value] ?? 0),
                'warna'  => self::WARNA_STATUS[$s->value],
            ])
            ->filter(fn ($r) => $r['jumlah'] > 0)
            ->values();
        $totalStatus = $alurStatus->sum('jumlah');

        // Tugas jemput/antar hari ini yang masih aktif (maksimal 5)
        $tugasHariIni = Delivery::with('order.user')
            ->whereIn('status', ['dijadwalkan', 'dalam_perjalanan'])
            ->whereBetween('scheduled_at', [now()->startOfDay(), now()->endOfDay()])
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'dibatalkan'))
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        $sekarang = now();
        $namaLengkap = $request->user()->name ?? 'Petugas';

        return view('petugas.dashboard', [
            'perluKonfirmasi' => $hitung['menunggu_konfirmasi'] ?? 0,
            'dikerjakan' => ($hitung['diterima'] ?? 0) + ($hitung['dicuci'] ?? 0),
            'siap' => $hitung['siap'] ?? 0,
            'tugasAktif' => Delivery::whereIn('status', ['dijadwalkan', 'dalam_perjalanan'])
                ->whereHas('order', fn ($q) => $q->where('status', '!=', 'dibatalkan'))
                ->count(),
            'omzet' => Order::where('payment_status', 'lunas')
                ->whereYear('created_at', $sekarang->year)
                ->whereMonth('created_at', $sekarang->month)
                ->sum('total'),
            'terbaru' => Order::with(['user', 'deliveries'])->latest()->take(5)->get(),
            'passwordBawaan' => Hash::check('password', $request->user()->password),

            // Baru di Tahap 3
            'namaDepan' => strtok(trim($namaLengkap), ' ') ?: 'Petugas',
            'tanggalHariIni' => $hariPanjang[$sekarang->dayOfWeek] . ', ' . $sekarang->day . ' ' . $bulanPanjang[$sekarang->month - 1] . ' ' . $sekarang->year,
            'harian' => $harian,
            'maksHarian' => $maksHarian,
            'totalHarian' => $totalHarian,
            'alurStatus' => $alurStatus,
            'totalStatus' => $totalStatus,
            'tugasHariIni' => $tugasHariIni,
        ]);
    }
}