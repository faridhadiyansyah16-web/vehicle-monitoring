<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    protected function ensureAdmin(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    public function index()
    {
        $vehicles = Vehicle::orderBy('plate_number')->paginate(15);
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'plate_number' => ['required','string','unique:vehicles,plate_number'],
            'type' => ['required','string'],
            'capacity' => ['nullable','integer'],
            'fuel_type' => ['nullable','in:diesel,gasoline,electric'],
        ]);
        Vehicle::create($data + ['status' => 'available', 'is_company_owned' => true]);
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan ditambahkan');
    }

    public function edit(Vehicle $vehicle)
    {
        $this->ensureAdmin();
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'type' => ['required','string'],
            'capacity' => ['nullable','integer'],
            'fuel_type' => ['nullable','in:diesel,gasoline,electric'],
            'status' => ['required','in:available,in_use,maintenance,inactive'],
            'next_service_date' => ['nullable','date'],
            'odometer' => ['nullable','integer'],
        ]);
        $vehicle->update($data);
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan diperbarui');
    }
}

