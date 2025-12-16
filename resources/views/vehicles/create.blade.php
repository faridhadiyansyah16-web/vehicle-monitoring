@extends('layouts.app')

@section('content')
<h4 class="mb-3">Tambah Kendaraan</h4>
<form method="post" action="{{ route('vehicles.store') }}" class="row g-3">
    @csrf
    <div class="col-md-4">
        <label class="form-label">Plat Nomor</label>
        <input type="text" name="plate_number" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Jenis</label>
        <input type="text" name="type" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Kapasitas</label>
        <input type="number" name="capacity" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Jenis BBM</label>
        <select name="fuel_type" class="form-select">
            <option value="">-</option>
            <option value="diesel">Diesel</option>
            <option value="gasoline">Bensin</option>
            <option value="electric">Listrik</option>
        </select>
    </div>
    <div class="col-12">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection
