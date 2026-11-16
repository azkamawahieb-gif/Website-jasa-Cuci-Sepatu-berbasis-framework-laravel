<?php

namespace App\Http\Controllers\Petugas;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $hitung = DB::table('orders')
            ->select('status', DB::raw('count(*) as n'))
            ->groupBy('status')
            ->pluck('n', 'status');

        return view('petugas.dashboard', [
            'perluKonfirmasi' => $hitung['menunggu_konfirmasi'] ?? 0,
            'dikerjakan' => ($hitung['diterima'] ?? 0) + ($hitung['dicuci'] ?? 0),
            'siap' => $hitung['siap'] ?? 0,
            'tugasAktif' => Delivery::whereIn('status', ['dijadwalkan', 'dalam_perjalanan'])
                ->whereHas('order', fn ($q) => $q->where('status', '!=', 'dibatalkan'))
                ->count(),
            'omzet' => Order::where('payment_status', 'lunas')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
            'terbaru' => Order::with('user')->latest()->take(5)->get(),
            'passwordBawaan' => Hash::check('password', $request->user()->password),
        ]);
    }
}