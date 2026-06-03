<?php

namespace App\Http\Controllers;

use App\Models\MemberLog;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
// use Maatwebsite\Excel\Facades\Excel; // If user has it, but wait, usually Laravel excel is used. Let's see if we should create an export class. Wait, the user mentioned they have maatwebsite/excel. I will generate a simple export class or just do a CSV stream if no export class is ready.
// Let's check how export is done in the system.

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = MemberLog::with(['member.employee', 'actor'])->latest();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('activity', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('actor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('member.employee', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('activity') && $request->activity !== 'All Activity') {
            $query->where('activity', $request->activity);
        }

        if ($request->filled('actor') && $request->actor !== 'All Actor') {
            $query->where('actor_id', $request->actor);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate($request->query('perPage', 10))->withQueryString();
        
        $activities = MemberLog::select('activity')->distinct()->pluck('activity');
        $actors = User::whereHas('memberLogs')->get(); // Get users who have done something

        return view('dashboard.history', compact('logs', 'activities', 'actors'));
    }

    public function exportExcel(Request $request)
    {
        return (new \App\Exports\HistoryExport())->export();
    }
    
    public function exportPdf(Request $request)
    {
        $logs = MemberLog::with(['member.employee', 'actor'])->latest()->get();
        $pdf = Pdf::loadView('exports.history-pdf', compact('logs'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('history_export_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
