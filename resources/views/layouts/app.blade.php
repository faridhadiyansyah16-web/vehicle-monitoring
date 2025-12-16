<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-brand shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-semibold" href="{{ url('/dashboard') }}">
            {{ config('app.name') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="{{ route('bookings.index') }}">Pemesanan</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('vehicles.index') }}">Kendaraan</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('drivers.index') }}">Driver</a></li>
                @if(auth()->check() && auth()->user()->isApprover())
                <li class="nav-item"><a class="nav-link" href="{{ route('approvals.index') }}">Persetujuan</a></li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Export
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('reports.bookings') }}" download>CSV</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.bookings_excel') }}" download>Excel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('reports.vehicles_excel') }}" download>Excel Kendaraan</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.drivers_excel') }}" download>Excel Driver</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.fuel_logs_excel') }}" download>Excel BBM</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.service_logs_excel') }}" download>Excel Servis</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.usage_excel', ['months'=>6]) }}" download>Excel Usage (6 bln)</a></li>
                    </ul>
                </li>
            </ul>
            <form method="post" action="{{ route('logout') }}" class="d-flex">
                @csrf
                <button class="btn btn-outline-light">Logout</button>
            </form>
        </div>
    </div>
</nav>
<main class="container-fluid py-4">
    <div class="container-xl">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @yield('content')
    </div>
</main>
<footer class="border-top py-3">
    <div class="container-xl text-muted small d-flex justify-content-between">
        <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
        <span>v{{ app()->version() }}</span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
