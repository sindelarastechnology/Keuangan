<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private ChatRoom $room;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->room = ChatRoom::ruangKomunitas();
    }

    public function test_kirim_pesan_ke_komunitas_tersimpan_dengan_profil_pengirim(): void
    {
        $response = $this->postJson(route('chat.kirim', $this->room), [
            'body' => 'Halo komunitas',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('own', true)
            ->assertJsonPath('body', 'Halo komunitas')
            ->assertJsonPath('sender_name', $this->user->name)
            ->assertJsonPath('sender_initial', mb_strtoupper(mb_substr($this->user->name, 0, 1)))
            ->assertJsonStructure(['sender_color']);

        $this->assertDatabaseHas('chat_messages', [
            'room_id' => $this->room->id,
            'sender_id' => $this->user->id,
            'body' => 'Halo komunitas',
        ]);
    }

    public function test_polling_pesan_mengembalikan_pesan_baru(): void
    {
        $message = ChatMessage::create([
            'room_id' => $this->room->id,
            'sender_id' => $this->user->id,
            'body' => 'Pesan berhasil masuk polling',
            'type' => ChatMessage::TIPE_USER,
        ]);

        $this->getJson(route('chat.pesan', [$this->room, 'after' => 0]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $message->id)
            ->assertJsonPath('0.sender_name', $this->user->name)
            ->assertJsonPath('0.sender_initial', mb_strtoupper(mb_substr($this->user->name, 0, 1)));
    }

    public function test_polling_mark_berulang_tidak_error_pada_kunci_primer_ganda(): void
    {
        ChatMessage::create([
            'room_id' => $this->room->id,
            'sender_id' => $this->user->id,
            'body' => 'Pesan untuk tandai baca',
            'type' => ChatMessage::TIPE_USER,
        ]);

        // Panggilan pertama membuat baris chat_room_reads (INSERT via upsert).
        $this->getJson(route('chat.pesan', [$this->room, 'after' => 0, 'mark' => '1']))
            ->assertOk();

        // Panggilan kedua memicu UPDATE pada kunci (user_id, room_id);
        // sebelum diperbaiki, Eloquent memakai WHERE id = null -> SQL 42S22.
        $this->getJson(route('chat.pesan', [$this->room, 'after' => 0, 'mark' => '1']))
            ->assertOk()
            ->assertJsonCount(1);

        $this->assertDatabaseCount('chat_room_reads', 1);
    }

    public function test_polling_before_mengembalikan_pesan_lama_terurut_kronologis(): void
    {
        foreach (range(1, 5) as $i) {
            ChatMessage::create([
                'room_id' => $this->room->id,
                'sender_id' => $this->user->id,
                'body' => "Pesan ke-{$i}",
                'type' => ChatMessage::TIPE_USER,
            ]);
        }

        $idTerakhir = (int) ChatMessage::where('room_id', $this->room->id)->orderByDesc('id')->value('id');

        $response = $this->getJson(route('chat.pesan', [$this->room, 'before' => $idTerakhir]))
            ->assertOk()
            ->assertJsonCount(4);

        $ids = collect($response->json())->pluck('id')->values()->all();
        $idsUrut = $ids;
        sort($idsUrut);

        $this->assertSame($idsUrut, $ids, 'Pesan lama harus terurut menaik (id).');
        $this->assertLessThan($idTerakhir, $ids[0]);
    }

    public function test_halaman_chat_menggunakan_url_relatif_untuk_fetch(): void
    {
        $urlPesanAbsolut = route('chat.pesan', $this->room->id);

        $response = $this->get(route('chat.index'));
        $content = $response->getContent();

        // @js merender URL di dalam JSON.parse dengan slash ter-escape (\\\\\\/chat\\\\\\/1\\\\\\/pesan).
        // Normalisasi dengan menghapus backslash agar tak bergantung pada bentuk escaping @js.
        $normalized = str_replace('\\', '', $content);

        $this->assertStringContainsString('/chat/'.$this->room->id.'/pesan', $normalized);
        $this->assertStringNotContainsString($urlPesanAbsolut, $normalized);
    }

    public function test_fetch_fab_komunitas_menggunakan_url_relatif(): void
    {
        $urlKirimRelatif = str_replace(url('/'), '', route('chat.kirim', $this->room->id));
        $urlKirimAbsolut = route('chat.kirim', $this->room->id);

        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertSee($urlKirimRelatif, false);

        $this->assertStringNotContainsString($urlKirimAbsolut, $response->getContent());
    }
}
