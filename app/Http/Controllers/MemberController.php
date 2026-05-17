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
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        $today = now()->toDateString();

        $availableEmployees = \App\Models\Employee::whereNotIn('id', function($query) {
            $query->select('employee_id')->from('members')->whereNull('deleted_at');
        })
        ->where(function($query) use ($today) {
            // Include employees with no end_date OR end_date >= today
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today);
        })
        ->get();

        return view('dashboard.members', compact('members', 'availableEmployees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $today = now()->toDateString();

        // Filter out any inactive employees (end_date < today)
        $activeEmployees = \App\Models\Employee::whereIn('id', $request->employee_ids)
            ->where(function($query) use ($today) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $today);
            })
            ->pluck('id')
            ->toArray();

        $inactiveCount = count($request->employee_ids) - count($activeEmployees);

        if (empty($activeEmployees)) {
            return back()->withErrors(['employee_ids' => 'Semua employee yang dipilih sudah tidak aktif (end_date sudah lewat).']);
        }

        $defaultRole = \App\Models\MemberRole::firstOrCreate(
            ['name' => 'Member'],
            ['is_single' => false, 'is_sign' => false]
        );

        $membersToInsert = [];
        foreach ($activeEmployees as $empId) {
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

        $message = count($activeEmployees) . ' Member berhasil ditambahkan.';
        if ($inactiveCount > 0) {
            $message .= ' ' . $inactiveCount . ' employee dilewati karena sudah tidak aktif.';
        }

        return back()->with('success', $message);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:members,id',
        ]);

        \App\Models\Member::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard.members')
            ->with('success', count($request->ids) . ' member berhasil dihapus.');
    }
}
