<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Feedback;
use App\Models\MemberLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Anggota (Registered)
        $totalAnggota = Member::where('status', 'registered')->count();
        $totalAnggotaThisMonth = Member::where('status', 'registered')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 2. Download Kartu Hari Ini
        $downloadsToday = MemberLog::where('activity', 'Download Card')
            ->whereDate('created_at', Carbon::today())
            ->count();
        
        $downloadsYesterday = MemberLog::where('activity', 'Download Card')
            ->whereDate('created_at', Carbon::yesterday())
            ->count();
            
        $downloadsDiff = $downloadsToday - $downloadsYesterday;
        $downloadsDiffText = $downloadsDiff >= 0 ? "+{$downloadsDiff} dari kemarin" : "{$downloadsDiff} dari kemarin";

        // 3. Pending Konfirmasi
        $pendingMembers = Member::where('status', 'pending')->count();

        // 4. Saran Masuk
        $totalFeedbacks = Feedback::count();
        $pendingFeedbacks = Feedback::where('status', 'pending')->count();

        // 5. Grafik Scan Barcode (Current Month)
        $daysInMonth = Carbon::now()->daysInMonth;
        $scansPerDay = array_fill(1, $daysInMonth, 0);
        
        $scanLogs = MemberLog::select(DB::raw('DAY(created_at) as day'), DB::raw('count(*) as total'))
            ->where('activity', 'Verify Card')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('day')
            ->get();
            
        foreach ($scanLogs as $log) {
            $scansPerDay[$log->day] = $log->total;
        }
        
        $chartData = [
            'labels' => array_keys($scansPerDay),
            'data' => array_values($scansPerDay),
            'monthName' => Carbon::now()->translatedFormat('F Y'),
            'average' => count(array_filter($scansPerDay)) > 0 ? round(array_sum($scansPerDay) / count(array_filter($scansPerDay)), 1) : 0
        ];

        // 6. Status Anggota (Donut Chart)
        $allMembers = Member::count();
        if ($allMembers > 0) {
            $aktif = round((Member::where('status', 'registered')->count() / $allMembers) * 100);
            $tidakAktif = round((Member::where('status', 'inactive')->count() / $allMembers) * 100);
            // using rejected/pending for the rest
            $keluar = round((Member::whereIn('status', ['rejected', 'pending'])->count() / $allMembers) * 100);
        } else {
            $aktif = 0; $tidakAktif = 0; $keluar = 0;
        }

        $donutData = [
            'aktif' => $aktif,
            'tidakAktif' => $tidakAktif,
            'keluar' => $keluar
        ];

        return view('dashboard', compact(
            'totalAnggota', 'totalAnggotaThisMonth',
            'downloadsToday', 'downloadsDiffText',
            'pendingMembers',
            'totalFeedbacks', 'pendingFeedbacks',
            'chartData', 'donutData'
        ));
    }
}
