<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected function ensureAdmin()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function index()
    {
        $this->ensureAdmin();
        $users = User::orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->ensureAdmin();
        $roles = ['admin','approver','user'];
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:120','unique:users,email'],
            'phone' => ['nullable','string','max:30'],
            'role' => ['required','in:admin,approver,user'],
            'is_active' => ['nullable','boolean'],
            'password' => ['required','string','min:6'],
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_active' => (bool)($data['is_active'] ?? true),
            'password' => Hash::make($data['password']),
        ]);
        return redirect()->route('users.index')->with('success', 'User dibuat');
    }

    public function edit(User $user)
    {
        $this->ensureAdmin();
        $roles = ['admin','approver','user'];
        return view('users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:120','unique:users,email,'.$user->id],
            'phone' => ['nullable','string','max:30'],
            'role' => ['required','in:admin,approver,user'],
            'is_active' => ['nullable','boolean'],
            'password' => ['nullable','string','min:6'],
        ]);
        $update = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_active' => (bool)($data['is_active'] ?? false),
        ];
        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }
        $user->update($update);
        return redirect()->route('users.index')->with('success', 'User diperbarui');
    }
}

