@extends('layouts.app')

@section('content')
<h4 class="mb-3">Buat Pemesanan</h4>
<form method="post" action="{{ route('bookings.store') }}" class="row g-3">
    @csrf
    <div class="col-md-4">
        <label class="form-label">Kendaraan</label>
        <select name="vehicle_id" class="form-select" required>
            <option value="">- pilih -</option>
            @foreach($vehicles as $v)
                <option value="{{ $v->id }}">{{ $v->plate_number }} ({{ $v->type }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Driver</label>
        <select name="driver_id" class="form-select">
            <option value="">- tanpa driver -</option>
            @foreach($drivers as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Waktu Mulai</label>
        <input type="datetime-local" name="start_time" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Waktu Selesai</label>
        <input type="datetime-local" name="end_time" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Keperluan</label>
        <input type="text" name="purpose" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tujuan</label>
        <input type="text" name="destination" class="form-control">
    </div>
    <div class="col-md-12">
        <label class="form-label">Pihak Penyetuju (minimal 2)</label>
        <select name="approver_ids[]" class="form-select" multiple required size="6">
            @foreach($approvers as $u)
                <option value="{{ $u->id }}">{{ $u->name }} - {{ $u->email }}</option>
            @endforeach
        </select>
        <div class="form-text">Urutan mengikuti urutan pilihan (atas ke bawah).</div>
    </div>
    <div class="col-12">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection
