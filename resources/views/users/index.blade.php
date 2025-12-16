@extends('layouts.app')

@section('content')
<div class="page-header">
    <h4 class="title">Users</h4>
    <div class="btn-toolbar">
        <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah User</a>
    </div>
    </div>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Telepon</th>
            <th>Role</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->phone }}</td>
                <td><span class="badge text-bg-secondary">{{ $u->role }}</span></td>
                <td>
                    <span class="badge text-bg-{{ $u->is_active ? 'success' : 'secondary' }}">{{ $u->is_active ? 'aktif' : 'nonaktif' }}</span>
                </td>
                <td>
                    <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $users->links() }}
@endsection

