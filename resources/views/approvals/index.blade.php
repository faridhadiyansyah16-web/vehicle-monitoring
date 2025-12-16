@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Persetujuan Saya</h4>
</div>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>ID Booking</th>
            <th>Kendaraan</th>
            <th>Pemesan</th>
            <th>Mulai</th>
            <th>Status Booking</th>
            <th>Status Approval</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($approvals as $ap)
            <tr>
                <td>{{ $ap->booking_id }}</td>
                <td>{{ $ap->booking->vehicle->plate_number }}</td>
                <td>{{ $ap->booking->user->name }}</td>
                <td>{{ $ap->booking->start_time->format('d/m/Y H:i') }}</td>
                <td><span class="badge text-bg-secondary">{{ $ap->booking->status }}</span></td>
                <td><span class="badge text-bg-{{ $ap->status === 'approved' ? 'success' : ($ap->status === 'rejected' ? 'danger' : 'secondary') }}">{{ $ap->status }}</span></td>
                <td>
                    @if($ap->status === 'pending')
                        <form method="post" action="{{ route('approvals.approve', $ap) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Setujui</button>
                        </form>
                        <form method="post" action="{{ route('approvals.reject', $ap) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-danger">Tolak</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $approvals->links() }}
@endsection
