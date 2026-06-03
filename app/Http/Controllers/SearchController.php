<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Member;
use App\Models\Feedback;
use App\Models\MemberLog;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $q = $request->query('q', '');
        $type = $request->query('type', 'members');

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $results = [];

        switch ($type) {
            case 'employees':
                $results = Employee::where('name', 'like', "%{$q}%")
                    ->orWhere('badge', 'like', "%{$q}%")
                    ->limit(8)
                    ->get(['id', 'badge', 'name', 'department'])
                    ->map(fn($e) => [
                        'text' => $e->name,
                        'sub' => $e->badge . ($e->department ? ' · ' . $e->department : ''),
                        'value' => $e->name,
                    ]);
                break;

            case 'members':
                $results = Member::with('employee')
                    ->whereHas('employee', function($query) use ($q) {
                        $query->where('name', 'like', "%{$q}%")
                              ->orWhere('badge', 'like', "%{$q}%");
                    })
                    ->limit(8)
                    ->get()
                    ->map(fn($m) => [
                        'text' => $m->employee->name ?? 'Unknown',
                        'sub' => ($m->employee->badge ?? '') . ' · ' . ucfirst($m->status),
                        'value' => $m->employee->name ?? '',
                    ]);
                break;

            case 'feedbacks':
                $results = Feedback::with('member.employee')
                    ->whereHas('member.employee', function($query) use ($q) {
                        $query->where('name', 'like', "%{$q}%")
                              ->orWhere('badge', 'like', "%{$q}%");
                    })
                    ->limit(8)
                    ->get()
                    ->map(fn($f) => [
                        'text' => $f->member->employee->name ?? 'Unknown',
                        'sub' => ($f->member->employee->badge ?? '') . ' · ' . $f->status,
                        'value' => $f->member->employee->name ?? '',
                    ])
                    ->unique('text')
                    ->values();
                break;

            case 'history':
                // Search by activity, description, actor name, or target member name
                $results = MemberLog::with(['actor', 'member.employee'])
                    ->where(function($query) use ($q) {
                        $query->where('activity', 'like', "%{$q}%")
                              ->orWhere('description', 'like', "%{$q}%")
                              ->orWhereHas('actor', fn($qr) => $qr->where('name', 'like', "%{$q}%"))
                              ->orWhereHas('member.employee', fn($qr) => $qr->where('name', 'like', "%{$q}%"));
                    })
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(fn($l) => [
                        'text' => ucfirst($l->activity) . ' — ' . ($l->member?->employee?->name ?? 'Unknown'),
                        'sub' => ($l->actor?->name ?? 'System') . ' · ' . $l->created_at->format('d M Y'),
                        'value' => $q,
                    ])
                    ->unique('text')
                    ->values();
                break;
        }

        return response()->json($results);
    }
}
