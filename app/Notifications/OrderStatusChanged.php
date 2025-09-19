<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Gunakan route helper ke halaman detail order
        return (new MailMessage)
            ->subject('Update Status Order #' . $this->order->id)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Status order Anda telah berubah.')
            ->line('Order #' . $this->order->id . ' sekarang berstatus: ' . ucfirst($this->order->status))
            ->action('Lihat Order', route('orders.show', $this->order->id)) // <<< diperbaiki
            ->line('Terima kasih telah berbelanja di toko kami!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id'   => $this->order->id,
            'user_name'  => $this->order->user->name ?? 'Unknown',
            'new_status' => $this->order->status,
            'message'    => "Status pesanan #{$this->order->id} diubah menjadi {$this->order->status}"
        ];
    }
}