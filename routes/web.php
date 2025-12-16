<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FuelLogController;
use App\Http\Controllers\ServiceLogController;

Route::get('/', fn () => redirect('/dashboard'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');

    Route::post('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');

    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
    Route::get('/drivers/create', [DriverController::class, 'create'])->name('drivers.create');
    Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
    Route::get('/drivers/{driver}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
    Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');

    Route::get('/vehicles/{vehicle}/fuel-logs', [FuelLogController::class, 'index'])->name('fuel_logs.index');
    Route::get('/vehicles/{vehicle}/fuel-logs/create', [FuelLogController::class, 'create'])->name('fuel_logs.create');
    Route::post('/vehicles/{vehicle}/fuel-logs', [FuelLogController::class, 'store'])->name('fuel_logs.store');

    Route::get('/vehicles/{vehicle}/service-logs', [ServiceLogController::class, 'index'])->name('service_logs.index');
    Route::get('/vehicles/{vehicle}/service-logs/create', [ServiceLogController::class, 'create'])->name('service_logs.create');
    Route::post('/vehicles/{vehicle}/service-logs', [ServiceLogController::class, 'store'])->name('service_logs.store');

    Route::get('/reports/bookings.csv', function (\Illuminate\Http\Request $request) {
        $filename = 'bookings_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID','Pemesan','Kendaraan','Driver','Mulai','Selesai','Tujuan','Status','Jarak (km)','BBM (L)']);
            $q = \App\Models\Booking::with(['user','vehicle','driver'])->orderBy('id');
            if ($request->filled('status')) $q->where('status', $request->string('status'));
            if ($request->filled('vehicle_id')) $q->where('vehicle_id', $request->integer('vehicle_id'));
            if ($request->filled('from')) $q->where('start_time', '>=', $request->date('from'));
            if ($request->filled('to')) $q->where('start_time', '<=', $request->date('to')->setTime(23,59,59));
            $rows = $q->get();
            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->user?->name,
                    $r->vehicle?->plate_number,
                    $r->driver?->name,
                    optional($r->start_time)->format('Y-m-d H:i'),
                    optional($r->end_time)->format('Y-m-d H:i'),
                    $r->destination,
                    $r->status,
                    $r->distance_km,
                    $r->fuel_consumed_l,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    })->name('reports.bookings');

    Route::get('/reports/bookings.xlsx', [\App\Http\Controllers\ReportController::class, 'bookingsExcel'])->name('reports.bookings_excel');
    Route::get('/reports/vehicles.xlsx', [\App\Http\Controllers\ReportController::class, 'vehiclesExcel'])->name('reports.vehicles_excel');
    Route::get('/reports/drivers.xlsx', [\App\Http\Controllers\ReportController::class, 'driversExcel'])->name('reports.drivers_excel');
    Route::get('/reports/fuel_logs.xlsx', [\App\Http\Controllers\ReportController::class, 'fuelLogsExcel'])->name('reports.fuel_logs_excel');
    Route::get('/reports/service_logs.xlsx', [\App\Http\Controllers\ReportController::class, 'serviceLogsExcel'])->name('reports.service_logs_excel');
    Route::get('/reports/usage.xlsx', [\App\Http\Controllers\ReportController::class, 'usageExcel'])->name('reports.usage_excel');

    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
});
