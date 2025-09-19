<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    // daftar semua notifikasi (pagination)
    public function index()
    {
        $user = auth()->user();

        if ($user->role && $user->role->role_name === 'super_admin') {
            $notifications = DatabaseNotification::orderBy('created_at', 'desc')->paginate(10);
        } else {
            $notifications = $user->notifications()->paginate(10);
        }

        return view('admin.notifications.index', compact('notifications'));
    }
    // lihat detail notifikasi sekaligus tandai sebagai read
    public function show($id)
    {
        $notification = DatabaseNotification::findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        // arahkan ke model terkait sesuai type notifikasi
        if ($notification->type === 'App\Notifications\NewUserRegistered') {
            return redirect()->route('users.index')
                ->with('success', 'Notifikasi user sudah dibaca.');
        }

        if ($notification->type === 'App\Notifications\NewOrder') {
            return redirect()->route('orders.show', $notification->data['order_id']);
        }

        if ($notification->type === 'App\Notifications\OrderStatusChanged') {
            return redirect()->route('orders.show', $notification->data['order_id']);
        }

        return redirect()->route('notifications.index');
    }

    // hapus 1 notifikasi
    public function destroy($id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        $notification->delete();

        return redirect()->route('notifications.index')->with('success', 'Notifikasi berhasil dihapus.');
    }

    // hapus semua notifikasi
    public function clear()
    {
        $user = auth()->user();

        if ($user->role && $user->role->role_name === 'super_admin') {
            DatabaseNotification::query()->delete();
        } else {
            $user->notifications()->delete();
        }

        return redirect()->route('notifications.index')->with('success', 'Notifikasi berhasil dihapus.');
    }
}