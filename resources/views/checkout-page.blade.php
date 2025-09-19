<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Order #{{ $order->id }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {font-family: Arial, sans-serif;margin: 30px;}
        .checkout-container {max-width: 700px;margin: auto;padding: 20px;border: 1px solid #ddd;border-radius: 12px;box-shadow: 0 2px 6px rgba(0,0,0,0.1);}
        h1 {margin-bottom: 15px;}
        table {width: 100%;border-collapse: collapse;margin-bottom: 15px;}
        table th, table td {padding: 10px;border: 1px solid #eee;text-align: left;}
        .total {text-align: right;font-weight: bold;font-size: 18px;}
        .btn-pay {background: #4CAF50;color: white;padding: 12px 18px;border: none;border-radius: 8px;cursor: pointer;font-size: 16px;}
        .btn-pay:hover {background: #45a049;}
    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Checkout Order #{{ $order->id }}</h1>

        <h3>Detail Produk</h3>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="total">Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>

        <button id="pay-button" class="btn-pay">Bayar Sekarang</button>
    </div>

    <!-- Midtrans Snap JS -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            window.snap.pay("{{ $snapToken }}", {
                onSuccess: function(result){
                    alert('Pembayaran berhasil!');
                    window.location.href = "{{ route('orders.index') }}";
                },
                onPending: function(result){
                    alert('Pembayaran pending. Silakan selesaikan.');
                },
                onError: function(result){
                    alert('Pembayaran gagal.');
                },
                onClose: function(){
                    alert('Pembayaran ditutup tanpa selesai.');
                }
            });
        });
    </script>
</body>
</html>
