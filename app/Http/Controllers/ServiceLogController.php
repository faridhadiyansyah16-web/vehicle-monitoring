<?php

namespace App\Http\Controllers;

use App\Models\ServiceLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ServiceLogController extends Controller
{
    public function index(Vehicle $vehicle)
    {
        $logs = ServiceLog::where('vehicle_id', $vehicle->id)->orderByDesc('date')->paginate(15);
        return view('service_logs.index', compact('vehicle','logs'));
    }

    public function create(Vehicle $vehicle)
    {
        return view('service_logs.create', compact('vehicle'));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'date' => ['required','date'],
            'service_type' => ['nullable','string'],
            'cost' => ['nullable','numeric','min:0'],
            'odometer' => ['nullable','integer','min:0'],
            'description' => ['nullable','string'],
        ]);
        ServiceLog::create($data + ['vehicle_id' => $vehicle->id]);
        return redirect()->route('service_logs.index', $vehicle)->with('success', 'Log servis ditambahkan');
    }
}

