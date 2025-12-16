@extends('layouts.app')

@section('content')
<div class="page-header">
    <h4 class="title">Dashboard</h4>
    <form method="get" class="btn-toolbar">
        <label class="form-label mb-0">Periode</label>
        <select name="months" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach([3,6,12] as $m)
                <option value="{{ $m }}" @selected($months===$m)>{{ $m }} Bulan</option>
            @endforeach
        </select>
        <a class="btn btn-sm btn-success" href="{{ route('reports.bookings_excel', ['from'=>$fromDate, 'to'=>$toDate]) }}">Export Excel</a>
        <a class="btn btn-sm btn-outline-success" href="{{ route('reports.usage_excel', ['months'=>$months]) }}">Export Usage</a>
    </form>
    </div>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Ringkasan</h6>
                <ul class="mb-0">
                    <li>Total kendaraan: {{ $summary['vehicles'] }}</li>
                    <li>Pemesanan pending: {{ $summary['pending'] }}</li>
                    <li>Pemesanan approved: {{ $summary['approved'] }}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Grafik Pemakaian Kendaraan ({{ $months }} Bulan Terakhir)</h6>
                <div class="chart-wrap">
                    <div class="chart-box">
                        <canvas id="usageChart" height="160"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="kpi mt-3">
    <div class="card">
        <div class="card-body">
            <div class="label">Total Kendaraan</div>
            <div class="value">{{ $summary['vehicles'] }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="label">Pending</div>
            <div class="value text-warning">{{ $summary['pending'] }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="label">Approved</div>
            <div class="value text-success">{{ $summary['approved'] }}</div>
        </div>
    </div>
</div>
@push('scripts')
<script>
const counts = {!! json_encode($usage) !!};
const labels = {!! json_encode($labels) !!};
new Chart(document.getElementById('usageChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{label:'Jumlah Pemakaian', data: counts, backgroundColor:'#1e40af'}]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        animation: false,
        plugins: { legend: { display: false } },
        scales: {
            x: {
                grid: { display: false },
                ticks: { autoSkip: true, maxTicksLimit: labels.length }
            },
            y: {
                beginAtZero: true,
                grid: { display: true },
                ticks: { precision: 0 }
            }
        }
    }
});
</script>
@endpush
@endsection
