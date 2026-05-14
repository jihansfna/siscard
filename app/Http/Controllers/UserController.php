<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = \App\Models\Employee::where('badge', $user->badge)->first();
        
        $accessStatus = 'no_access';
        $memberId = null;

        $feedbacks = collect();

        if ($employee) {
            $member = \App\Models\Member::where('employee_id', $employee->id)->first();
            if ($member) {
                $accessStatus = $member->status; // 'pending' or 'registered' etc.
                $memberId = $member->id;
                
                if ($accessStatus === 'registered') {
                    $feedbacks = \App\Models\Feedback::where('member_id', $memberId)->latest()->get();
                }
            }
        }

        return view('user', compact('accessStatus', 'memberId', 'feedbacks'));
    }

    public function confirmMembership(Request $request, $id)
    {
        $member = \App\Models\Member::findOrFail($id);
        
        // Pastikan member ini milik user yang sedang login
        $employee = \App\Models\Employee::where('badge', auth()->user()->badge)->first();
        if (!$employee || $member->employee_id !== $employee->id) {
            abort(403);
        }

        $member->update(['status' => 'registered']);

        return redirect()->route('user.home')->with('success', 'Berhasil mengkonfirmasi keanggotaan. Selamat datang!');
    }
}
