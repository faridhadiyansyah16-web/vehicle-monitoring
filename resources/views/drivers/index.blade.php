@extends('layouts.app')

@section('content')
<div class="page-header">
    <h4 class="title">Driver</h4>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('drivers.create') }}" class="btn btn-primary">Tambah Driver</a>
    @endif
    <a href="{{ route('reports.drivers_excel') }}" class="btn btn-success" download>Export Excel</a>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Nama</th>
            <th>Telepon</th>
            <th>SIM</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($drivers as $d)
            <tr>
                <td>{{ $d->name }}</td>
                <td>{{ $d->phone }}</td>
                <td>{{ $d->license_number }}</td>
                <td><span class="badge text-bg-secondary">{{ $d->status }}</span></td>
                <td>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('drivers.edit', $d) }}" class="btn btn-sm btn-warning">Edit</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $drivers->links() }}
@endsection
