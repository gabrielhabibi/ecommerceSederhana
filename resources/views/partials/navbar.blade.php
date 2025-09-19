<nav class="navbar navbar-expand-lg navbar-light px-3 bg-light">
    <div class="container-fluid">
        {{-- Logo + Judul --}}
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center me-md-auto text-black text-decoration-none">
            <img src="{{ asset('assets/logo.svg') }}" alt="Dashboard" style="width: 25px; height: 25px; margin-right: 10px;">
            <span class="fs-4">Admin Panel</span>
        </a>

        {{-- Toggle untuk Mobile --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Menu --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                {{-- Kosong dulu, bisa diisi menu tambahan --}}
            </ul>

            <div class="d-flex align-items-center">
                {{-- 🔔 Notifikasi --}}
                @auth
                    <div class="dropdown me-3">
                        <a href="{{ route('notifications.index') }}" class="btn position-relative" id="notificationDropdown">
                            <i class="bi bi-bell fs-5"></i>
                            @php
                                $unreadCount = auth()->user()->unreadNotifications()->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                @endauth

                {{-- 🌐 Bahasa --}}
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ app()->getLocale() === 'en' ? 'Language' : 'Bahasa' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        <li><a class="dropdown-item" href="{{ route('set.language', 'en') }}">English</a></li>
                        <li><a class="dropdown-item" href="{{ route('set.language', 'id') }}">Indonesia</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>