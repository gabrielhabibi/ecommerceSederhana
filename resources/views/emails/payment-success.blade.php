{{-- resources/views/emails/payment-success.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran Berhasil</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Pembayaran Berhasil!</h2>
    <p>Halo, <strong>{{ $order->user->name }}</strong></p>
    <p>Terima kasih telah melakukan pembayaran. Berikut detail order Anda:</p>

    <ul>
        <li><strong>Order ID:</strong> {{ $order->id }}</li>
        <li><strong>Total:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</li>
        <li><strong>Status:</strong> {{ ucfirst($order->status) }}</li>
    </ul>

    <p>Jika ada pertanyaan, silakan hubungi customer service kami.</p>
    <p>Salam hangat,<br>Tim Ecommerce</p>
</body>
</html>
