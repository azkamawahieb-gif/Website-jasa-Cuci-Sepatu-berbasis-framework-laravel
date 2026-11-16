<?php

namespace App\Http\Controllers\Petugas;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', Role::Pengguna->value)
            ->withCount('orders')
            ->withSum(['orders as total_belanja' => fn ($q) => $q->where('payment_status', 'lunas')], 'total')
            ->when($request->query('q'), function ($q, $kata) {
                $q->where(function ($w) use ($kata) {
                    $w->where('name', 'like', "%{$kata}%")
                        ->orWhere('email', 'like', "%{$kata}%")
                        ->orWhere('phone', 'like', "%{$kata}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('petugas.pengguna.index', compact('users'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === Role::Pengguna, 404);

        $orders = $user->orders()->withCount('items')->latest()->get();

        return view('petugas.pengguna.show', compact('user', 'orders'));
    }
}