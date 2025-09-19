<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewOrder extends Notification
{
    use Queueable;

    public $order;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $status)
    {
        $this->order  = $order;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        $message = match ($this->status) {
            'success' => "Pesanan #{$this->order->id} dari {$this->order->user->name} telah berhasil dibayar ✅",
            'failed'  => "Pesanan #{$this->order->id} dari {$this->order->user->name} gagal dibayar ❌",
            'pending' => "Pesanan #{$this->order->id} dari {$this->order->user->name} menunggu pembayaran ⏳",
            default   => "Status pesanan #{$this->order->id} diperbarui.",
        };

        return [
            'order_id'  => $this->order->id,
            'user_name' => $this->order->user->name ?? 'Unknown',
            'status'    => $this->status,
            'amount'    => $this->order->total ?? 0,
            'message'   => "Order baru dari {$this->order->user->name}, status: {$this->status}"
        ];
    }
}
