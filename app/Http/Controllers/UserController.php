<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Member;
use App\Models\Feedback;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = Employee::where('badge', $user->badge)->first();
        
        $accessStatus = 'no_access';
        $memberId = null;

        $feedbacks = collect();

        if ($employee) {
            // Check if employee has passed their end_date
            if ($employee->end_date && Carbon::parse($employee->end_date)->lt(now()->startOfDay())) {
                $accessStatus = 'inactive';
            } else {
                $member = Member::where('employee_id', $employee->id)->first();
                if ($member) {
                    $accessStatus = $member->status; // 'pending' or 'registered' etc.
                    $memberId = $member->id;
                    
                    if ($accessStatus === 'registered') {
                        $feedbacks = Feedback::where('member_id', $memberId)->latest()->get();
                    }
                }
            }
        }

        return view('home', compact('accessStatus', 'memberId', 'feedbacks', 'employee'));
    }

    public function confirmMembership(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        
        // Pastikan member ini milik user yang sedang login
        $employee = Employee::where('badge', auth()->user()->badge)->first();
        if (!$employee || $member->employee_id !== $employee->id) {
            abort(403);
        }

        // Pastikan karyawan masih aktif
        if ($employee->end_date && Carbon::parse($employee->end_date)->lt(now()->startOfDay())) {
            abort(403, 'Masa kerja Anda telah berakhir.');
        }

        $member->update(['status' => 'registered']);

        return redirect()->route('user.home')->with('success', 'Berhasil mengkonfirmasi keanggotaan. Selamat datang!');
    }
}
