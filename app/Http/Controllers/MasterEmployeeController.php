<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MasterEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $employees = Employee::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('badge', 'like', "%{$search}%");
        })->orderBy('created_at', 'asc')->paginate(10);

        return view('dashboard.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('dashboard.employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'badge' => 'required|string|unique:employees,badge',
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'line' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:join_date',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date|before:join_date',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('employees', 'public');
        }

        Employee::create($validated);

        // Create associated user account with default password
        User::firstOrCreate(
            ['badge' => $validated['badge']],
            [
                'name' => $validated['name'],
                'password' => Hash::make('P4ssword'),
                'role' => 'user'
            ]
        );

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Employee successfully added.');
    }

    public function edit(Employee $employee)
    {
        return view('dashboard.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'badge' => 'required|string|unique:employees,badge,' . $employee->id,
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'line' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:join_date',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date|before:join_date',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($employee->image) {
                Storage::disk('public')->delete($employee->image);
            }
            $validated['image'] = $request->file('image')->store('employees', 'public');
        }

        $oldBadge = $employee->badge;
        $employee->update($validated);

        // Update associated user account if it exists
        $user = User::where('badge', $oldBadge)->first();
        if ($user) {
            $user->update([
                'badge' => $validated['badge'],
                'name' => $validated['name'],
            ]);
        }

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Employee data successfully updated.');
    }

    public function destroy(Employee $employee)
    {
        $badge = $employee->badge;
        $employee->delete();
        
        // Also delete the associated user account
        User::where('badge', $badge)->delete();

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Employee data successfully deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:employees,id',
        ]);

        $employees = Employee::whereIn('id', $request->ids)->get();
        $badges = $employees->pluck('badge')->toArray();

        // Delete associated user accounts
        User::whereIn('badge', $badges)->delete();

        // Delete the employees
        Employee::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard.employees.index')
            ->with('success', count($request->ids) . ' employee data successfully deleted.');
    }

    public function setInactive(Employee $employee)
    {
        // 1. Set employee end_date to yesterday so they become inactive immediately
        $employee->update(['end_date' => now()->subDay()->startOfDay()]);

        // 2. Set Member status to inactive
        $member = Member::where('employee_id', $employee->id)->first();
        if ($member) {
            $member->update(['status' => 'inactive']);
        }

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Employee ' . $employee->name . ' has been deactivated and login access revoked.');
    }
}
