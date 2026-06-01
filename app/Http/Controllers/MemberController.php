<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Employee;
use App\Models\MemberRole;
use App\Models\MemberLog;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status');
        $sort = $request->query('sort', 'desc');

        $sortDirection = $sort === 'asc' ? 'asc' : 'desc';

        // Optimized: removed 'logs.actor' — now loaded on-demand via AJAX
        $members = Member::with(['employee', 'role'])
            ->when($q, function($query, $q) {
                $query->whereHas('employee', function($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                       ->orWhere('badge', 'like', "%{$q}%");
                });
            })
            ->when($status && $status !== 'All Status', function($query) use ($status) {
                $statusMap = [
                    'Registered Member' => 'registered',
                    'Pending Verification' => 'pending',
                    'Inactive' => 'inactive'
                ];
                if (isset($statusMap[$status])) {
                    $query->where('status', $statusMap[$status]);
                }
            })
            ->orderBy('updated_at', $sortDirection)
            ->paginate(10);

        $today = now()->toDateString();

        // Optimized: only select columns needed for the modal table
        $availableEmployees = Employee::select('id', 'badge', 'name', 'department', 'line', 'position', 'end_date')
            ->whereNotIn('id', function($query) {
                $query->select('employee_id')->from('members')->whereNull('deleted_at');
            })
            ->where(function($query) use ($today) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $today);
            })
            ->get();

        $memberRoles = MemberRole::where('name', '!=', 'Member')->orderBy('name')->get();

        // Optimized: single query for Ketua & Sekretaris instead of 4 separate queries
        $signRoles = MemberRole::whereIn('name', ['Ketua', 'Sekretaris'])->pluck('id', 'name');
        $signMembers = Member::with('employee')
            ->whereIn('member_role_id', $signRoles->values())
            ->where('status', 'registered')
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('member_role_id');

        $ketua = $signMembers->get($signRoles->get('Ketua'));
        $sekretaris = $signMembers->get($signRoles->get('Sekretaris'));

        return view('dashboard.members', compact('members', 'availableEmployees', 'memberRoles', 'ketua', 'sekretaris', 'sort'));
    }

    /**
     * AJAX endpoint: fetch member logs on-demand (when drawer is opened)
     */
    public function logs(Member $member)
    {
        $logs = $member->logs()->with('actor')->orderBy('created_at', 'asc')->get();

        return response()->json($logs->map(function($log) {
            return [
                'activity' => $log->activity,
                'description' => $log->description,
                'actor_name' => $log->actor ? $log->actor->name : 'System',
                'actor_badge' => $log->actor ? $log->actor->badge : '',
                'created_at_date' => $log->created_at->format('l'),
                'created_at_time' => $log->created_at->format('d F Y, H.i'),
            ];
        }));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'member_role_id' => 'required|exists:member_roles,id',
            'sign_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $newRole = MemberRole::findOrFail($request->member_role_id);

        // Enforce is_single: if the new role is single-holder, demote the current holder
        if ($newRole->is_single) {
            $defaultRole = MemberRole::where('name', 'Member')->first();
            if ($defaultRole) {
                // Find old holders of this specific role
                $oldHolders = Member::where('member_role_id', $newRole->id)
                    ->where('id', '!=', $member->id)
                    ->whereNull('deleted_at')
                    ->get();
                
                foreach ($oldHolders as $oldHolder) {
                    // Delete signature file from storage
                    if ($oldHolder->sign_image) {
                        $oldPath = storage_path('app/public/' . $oldHolder->sign_image);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    
                    // Demote to Member and clear signature
                    $oldHolder->update([
                        'member_role_id' => $defaultRole->id,
                        'sign_image' => null
                    ]);
                }
            }
        }

        $data = [
            'member_role_id' => $request->member_role_id,
        ];

        // Handle signature upload
        if ($request->hasFile('sign_image')) {
            // Delete old signature if exists
            if ($member->sign_image) {
                $oldPath = storage_path('app/public/' . $member->sign_image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $data['sign_image'] = $request->file('sign_image')->store('signatures', 'public');
        }

        $member->update($data);

        MemberLog::create([
            'member_id' => $member->id,
            'actor_id' => auth()->id(),
            'activity' => 'Update',
            'status' => $member->status,
            'description' => 'Role updated to "' . $newRole->name . '"' . ($request->hasFile('sign_image') ? ' and signature updated.' : '.'),
        ]);

        $message = 'Member role successfully updated to "' . $newRole->name . '".';
        return back()->with('success', $message);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $today = now()->toDateString();

        // Filter out any inactive employees (end_date < today)
        $activeEmployees = Employee::whereIn('id', $request->employee_ids)
            ->where(function($query) use ($today) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $today);
            })
            ->pluck('id')
            ->toArray();

        $inactiveCount = count($request->employee_ids) - count($activeEmployees);

        if (empty($activeEmployees)) {
            return back()->withErrors(['employee_ids' => 'All selected employees are already inactive (end_date has passed).']);
        }

        $defaultRole = MemberRole::firstOrCreate(
            ['name' => 'Member'],
            ['is_single' => false, 'is_sign' => false]
        );

        $membersToInsert = [];
        foreach ($activeEmployees as $empId) {
            $membersToInsert[] = [
                'uuid' => Str::uuid(),
                'employee_id' => $empId,
                'member_role_id' => $defaultRole->id,
                'status' => 'pending',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Member::insert($membersToInsert);

        $insertedMembers = Member::whereIn('employee_id', $activeEmployees)->get();
        $logsToInsert = [];
        foreach ($insertedMembers as $m) {
            $logsToInsert[] = [
                'member_id' => $m->id,
                'actor_id' => auth()->id(),
                'activity' => 'Create',
                'status' => 'pending',
                'description' => 'Member added',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $logsToInsert[] = [
                'member_id' => $m->id,
                'actor_id' => auth()->id(),
                'activity' => 'Status Update',
                'status' => 'pending',
                'description' => 'Waiting for member confirmation',
                'created_at' => now()->addSecond(),
                'updated_at' => now()->addSecond(),
            ];
        }
        MemberLog::insert($logsToInsert);

        $message = count($activeEmployees) . ' Members successfully added.';
        if ($inactiveCount > 0) {
            $message .= ' ' . $inactiveCount . ' employees skipped because they are inactive.';
        }

        return back()->with('success', $message);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:members,id',
        ]);

        $members = Member::with('employee')->whereIn('id', $request->ids)->get();
        $logsToInsert = [];
        foreach ($members as $m) {
            $logsToInsert[] = [
                'member_id' => $m->id,
                'actor_id' => auth()->id(),
                'activity' => 'Delete',
                'status' => $m->status,
                'description' => 'Deleting member data: ' . ($m->employee ? $m->employee->name : 'Unknown'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        MemberLog::insert($logsToInsert);

        Member::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard.members')
            ->with('success', count($request->ids) . ' members successfully deleted.');
    }
}
