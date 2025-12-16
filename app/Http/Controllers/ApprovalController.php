<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Approval;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index()
    {
        $approvals = Approval::with(['booking.vehicle','booking.user'])
            ->where('approver_id', auth()->id())
            ->orderByRaw("CASE WHEN status='pending' THEN 0 ELSE 1 END")
            ->orderBy('id','desc')
            ->paginate(15);
        return view('approvals.index', compact('approvals'));
    }
    public function approve(Request $request, Approval $approval)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'approver' || $approval->approver_id !== $user->id) {
            abort(403);
        }

        return DB::transaction(function () use ($approval, $user) {
            $booking = $approval->booking()->with('approvals')->first();
            $firstPending = $booking->approvals()->where('status','pending')->orderBy('level')->first();
            if (!$firstPending || $firstPending->id !== $approval->id) {
                return back()->with('success', 'Menunggu persetujuan level sebelumnya');
            }
            $approval->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            $allApproved = $booking->approvals()->where('status', 'approved')->count() === $booking->approvals()->count();
            if ($allApproved) {
                $booking->update(['status' => 'approved']);
                Vehicle::where('id', $booking->vehicle_id)->update(['status' => 'in_use']);
            }

            Activity::create([
                'user_id' => $user->id,
                'action' => 'approve_booking',
                'model_type' => Approval::class,
                'model_id' => $approval->id,
                'message' => 'Pemesanan disetujui',
            ]);

            return back()->with('success', 'Disetujui');
        });
    }

    public function reject(Request $request, Approval $approval)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'approver' || $approval->approver_id !== $user->id) {
            abort(403);
        }

        return DB::transaction(function () use ($approval, $user) {
            $booking = $approval->booking()->with('approvals')->first();
            $firstPending = $booking->approvals()->where('status','pending')->orderBy('level')->first();
            if (!$firstPending || $firstPending->id !== $approval->id) {
                return back()->with('success', 'Menunggu persetujuan level sebelumnya');
            }
            $approval->update([
                'status' => 'rejected',
                'approved_at' => now(),
                'note' => request('note'),
            ]);

            $booking->update(['status' => 'rejected']);

            Activity::create([
                'user_id' => $user->id,
                'action' => 'reject_booking',
                'model_type' => Approval::class,
                'model_id' => $approval->id,
                'message' => 'Pemesanan ditolak',
            ]);

            return back()->with('success', 'Ditolak');
        });
    }
}
