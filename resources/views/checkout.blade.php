<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
<body>
    <h1>Checkout</h1>
    <button id="pay-button">Bayar Sekarang</button>

    <!-- Midtrans Snap JS -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <script>
    const token = '{{ auth()->user()->api_token }}'; // ambil token user dari Laravel

    document.getElementById('pay-button').addEventListener('click', function () {
        fetch('{{ route("api.checkout") }}', { 
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                cart_ids: [1,2,3] // ganti dengan cart user yg aktif
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        alert('Pembayaran berhasil!');
                        window.location.href = '/orders';
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
            } else {
                alert('Gagal membuat transaksi. Coba lagi.');
            }
        });
    });
    </script>
</body>
</html>
