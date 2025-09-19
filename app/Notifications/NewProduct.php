<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewProduct extends Notification
{
    use Queueable;

    protected $product;
    protected $creator;

    public function __construct($product, $creator)
    {
        $this->product = $product;
        $this->creator = $creator;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message'       => 'Produk baru ditambahkan oleh ' . $this->creator->name . ': ' . $this->product->name,
            'product_id'    => $this->product->id,
            'creator_id'    => $this->creator->id,
            'creator_name'  => $this->creator->name,
        ];
    }
}