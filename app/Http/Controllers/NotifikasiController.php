<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotifikasiController extends Controller
{
    /**
     * Pusat notifikasi milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $notifikasi = $request->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notifikasi.index', compact('notifikasi'));
    }

    /**
     * Tandai satu notifikasi sudah dibaca.
     */
    public function baca(Request $request, DatabaseNotification $notification)
    {
        $notifikasi = $request->user()
            ->notifications()
            ->whereKey($notification->getKey())
            ->firstOrFail();

        if ($notifikasi->unread()) {
            $notifikasi->markAsRead();
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi sudah dibaca.
     */
    public function bacaSemua(Request $request)
    {
        $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Jumlah notifikasi belum dibaca untuk badge (dipanggil polling).
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $total = $request->user()->notifications()->whereNull('read_at')->count();

        return response()->json(['total' => $total]);
    }

    /**
     * Notifikasi terbaru untuk dropdown di navbar (dipanggil polling).
     */
    public function recent(Request $request): JsonResponse
    {
        $notifikasi = $request->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(function (DatabaseNotification $notif) {
                $data = is_array($notif->data) ? $notif->data : [];

                return [
                    'id' => $notif->getKey(),
                    'read' => $notif->read_at !== null,
                    'title' => $data['title'] ?? $notif->type,
                    'body' => $data['body'] ?? '',
                    'type' => $data['type'] ?? $notif->type,
                    'created_at' => $notif->created_at?->toISOString(),
                ];
            });

        $unread = $request->user()->notifications()->whereNull('read_at')->count();

        return response()->json([
            'items' => $notifikasi,
            'unread' => $unread,
        ]);
    }
}
