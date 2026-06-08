<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Saran;
use App\Models\LogAnggota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ─── RINGKASAN CARDS ─────────────────────────────────────

        // 1. Total Anggota Aktif
        $totalActiveMembers = Anggota::where('status', 'registered')->count();
        $totalActiveMembersThisMonth = Anggota::where('status', 'registered')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 2. Pending Verifikasi
        $pendingMembers = Anggota::where('status', 'pending')->count();
        // Average verification time (days from created to disetujui_pada)
        $avgVerificationDays = Anggota::whereNotNull('disetujui_pada')
            ->selectRaw('AVG(DATEDIFF(disetujui_pada, created_at)) as avg_days')
            ->value('avg_days');
        $avgVerificationDays = $avgVerificationDays ? round($avgVerificationDays, 1) : 0;

        // 3. Aduan Belum Selesai
        $pendingFeedbacks = Saran::where('status', 'Waiting')->count();

        // ─── GRAFIK: PERTUMBUHAN ANGGOTA (6 BULAN TERAKHIR) ──────

        $monthlyGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('M');

            // Anggota baru bulan ini
            $newMembers = Anggota::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            // Anggota keluar/nonaktif bulan ini
            $exitedMembers = Anggota::whereIn('status', ['inactive', 'rejected'])
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->count();

            $monthlyGrowth[] = [
                'month' => $monthName,
                'new' => $newMembers,
                'exited' => $exitedMembers,
            ];
        }

        $growthLabels = array_column($monthlyGrowth, 'month');
        $growthNew = array_column($monthlyGrowth, 'new');
        $growthExited = array_column($monthlyGrowth, 'exited');

        $now = Carbon::now();
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $growthPeriodLabel = $sixMonthsAgo->translatedFormat('M') . ' – ' . $now->translatedFormat('M Y');

        // ─── GRAFIK: AKTIVITAS KARTU DIGITAL (30 HARI TERAKHIR) ──

        $activityDownloads = [];
        $activityScans = [];
        $activityLabels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $activityLabels[] = $date->format('j');

            $downloads = LogAnggota::where('aktivitas', 'Download Card')
                ->whereDate('created_at', $date)
                ->count();
            $activityDownloads[] = $downloads;

            $scans = LogAnggota::where('aktivitas', 'Verify Card')
                ->whereDate('created_at', $date)
                ->count();
            $activityScans[] = $scans;
        }

        // ─── STATUS ANGGOTA (PROGRESS BARS) ──────────────────────

        $allMembers = Anggota::count();
        if ($allMembers > 0) {
            $activePercent = round((Anggota::where('status', 'registered')->count() / $allMembers) * 100);
            $inactivePercent = round((Anggota::where('status', 'inactive')->count() / $allMembers) * 100);
            $exitedPercent = 100 - $activePercent - $inactivePercent;
        } else {
            $activePercent = 0;
            $inactivePercent = 0;
            $exitedPercent = 0;
        }

        return view('dashboard', compact(
            'totalActiveMembers', 'totalActiveMembersThisMonth',
            'pendingMembers', 'avgVerificationDays',
            'pendingFeedbacks',
            'growthLabels', 'growthNew', 'growthExited', 'growthPeriodLabel',
            'activityLabels', 'activityDownloads', 'activityScans',
            'activePercent', 'inactivePercent', 'exitedPercent'
        ));
    }
}
