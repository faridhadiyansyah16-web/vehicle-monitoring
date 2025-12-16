<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Approval;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $q = Booking::with(['vehicle','driver','user','approvals.approver'])->orderByDesc('id');
        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        if ($request->filled('vehicle_id')) {
            $q->where('vehicle_id', $request->integer('vehicle_id'));
        }
        if ($request->filled('from')) {
            $q->where('start_time', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $q->where('start_time', '<=', $request->date('to')->setTime(23,59,59));
        }
        $bookings = $q->paginate(15)->appends($request->query());
        $vehicles = Vehicle::orderBy('plate_number')->get(['id','plate_number']);
        return view('bookings.index', compact('bookings','vehicles'));
    }

    public function create()
    {
        $vehicles = Vehicle::where('status', 'available')->orderBy('plate_number')->get();
        $drivers = Driver::where('status', 'active')->orderBy('name')->get();
        $approvers = User::where('role', 'approver')->where('is_active', true)->orderBy('name')->get();
        return view('bookings.create', compact('vehicles','drivers','approvers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['required','exists:vehicles,id'],
            'driver_id' => ['nullable','exists:drivers,id'],
            'start_time' => ['required','date'],
            'end_time' => ['nullable','date','after_or_equal:start_time'],
            'purpose' => ['nullable','string'],
            'origin' => ['nullable','string'],
            'destination' => ['nullable','string'],
            'approver_ids' => ['required','array','min:2'],
            'approver_ids.*' => ['exists:users,id'],
        ]);

        return DB::transaction(function () use ($data) {
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'] ?? null,
                'purpose' => $data['purpose'] ?? null,
                'origin' => $data['origin'] ?? null,
                'destination' => $data['destination'] ?? null,
                'status' => 'pending',
            ]);

            $level = 1;
            foreach ($data['approver_ids'] as $approverId) {
                Approval::create([
                    'booking_id' => $booking->id,
                    'approver_id' => $approverId,
                    'level' => $level++,
                    'status' => 'pending',
                ]);
            }

            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'create_booking',
                'model_type' => Booking::class,
                'model_id' => $booking->id,
                'message' => 'Pemesanan kendaraan dibuat',
            ]);

            return redirect()->route('bookings.index')->with('success', 'Pemesanan dibuat');
        });
    }

    public function complete(Request $request, Booking $booking)
    {
        if (!auth()->check() || (auth()->id() !== $booking->user_id && !auth()->user()->isAdmin())) {
            abort(403);
        }
        $data = $request->validate([
            'end_time' => ['required','date','after_or_equal:start_time'],
            'distance_km' => ['nullable','integer','min:0'],
            'fuel_consumed_l' => ['nullable','numeric','min:0'],
        ]);
        $booking->update($data + ['status' => 'completed']);
        $booking->vehicle()->update(['status' => 'available']);
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'complete_booking',
            'model_type' => Booking::class,
            'model_id' => $booking->id,
            'message' => 'Pemesanan diselesaikan',
        ]);
        return redirect()->route('bookings.index')->with('success', 'Pemesanan diselesaikan');
    }
}
