<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\ServiceLog;

class ReportController extends Controller
{
    public function bookingsExcel(Request $request)
    {
        $q = Booking::with(['user','vehicle','driver'])->orderBy('id');
        if ($request->filled('status')) $q->where('status', $request->string('status'));
        if ($request->filled('vehicle_id')) $q->where('vehicle_id', $request->integer('vehicle_id'));
        if ($request->filled('from')) $q->where('start_time', '>=', $request->date('from'));
        if ($request->filled('to')) $q->where('start_time', '<=', $request->date('to')->setTime(23,59,59));
        $rows = $q->get();

        $sheet = new Spreadsheet();
        $ws = $sheet->getActiveSheet();
        $ws->setTitle('Bookings');

        $headers = ['ID','Pemesan','Kendaraan','Driver','Mulai','Selesai','Tujuan','Status','Jarak (km)','BBM (L)'];
        foreach ($headers as $i => $h) {
            $ws->setCellValueByColumnAndRow($i + 1, 1, $h);
        }
        $ws->getStyle('A1:J1')->getFont()->setBold(true);
        $r = 2;
        foreach ($rows as $row) {
            $ws->setCellValueByColumnAndRow(1, $r, $row->id);
            $ws->setCellValueByColumnAndRow(2, $r, $row->user?->name);
            $ws->setCellValueByColumnAndRow(3, $r, $row->vehicle?->plate_number);
            $ws->setCellValueByColumnAndRow(4, $r, $row->driver?->name);
            $ws->setCellValueByColumnAndRow(5, $r, optional($row->start_time)->format('Y-m-d H:i'));
            $ws->setCellValueByColumnAndRow(6, $r, optional($row->end_time)->format('Y-m-d H:i'));
            $ws->setCellValueByColumnAndRow(7, $r, $row->destination);
            $ws->setCellValueByColumnAndRow(8, $r, $row->status);
            $ws->setCellValueByColumnAndRow(9, $r, $row->distance_km);
            $ws->setCellValueByColumnAndRow(10, $r, $row->fuel_consumed_l);
            $r++;
        }
        $ws->getStyle('E2:E'.$r)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');
        $ws->getStyle('F2:F'.$r)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');
        $ws->getStyle('I2:I'.$r)->getNumberFormat()->setFormatCode('0');
        $ws->getStyle('J2:J'.$r)->getNumberFormat()->setFormatCode('0.00');

        foreach (range('A','J') as $col) {
            $ws->getColumnDimension($col)->setAutoSize(true);
        }
        $ws->freezePane('A2');

        // Summary sheet by status
        $summarySheet = $sheet->createSheet(1)->setTitle('Summary');
        $summarySheet->setCellValue('A1', 'Status');
        $summarySheet->setCellValue('B1', 'Jumlah');
        $summarySheet->getStyle('A1:B1')->getFont()->setBold(true);
        $statuses = ['pending','approved','rejected','completed','cancelled'];
        $sr = 2;
        foreach ($statuses as $st) {
            $summarySheet->setCellValue('A'.$sr, ucfirst($st));
            $summarySheet->setCellValue('B'.$sr, $rows->where('status', $st)->count());
            $sr++;
        }
        $summarySheet->getColumnDimension('A')->setAutoSize(true);
        $summarySheet->getColumnDimension('B')->setAutoSize(true);

        $filename = 'bookings_'.now()->format('Ymd_His').'.xlsx';
        return response()->streamDownload(function () use ($sheet) {
            $writer = new Xlsx($sheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function vehiclesExcel()
    {
        $rows = Vehicle::orderBy('plate_number')->get();
        $sheet = new Spreadsheet();
        $ws = $sheet->getActiveSheet();
        $ws->setTitle('Vehicles');
        $headers = ['ID','Plat','Tipe','Kapasitas','BBM','Milik Perusahaan','Status','Next Service','Odometer'];
        foreach ($headers as $i => $h) $ws->setCellValueByColumnAndRow($i + 1, 1, $h);
        $ws->getStyle('A1:I1')->getFont()->setBold(true);
        $r = 2;
        foreach ($rows as $row) {
            $ws->setCellValueByColumnAndRow(1, $r, $row->id);
            $ws->setCellValueByColumnAndRow(2, $r, $row->plate_number);
            $ws->setCellValueByColumnAndRow(3, $r, $row->type);
            $ws->setCellValueByColumnAndRow(4, $r, $row->capacity);
            $ws->setCellValueByColumnAndRow(5, $r, $row->fuel_type);
            $ws->setCellValueByColumnAndRow(6, $r, $row->is_company_owned ? 'Ya' : 'Tidak');
            $ws->setCellValueByColumnAndRow(7, $r, $row->status);
            $ws->setCellValueByColumnAndRow(8, $r, optional($row->next_service_date)->format('Y-m-d'));
            $ws->setCellValueByColumnAndRow(9, $r, $row->odometer);
            $r++;
        }
        $ws->getStyle('H2:H'.$r)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $ws->getStyle('D2:D'.$r)->getNumberFormat()->setFormatCode('0');
        $ws->getStyle('I2:I'.$r)->getNumberFormat()->setFormatCode('0');
        foreach (range('A','I') as $col) $ws->getColumnDimension($col)->setAutoSize(true);
        $ws->freezePane('A2');
        $filename = 'vehicles_'.now()->format('Ymd_His').'.xlsx';
        return response()->streamDownload(function () use ($sheet) {
            $writer = new Xlsx($sheet);
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function driversExcel()
    {
        $rows = Driver::orderBy('name')->get();
        $sheet = new Spreadsheet();
        $ws = $sheet->getActiveSheet();
        $ws->setTitle('Drivers');
        $headers = ['ID','Nama','Telepon','Lisensi','Status'];
        foreach ($headers as $i => $h) $ws->setCellValueByColumnAndRow($i + 1, 1, $h);
        $ws->getStyle('A1:E1')->getFont()->setBold(true);
        $r = 2;
        foreach ($rows as $row) {
            $ws->setCellValueByColumnAndRow(1, $r, $row->id);
            $ws->setCellValueByColumnAndRow(2, $r, $row->name);
            $ws->setCellValueByColumnAndRow(3, $r, $row->phone);
            $ws->setCellValueByColumnAndRow(4, $r, $row->license_number);
            $ws->setCellValueByColumnAndRow(5, $r, $row->status);
            $r++;
        }
        foreach (range('A','E') as $col) $ws->getColumnDimension($col)->setAutoSize(true);
        $ws->freezePane('A2');
        $filename = 'drivers_'.now()->format('Ymd_His').'.xlsx';
        return response()->streamDownload(function () use ($sheet) {
            $writer = new Xlsx($sheet);
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function fuelLogsExcel(Request $request)
    {
        $q = FuelLog::with('vehicle')->orderByDesc('date');
        if ($request->filled('vehicle_id')) $q->where('vehicle_id', $request->integer('vehicle_id'));
        if ($request->filled('from')) $q->where('date', '>=', $request->date('from'));
        if ($request->filled('to')) $q->where('date', '<=', $request->date('to'));
        $rows = $q->get();
        $sheet = new Spreadsheet();
        $ws = $sheet->getActiveSheet();
        $ws->setTitle('Fuel Logs');
        $headers = ['ID','Plat','Tanggal','Liter','Biaya','Odometer','Catatan'];
        foreach ($headers as $i => $h) $ws->setCellValueByColumnAndRow($i + 1, 1, $h);
        $ws->getStyle('A1:G1')->getFont()->setBold(true);
        $r = 2;
        foreach ($rows as $row) {
            $ws->setCellValueByColumnAndRow(1, $r, $row->id);
            $ws->setCellValueByColumnAndRow(2, $r, $row->vehicle?->plate_number);
            $ws->setCellValueByColumnAndRow(3, $r, optional($row->date)->format('Y-m-d'));
            $ws->setCellValueByColumnAndRow(4, $r, $row->liters);
            $ws->setCellValueByColumnAndRow(5, $r, $row->cost);
            $ws->setCellValueByColumnAndRow(6, $r, $row->odometer);
            $ws->setCellValueByColumnAndRow(7, $r, $row->note);
            $r++;
        }
        $ws->getStyle('C2:C'.$r)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $ws->getStyle('D2:D'.$r)->getNumberFormat()->setFormatCode('0.00');
        $ws->getStyle('E2:E'.$r)->getNumberFormat()->setFormatCode('#,##0.00');
        $ws->getStyle('F2:F'.$r)->getNumberFormat()->setFormatCode('0');
        foreach (range('A','G') as $col) $ws->getColumnDimension($col)->setAutoSize(true);
        $ws->freezePane('A2');
        $filename = 'fuel_logs_'.now()->format('Ymd_His').'.xlsx';
        return response()->streamDownload(function () use ($sheet) {
            $writer = new Xlsx($sheet);
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function serviceLogsExcel(Request $request)
    {
        $q = ServiceLog::with('vehicle')->orderByDesc('date');
        if ($request->filled('vehicle_id')) $q->where('vehicle_id', $request->integer('vehicle_id'));
        if ($request->filled('from')) $q->where('date', '>=', $request->date('from'));
        if ($request->filled('to')) $q->where('date', '<=', $request->date('to'));
        $rows = $q->get();
        $sheet = new Spreadsheet();
        $ws = $sheet->getActiveSheet();
        $ws->setTitle('Service Logs');
        $headers = ['ID','Plat','Tanggal','Jenis Servis','Biaya','Odometer','Deskripsi'];
        foreach ($headers as $i => $h) $ws->setCellValueByColumnAndRow($i + 1, 1, $h);
        $ws->getStyle('A1:G1')->getFont()->setBold(true);
        $r = 2;
        foreach ($rows as $row) {
            $ws->setCellValueByColumnAndRow(1, $r, $row->id);
            $ws->setCellValueByColumnAndRow(2, $r, $row->vehicle?->plate_number);
            $ws->setCellValueByColumnAndRow(3, $r, optional($row->date)->format('Y-m-d'));
            $ws->setCellValueByColumnAndRow(4, $r, $row->service_type);
            $ws->setCellValueByColumnAndRow(5, $r, $row->cost);
            $ws->setCellValueByColumnAndRow(6, $r, $row->odometer);
            $ws->setCellValueByColumnAndRow(7, $r, $row->description);
            $r++;
        }
        $ws->getStyle('C2:C'.$r)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $ws->getStyle('E2:E'.$r)->getNumberFormat()->setFormatCode('#,##0.00');
        $ws->getStyle('F2:F'.$r)->getNumberFormat()->setFormatCode('0');
        foreach (range('A','G') as $col) $ws->getColumnDimension($col)->setAutoSize(true);
        $ws->freezePane('A2');
        $filename = 'service_logs_'.now()->format('Ymd_His').'.xlsx';
        return response()->streamDownload(function () use ($sheet) {
            $writer = new Xlsx($sheet);
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function usageExcel(Request $request)
    {
        $months = (int)($request->input('months') ?: 6);
        $start = now()->copy()->subMonths($months - 1)->startOfMonth();
        $end = now()->copy()->endOfMonth();
        $agg = Booking::selectRaw('YEAR(start_time) as y, MONTH(start_time) as m, COUNT(*) as c')
            ->whereBetween('start_time', [$start, $end])
            ->groupBy(\DB::raw('YEAR(start_time), MONTH(start_time)'))
            ->orderBy(\DB::raw('YEAR(start_time)'))
            ->orderBy(\DB::raw('MONTH(start_time)'))
            ->get()
            ->mapWithKeys(function ($r) {
                return [sprintf('%04d-%02d', $r->y, $r->m) => (int) $r->c];
            })
            ->all();

        $labels = [];
        $counts = [];
        for ($i = 0; $i < $months; $i++) {
            $d = $start->copy()->addMonths($i);
            $key = $d->format('Y-m');
            $labels[] = $d->locale('id')->isoFormat('MMM YYYY');
            $counts[] = (int)($agg[$key] ?? 0);
        }

        $sheet = new Spreadsheet();
        $ws = $sheet->getActiveSheet();
        $ws->setTitle('Usage');
        $ws->setCellValue('A1', 'Periode');
        $ws->setCellValue('B1', 'Jumlah Pemakaian');
        $ws->getStyle('A1:B1')->getFont()->setBold(true);
        $r = 2;
        for ($i = 0; $i < count($labels); $i++) {
            $ws->setCellValueByColumnAndRow(1, $r, $labels[$i]);
            $ws->setCellValueByColumnAndRow(2, $r, $counts[$i]);
            $r++;
        }
        $ws->getStyle('B2:B'.$r)->getNumberFormat()->setFormatCode('0');
        $ws->setCellValue('A'.$r, 'Total');
        $ws->setCellValue('B'.$r, array_sum($counts));
        $ws->getStyle('A'.$r.':B'.$r)->getFont()->setBold(true);
        $ws->getColumnDimension('A')->setAutoSize(true);
        $ws->getColumnDimension('B')->setAutoSize(true);
        $ws->freezePane('A2');

        $filename = 'usage_'.$start->format('Ymd').'_'.$end->format('Ymd').'.xlsx';
        return response()->streamDownload(function () use ($sheet) {
            $writer = new Xlsx($sheet);
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
