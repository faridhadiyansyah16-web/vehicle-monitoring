<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FuelLogController extends Controller
{
    public function index(Vehicle $vehicle)
    {
        $logs = FuelLog::where('vehicle_id', $vehicle->id)->orderByDesc('date')->paginate(15);
        return view('fuel_logs.index', compact('vehicle','logs'));
    }

    public function create(Vehicle $vehicle)
    {
        return view('fuel_logs.create', compact('vehicle'));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'date' => ['required','date'],
            'liters' => ['required','numeric','min:0'],
            'cost' => ['nullable','numeric','min:0'],
            'odometer' => ['nullable','integer','min:0'],
            'note' => ['nullable','string'],
        ]);
        FuelLog::create($data + ['vehicle_id' => $vehicle->id]);
        return redirect()->route('fuel_logs.index', $vehicle)->with('success', 'Log BBM ditambahkan');
    }
}

