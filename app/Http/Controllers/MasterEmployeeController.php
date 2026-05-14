<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MasterEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $employees = \App\Models\Employee::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('badge', 'like', "%{$search}%");
        })->orderBy('created_at', 'desc')->paginate(10);

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
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        \App\Models\Employee::create($validated);

        // Create associated user account with default password
        \App\Models\User::firstOrCreate(
            ['badge' => $validated['badge']],
            [
                'name' => $validated['name'],
                'password' => \Illuminate\Support\Facades\Hash::make('P4ssword'),
                'role' => 'user'
            ]
        );

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Employee berhasil ditambahkan.');
    }

    public function edit(\App\Models\Employee $employee)
    {
        return view('dashboard.employees.edit', compact('employee'));
    }

    public function update(Request $request, \App\Models\Employee $employee)
    {
        $validated = $request->validate([
            'badge' => 'required|string|unique:employees,badge,' . $employee->id,
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'line' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        $oldBadge = $employee->badge;
        $employee->update($validated);

        // Update associated user account if it exists
        $user = \App\Models\User::where('badge', $oldBadge)->first();
        if ($user) {
            $user->update([
                'badge' => $validated['badge'],
                'name' => $validated['name'],
            ]);
        }

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Data Employee berhasil diperbarui.');
    }

    public function destroy(\App\Models\Employee $employee)
    {
        $badge = $employee->badge;
        $employee->delete();
        
        // Also delete the associated user account
        \App\Models\User::where('badge', $badge)->delete();

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Data Employee berhasil dihapus.');
    }
}
