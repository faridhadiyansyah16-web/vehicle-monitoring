@extends('layouts.app')

@section('content')
<div class="page-header">
    <h4 class="title">Kendaraan</h4>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">Tambah Kendaraan</a>
    @endif
    <a href="{{ route('reports.vehicles_excel') }}" class="btn btn-success" download>Export Excel</a>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Plat</th>
            <th>Jenis</th>
            <th>Kapasitas</th>
            <th>BBM</th>
            <th>Status</th>
            <th>Servis Berikutnya</th>
            <th>Odometer</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($vehicles as $v)
            <tr>
                <td>{{ $v->plate_number }}</td>
                <td>{{ $v->type }}</td>
                <td>{{ $v->capacity }}</td>
                <td>{{ $v->fuel_type }}</td>
                <td><span class="badge text-bg-secondary">{{ $v->status }}</span></td>
                <td>{{ $v->next_service_date?->format('d/m/Y') }}</td>
                <td>{{ $v->odometer }}</td>
                <td class="d-flex gap-2">
                    <a href="{{ route('fuel_logs.index', $v) }}" class="btn btn-sm btn-outline-primary">BBM</a>
                    <a href="{{ route('service_logs.index', $v) }}" class="btn btn-sm btn-outline-secondary">Servis</a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('vehicles.edit', $v) }}" class="btn btn-sm btn-warning">Edit</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $vehicles->links() }}
@endsection
