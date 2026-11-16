<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlurPesananTest extends TestCase
{
    use RefreshDatabase;

    private function pengguna(): User
    {
        return User::factory()->create(['role' => Role::Pengguna]);
    }

    private function petugas(): User
    {
        return User::factory()->create(['role' => Role::Petugas]);
    }

    private function layanan(int $harga = 40000): Service
    {
        return Service::create(['name' => 'Deep Clean', 'price' => $harga, 'estimated_days' => 3]);
    }

    private function pesanan(User $pemilik, OrderStatus $status = OrderStatus::MenungguKonfirmasi): Order
    {
        return Order::create(['user_id' => $pemilik->id, 'status' => $status]);
    }

    // ---------- Akses & role ----------

    public function test_pengunjung_diminta_login_untuk_memesan(): void
    {
        $this->get(route('pesanan.buat'))->assertRedirect(route('login'));
    }

    public function test_area_pengguna_dan_petugas_dipisahkan(): void
    {
        $this->actingAs($this->pengguna())
            ->get(route('petugas.dashboard'))
            ->assertForbidden();

        $this->actingAs($this->petugas())
            ->get(route('pesanan.buat'))
            ->assertForbidden();
    }

    public function test_login_petugas_diarahkan_ke_dashboard(): void
    {
        $petugas = $this->petugas();

        $this->post('/login', ['email' => $petugas->email, 'password' => 'password'])
            ->assertRedirect(route('petugas.dashboard'));
    }

    public function test_login_pengguna_diarahkan_ke_beranda(): void
    {
        $pengguna = $this->pengguna();

        $this->post('/login', ['email' => $pengguna->email, 'password' => 'password'])
            ->assertRedirect(route('beranda'));
    }

    public function test_akun_dari_form_daftar_selalu_berperan_pengguna(): void
    {
        $this->post('/register', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'petugas',   // upaya menyusup, harus diabaikan
        ]);

        $user = User::where('email', 'budi@example.com')->firstOrFail();
        $this->assertSame(Role::Pengguna, $user->role);
    }

    // ---------- Sisi pengguna ----------

    public function test_beranda_menampilkan_layanan_aktif(): void
    {
        $this->layanan();

        $this->get('/')->assertOk()->assertSee('Deep Clean');
    }

    public function test_total_pesanan_dihitung_dari_database(): void
    {
        $user = $this->pengguna();
        $layanan = $this->layanan(40000);
        $zona = DeliveryZone::create(['name' => 'Zona 2', 'fee' => 10000]);

        $this->actingAs($user)->post(route('pesanan.simpan'), [
            'items' => [['service_id' => $layanan->id, 'shoe_name' => 'Nike AF1']],
            'jemput' => '1',
            'jemput_address' => 'Jl. Contoh No. 1',
            'jemput_zone' => $zona->id,
            'antar' => '0',
            'total' => 1,   // upaya memanipulasi harga, harus diabaikan
        ])->assertSessionHasNoErrors();

        $order = Order::firstOrFail();

        $this->assertEquals($user->id, $order->user_id);
        $this->assertSame(OrderStatus::MenungguKonfirmasi, $order->status);
        $this->assertEquals(50000, $order->total);   // 40.000 layanan + 10.000 jemput
        $this->assertCount(1, $order->deliveries);
        $this->assertCount(1, $order->logs);
    }

    public function test_pengguna_tidak_bisa_melihat_pesanan_orang_lain(): void
    {
        $pemilik = $this->pengguna();
        $lain = $this->pengguna();
        $order = $this->pesanan($pemilik);

        $this->actingAs($lain)->get(route('pesanan.show', $order))->assertNotFound();
        $this->actingAs($pemilik)->get(route('pesanan.show', $order))->assertOk();
    }

    public function test_pembatalan_hanya_boleh_sebelum_diproses(): void
    {
        $user = $this->pengguna();
        $baru = $this->pesanan($user);
        $dicuci = $this->pesanan($user, OrderStatus::Dicuci);

        $this->actingAs($user)->patch(route('pesanan.batal', $baru))->assertSessionHasNoErrors();
        $this->assertSame(OrderStatus::Dibatalkan, $baru->fresh()->status);

        $this->actingAs($user)->patch(route('pesanan.batal', $dicuci))->assertSessionHasErrors('batal');
        $this->assertSame(OrderStatus::Dicuci, $dicuci->fresh()->status);
    }

    // ---------- Sisi petugas ----------

    public function test_alur_petugas_sampai_selesai(): void
    {
        $order = $this->pesanan($this->pengguna());

        $this->actingAs($this->petugas());

        foreach ([OrderStatus::Diterima, OrderStatus::Dicuci, OrderStatus::Siap] as $harapan) {
            $this->post(route('petugas.pesanan.lanjut', $order))->assertSessionHasNoErrors();
            $this->assertSame($harapan, $order->fresh()->status);
        }

        // Belum lunas: sepatu tidak boleh diserahkan
        $this->post(route('petugas.pesanan.lanjut', $order))->assertSessionHasErrors('aksi');
        $this->assertSame(OrderStatus::Siap, $order->fresh()->status);

        $this->post(route('petugas.pesanan.bayar', $order), ['payment_method' => 'tunai']);
        $this->assertSame('lunas', $order->fresh()->payment_status);

        $this->post(route('petugas.pesanan.lanjut', $order))->assertSessionHasNoErrors();
        $this->assertSame(OrderStatus::Selesai, $order->fresh()->status);
    }

    public function test_alur_penjemputan_oleh_petugas(): void
    {
        $order = $this->pesanan($this->pengguna());
        $zona = DeliveryZone::create(['name' => 'Zona 1', 'fee' => 5000]);
        $jemput = $order->deliveries()->create([
            'type' => 'jemput',
            'address' => 'Jl. Contoh No. 1',
            'delivery_zone_id' => $zona->id,
            'fee' => 5000,
        ]);

        $this->actingAs($this->petugas());

        $this->post(route('petugas.pesanan.lanjut', $order));
        $this->assertSame(OrderStatus::MenungguJemput, $order->fresh()->status);

        $this->post(route('petugas.pengantaran.mulai', $jemput));
        $this->post(route('petugas.pengantaran.selesai', $jemput));

        $this->assertSame(OrderStatus::Diterima, $order->fresh()->status);
        $this->assertSame('selesai', $jemput->fresh()->status);
    }

    public function test_layanan_yang_sudah_dipesan_tidak_bisa_dihapus(): void
    {
        $layanan = $this->layanan();
        $order = $this->pesanan($this->pengguna());
        $order->items()->create(['service_id' => $layanan->id, 'shoe_name' => 'Sepatu', 'price' => 40000]);

        $this->actingAs($this->petugas())
            ->delete(route('petugas.layanan.destroy', $layanan))
            ->assertSessionHasErrors('hapus');

        $this->assertDatabaseHas('services', ['id' => $layanan->id]);
    }
}