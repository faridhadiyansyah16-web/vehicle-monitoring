@extends('layouts.app')

@section('content')
<div class="page-header">
    <h4 class="title">Tambah User</h4>
    <div class="btn-toolbar">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>
<form method="post" action="{{ route('users.store') }}" class="row g-3">
    @csrf
    <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Telepon</label>
        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
            @foreach($roles as $r)
                <option value="{{ $r }}" @selected(old('role')===$r)>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected(old('is_active',1)==1)>Aktif</option>
            <option value="0" @selected(old('is_active')==='0')>Nonaktif</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="col-12">
        <button class="btn btn-primary">Simpan</button>
    </div>
</form>
@endsection

