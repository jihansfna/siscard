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
        $status = $request->query('status');
        $sort = $request->query('sort', 'desc');
        
        $feedbacks = Feedback::with('member.employee')
            ->when($q, function($query, $q) {
                $query->whereHas('member.employee', function($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                       ->orWhere('badge', 'like', "%{$q}%");
                });
            })
            ->when($status && $status !== 'All Status', function($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('updated_at', $sort)
            ->paginate(10)
            ->withQueryString();
            
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

        return back()->with('success', 'Feedback successfully marked as Completed.');
    }

    // USER: Store a new feedback
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,zip|max:10240',
        ], [
            'document.max' => 'File size too large. Maximum 10MB.',
            'document.mimes' => 'Unsupported file format. Use PDF, Word, Excel, Image, or ZIP.',
        ]);

        $employee = Employee::where('badge', auth()->user()->badge)->first();
        if (!$employee) return back()->withErrors(['message' => 'You do not have access.']);

        // Block if employee is inactive
        if ($employee->end_date && Carbon::parse($employee->end_date)->lt(now()->startOfDay())) {
            return back()->withErrors(['message' => 'Your employment period has ended. Account deactivated.']);
        }
        
        $member = Member::where('employee_id', $employee->id)->first();
        if (!$member || $member->status !== 'registered') {
            return back()->withErrors(['message' => 'You are not registered as an active member.']);
        }

        $filePath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            
            // Double-check file validity
            if (!$file->isValid()) {
                return back()->withErrors(['document' => 'File upload failed. Ensure the file size does not exceed the maximum limit (10MB).'])->withInput();
            }
            
            $filePath = $file->store('feedbacks', 'public');
        }

        Feedback::create([
            'member_id' => $member->id,
            'description' => $request->description,
            'file' => $filePath,
            'status' => 'Waiting',
        ]);

        return back()->with('success', 'Feedback successfully submitted. Waiting for HRD response.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:feedbacks,id',
        ]);

        Feedback::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard.feedbacks')
            ->with('success', count($request->ids) . ' feedbacks successfully deleted.');
    }

    // USER: Delete a feedback
    public function destroyUser(Feedback $feedback)
    {
        $employee = Employee::where('badge', auth()->user()->badge)->first();
        if (!$employee) {
            abort(403, 'Unauthorized');
        }

        $member = Member::where('employee_id', $employee->id)->first();
        if (!$member || $feedback->member_id !== $member->id) {
            abort(403, 'Unauthorized');
        }

        // Delete attachment if present
        if ($feedback->file) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($feedback->file);
        }

        $feedback->delete();

        return back()->with('success', 'Your feedback was successfully deleted.');
    }
}
