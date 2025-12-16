@extends('layouts.app')

@section('content')
<h4 class="mb-3">Tambah Log BBM - {{ $vehicle->plate_number }}</h4>
<form method="post" action="{{ route('fuel_logs.store', $vehicle) }}" class="row g-3">
    @csrf
    <div class="col-md-4">
        <label class="form-label">Tanggal</label>
        <input type="date" name="date" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Liter</label>
        <input type="number" step="0.01" name="liters" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Biaya</label>
        <input type="number" step="0.01" name="cost" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Odometer</label>
        <input type="number" name="odometer" class="form-control">
    </div>
    <div class="col-md-8">
        <label class="form-label">Catatan</label>
        <input type="text" name="note" class="form-control">
    </div>
    <div class="col-12">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('fuel_logs.index', $vehicle) }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection
