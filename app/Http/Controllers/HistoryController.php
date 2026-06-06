<?php

namespace App\Http\Controllers;

use App\Models\LogAnggota;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAnggota::with(['anggota.karyawan', 'pelaku'])->latest();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('aktivitas', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('pelaku', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('anggota.karyawan', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('activity') && $request->activity !== 'Semua Aktivitas') {
            $query->where('aktivitas', $request->activity);
        }

        if ($request->filled('actor') && $request->actor !== 'Semua Pelaku') {
            $query->where('pelaku_id', $request->actor);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate($request->query('perPage', 10))->withQueryString();
        
        $activities = LogAnggota::select('aktivitas')->distinct()->pluck('aktivitas');
        $actors = User::whereHas('logAnggota')->get(); // Get users who have done something

        return view('dashboard.history', compact('logs', 'activities', 'actors'));
    }

    public function exportExcel(Request $request)
    {
        return (new \App\Exports\HistoryExport())->export();
    }
    
    public function exportPdf(Request $request)
    {
        $logs = LogAnggota::with(['anggota.karyawan', 'pelaku'])->latest()->get();
        $pdf = Pdf::loadView('exports.history-pdf', compact('logs'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('history_export_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
