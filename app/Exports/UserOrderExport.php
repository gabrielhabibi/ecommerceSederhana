<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserOrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection()
    {
        return Order::where('user_id', $this->userId)->get();
    }

    public function map($order): array
    {
        return [
            $order->id,
            'Rp ' . number_format($order->total_price, 0, ',', '.'),
            ucfirst($order->status),
            $order->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Total Harga',
            'Status',
            'Tanggal Dibuat',
        ];
    }
}
