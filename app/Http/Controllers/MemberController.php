<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status');

        $members = \App\Models\Member::with(['employee', 'role'])
            ->when($q, function($query, $q) {
                $query->whereHas('employee', function($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                       ->orWhere('badge', 'like', "%{$q}%");
                });
            })
            ->when($status && $status !== 'All Status', function($query) use ($status) {
                // Map frontend status to database enum
                $statusMap = [
                    'Registered Member' => 'registered',
                    'Pending Verification' => 'pending',
                    'Inactive' => 'inactive'
                ];
                if (isset($statusMap[$status])) {
                    $query->where('status', $statusMap[$status]);
                }
            })
            ->latest()
            ->paginate(10);

        $availableEmployees = \App\Models\Employee::whereNotIn('id', function($query) {
            $query->select('employee_id')->from('members')->whereNull('deleted_at');
        })->get();

        return view('dashboard.members', compact('members', 'availableEmployees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $defaultRole = \App\Models\MemberRole::firstOrCreate(
            ['name' => 'Member'],
            ['is_single' => false, 'is_sign' => false]
        );

        $membersToInsert = [];
        foreach ($request->employee_ids as $empId) {
            $membersToInsert[] = [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'employee_id' => $empId,
                'member_role_id' => $defaultRole->id,
                'status' => 'pending',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \App\Models\Member::insert($membersToInsert);

        return back()->with('success', count($request->employee_ids) . ' Member berhasil ditambahkan.');
    }
}
