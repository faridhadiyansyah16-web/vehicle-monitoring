@extends('layouts.app')

@section('content')
<div class="page-header">
    <h4 class="title">Pemesanan Kendaraan</h4>
    <div class="btn-toolbar">
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">Buat Pemesanan</a>
    </div>
</div>
<form method="get" class="row g-2 mb-3">
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            @foreach(['pending','approved','rejected','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="vehicle_id" class="form-select">
            <option value="">Semua kendaraan</option>
            @foreach($vehicles as $v)
                <option value="{{ $v->id }}" @selected(request('vehicle_id')==$v->id)>{{ $v->plate_number }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="Dari"></div>
    <div class="col-md-2"><input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="Sampai"></div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100">Filter</button>
    </div>
    <div class="col-md-2">
        <a class="btn btn-outline-primary w-100" href="{{ route('reports.bookings', request()->query()) }}" download>Export CSV</a>
    </div>
    <div class="col-md-2">
        <a class="btn btn-success w-100" href="{{ route('reports.bookings_excel', request()->query()) }}" download>Export Excel</a>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Pemesan</th>
            <th>Kendaraan</th>
            <th>Driver</th>
            <th>Waktu</th>
            <th>Tujuan</th>
            <th>Status</th>
            <th>Persetujuan</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bookings as $b)
            <tr>
                <td>{{ $b->id }}</td>
                <td>{{ $b->user->name }}</td>
                <td>{{ $b->vehicle->plate_number }}</td>
                <td>{{ $b->driver?->name ?? '-' }}</td>
                <td>{{ $b->start_time->format('d/m/Y H:i') }}</td>
                <td>{{ $b->destination }}</td>
                <td><span class="badge text-bg-{{ $b->status === 'approved' ? 'success' : ($b->status === 'rejected' ? 'danger' : 'secondary') }}">{{ $b->status }}</span></td>
                <td>
                    @foreach($b->approvals as $ap)
                        <div class="d-flex gap-2 align-items-center">
                            <span>Lv{{ $ap->level }} - {{ $ap->approver->name }}</span>
                            @if(auth()->check() && auth()->user()->id === $ap->approver_id && $ap->status === 'pending')
                                <form method="post" action="{{ route('approvals.approve', $ap) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Setujui</button>
                                </form>
                                <form method="post" action="{{ route('approvals.reject', $ap) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">Tolak</button>
                                </form>
                            @else
                                <span class="badge text-bg-{{ $ap->status === 'approved' ? 'success' : ($ap->status === 'rejected' ? 'danger' : 'secondary') }}">{{ $ap->status }}</span>
                            @endif
                        </div>
                    @endforeach
                    @if(auth()->check() && (auth()->id() === $b->user_id || auth()->user()->isAdmin()) && $b->status === 'approved')
                        <form method="post" action="{{ route('bookings.complete', $b) }}" class="mt-2">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="datetime-local" name="end_time" class="form-control" placeholder="Selesai" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="distance_km" class="form-control" placeholder="Jarak (km)">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.01" name="fuel_consumed_l" class="form-control" placeholder="BBM (L)">
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-sm btn-primary">Selesaikan</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $bookings->links() }}
@endsection
