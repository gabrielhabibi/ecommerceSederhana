<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-white px-4">
        <div class="max-w-2xl w-full text-center">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-6">
                {{ __('auth.welcome') }} <span class="text-blue-600">SHOPORA</span>
            </h1>
            <p class="text-lg text-gray-700 mb-8">
                Aplikasi e-commerce sederhana untuk mengelola produk, kategori, dan transaksi secara cepat dan efisien.
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}"
                    class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition">
                    {{ __('auth.login') }}
                </a>
                <a href="{{ route('register') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-800 rounded-md font-semibold hover:bg-gray-300 transition">
                    {{ __('auth.register') }}
                </a>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function () {
            window.location.href = "http://127.0.0.1:8000";
        }, 3000); // 3 detik, bisa kamu ubah sesuai kebutuhan
    </script>
</x-guest-layout>
