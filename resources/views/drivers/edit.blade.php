@extends('layouts.app')

@section('content')
<h4 class="mb-3">Edit Driver</h4>
<form method="post" action="{{ route('drivers.update', $driver) }}" class="row g-3">
    @csrf
    @method('PUT')
    <div class="col-md-4">
        <label class="form-label">Nama</label>
        <input type="text" name="name" value="{{ $driver->name }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Telepon</label>
        <input type="text" name="phone" value="{{ $driver->phone }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Nomor SIM</label>
        <input type="text" name="license_number" value="{{ $driver->license_number }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" @selected($driver->status==='active')>Aktif</option>
            <option value="inactive" @selected($driver->status==='inactive')>Tidak aktif</option>
        </select>
    </div>
    <div class="col-12">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection
