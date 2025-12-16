@extends('layouts.app')

@section('content')
<div class="page-header">
    <h4 class="title">Log BBM: {{ $vehicle->plate_number }}</h4>
    <a href="{{ route('fuel_logs.create', $vehicle) }}" class="btn btn-primary">Tambah Log BBM</a>
    <a href="{{ route('reports.fuel_logs_excel', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-success" download>Export Excel</a>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Tanggal</th>
            <th>Liter</th>
            <th>Biaya</th>
            <th>Odometer</th>
            <th>Catatan</th>
        </tr>
        </thead>
        <tbody>
        @foreach($logs as $l)
            <tr>
                <td>{{ \Carbon\Carbon::parse($l->date)->format('d/m/Y') }}</td>
                <td>{{ $l->liters }}</td>
                <td>{{ $l->cost }}</td>
                <td>{{ $l->odometer }}</td>
                <td>{{ $l->note }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $logs->links() }}
@endsection
