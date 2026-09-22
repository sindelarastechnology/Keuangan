<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotifikasiRecentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_recent_mengembalikan_lima_notifikasi_terbaru_dan_unread(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $notifs = [];
        for ($i = 1; $i <= 6; $i++) {
            $notifs[] = $user->notifications()->create([
                'id' => (string) Str::uuid(),
                'type' => NotifikasiService::TYPE_ADMIN,
                'data' => [
                    'title' => "Notif ke-{$i}",
                    'body' => "Isi {$i}",
                    'type' => NotifikasiService::TYPE_ADMIN,
                ],
                'created_at' => now()->addSeconds($i),
            ]);
        }

        // Tandai dua notif paling lama (ke-1 & ke-2) sudah dibaca → 4 unread (6-2).
        $notifs[0]->markAsRead();
        $notifs[1]->markAsRead();

        $response = $this->getJson(route('notifikasi.recent'))
            ->assertOk()
            ->assertJsonPath('unread', 4)
            ->assertJsonCount(5, 'items');

        // Urut terbaru dulu: item pertama adalah ke-6, bukan yang dibaca.
        $response->assertJsonPath('items.0.title', 'Notif ke-6');
        $response->assertJsonPath('items.0.read', false);

        // Yang dibaca (yang paling lama) tidak muncul di lima teratas.
        $titles = collect($response->json('items'))->pluck('title');
        $this->assertFalse($titles->contains('Notif ke-1'));
    }

    public function test_recent_mengharuskan_login(): void
    {
        // DatabaseSeeder meng-Auth::login(admin); kosongkan agar alur guest bisa diuji.
        auth()->logout();
        session()->invalidate();

        $this->getJson(route('notifikasi.recent'))->assertUnauthorized();
    }
}
