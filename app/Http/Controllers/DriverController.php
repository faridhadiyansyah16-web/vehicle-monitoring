<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    protected function ensureAdmin(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function index()
    {
        $drivers = Driver::orderBy('name')->paginate(15);
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => ['required','string'],
            'phone' => ['nullable','string'],
            'license_number' => ['nullable','string'],
        ]);
        Driver::create($data + ['status' => 'active']);
        return redirect()->route('drivers.index')->with('success', 'Driver ditambahkan');
    }

    public function edit(Driver $driver)
    {
        $this->ensureAdmin();
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => ['required','string'],
            'phone' => ['nullable','string'],
            'license_number' => ['nullable','string'],
            'status' => ['required','in:active,inactive'],
        ]);
        $driver->update($data);
        return redirect()->route('drivers.index')->with('success', 'Driver diperbarui');
    }
}

