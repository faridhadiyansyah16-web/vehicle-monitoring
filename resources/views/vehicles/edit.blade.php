@extends('layouts.app')

@section('content')
<h4 class="mb-3">Edit Kendaraan</h4>
<form method="post" action="{{ route('vehicles.update', $vehicle) }}" class="row g-3">
    @csrf
    @method('PUT')
    <div class="col-md-4">
        <label class="form-label">Plat Nomor</label>
        <input type="text" value="{{ $vehicle->plate_number }}" class="form-control" disabled>
    </div>
    <div class="col-md-4">
        <label class="form-label">Jenis</label>
        <input type="text" name="type" value="{{ $vehicle->type }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Kapasitas</label>
        <input type="number" name="capacity" value="{{ $vehicle->capacity }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Jenis BBM</label>
        <select name="fuel_type" class="form-select">
            <option value="">-</option>
            <option value="diesel" @selected($vehicle->fuel_type==='diesel')>Diesel</option>
            <option value="gasoline" @selected($vehicle->fuel_type==='gasoline')>Bensin</option>
            <option value="electric" @selected($vehicle->fuel_type==='electric')>Listrik</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach(['available','in_use','maintenance','inactive'] as $s)
                <option value="{{ $s }}" @selected($vehicle->status===$s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Servis Berikutnya</label>
        <input type="date" name="next_service_date" value="{{ optional($vehicle->next_service_date)->format('Y-m-d') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Odometer</label>
        <input type="number" name="odometer" value="{{ $vehicle->odometer }}" class="form-control">
    </div>
    <div class="col-12">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection
