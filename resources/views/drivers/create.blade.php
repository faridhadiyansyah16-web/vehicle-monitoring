@extends('layouts.app')

@section('content')
<h4 class="mb-3">Tambah Driver</h4>
<form method="post" action="{{ route('drivers.store') }}" class="row g-3">
    @csrf
    <div class="col-md-4">
        <label class="form-label">Nama</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Telepon</label>
        <input type="text" name="phone" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Nomor SIM</label>
        <input type="text" name="license_number" class="form-control">
    </div>
    <div class="col-12">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection
