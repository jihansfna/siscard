<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Feedback;
use App\Models\Employee;
use App\Models\Member;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    // ADMIN: View all feedbacks
    public function indexAdmin(Request $request)
    {
        $q = $request->query('q');
        
        $feedbacks = Feedback::with('member.employee')
            ->when($q, function($query, $q) {
                $query->whereHas('member.employee', function($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                       ->orWhere('badge', 'like', "%{$q}%");
                });
            })
            ->orderBy('created_at', 'asc')
            ->paginate(10);
            
        return view('dashboard.feedbacks', compact('feedbacks'));
    }

    // ADMIN: Mark feedback as completed with remark
    public function complete(Request $request, Feedback $feedback)
    {
        $request->validate([
            'remark' => 'required|string',
        ]);

        $feedback->update([
            'status' => 'Completed',
            'remark' => $request->remark,
        ]);

        return back()->with('success', 'Saran berhasil ditandai sebagai Completed.');
    }

    // USER: Store a new feedback
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $employee = Employee::where('badge', auth()->user()->badge)->first();
        if (!$employee) return back()->withErrors(['message' => 'Anda tidak memiliki akses.']);

        // Block if employee is inactive
        if ($employee->end_date && Carbon::parse($employee->end_date)->lt(now()->startOfDay())) {
            return back()->withErrors(['message' => 'Masa kerja Anda telah berakhir. Akun dinonaktifkan.']);
        }
        
        $member = Member::where('employee_id', $employee->id)->first();
        if (!$member || $member->status !== 'registered') {
            return back()->withErrors(['message' => 'Anda belum terdaftar sebagai member aktif.']);
        }

        Feedback::create([
            'member_id' => $member->id,
            'description' => $request->description,
            'status' => 'Waiting',
        ]);

        return back()->with('success', 'Saran berhasil dikirim. Menunggu respon HRD.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:feedbacks,id',
        ]);

        Feedback::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard.feedbacks')
            ->with('success', count($request->ids) . ' saran berhasil dihapus.');
    }
}
