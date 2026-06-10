<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Anggota;
use App\Models\Saran;
use App\Models\RiwayatAnggota;

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
                $results = Karyawan::where('nama', 'like', "%{$q}%")
                    ->orWhere('badge', 'like', "%{$q}%")
                    ->limit(8)
                    ->get(['id', 'badge', 'nama', 'departemen'])
                    ->map(fn($e) => [
                        'text' => $e->nama,
                        'sub' => $e->badge . ($e->departemen ? ' · ' . $e->departemen : ''),
                        'value' => $e->nama,
                    ]);
                break;

            case 'members':
                $results = Anggota::with('karyawan')
                    ->whereHas('karyawan', function($query) use ($q) {
                        $query->where('nama', 'like', "%{$q}%")
                              ->orWhere('badge', 'like', "%{$q}%");
                    })
                    ->limit(8)
                    ->get()
                    ->map(fn($m) => [
                        'text' => $m->karyawan->nama ?? 'Tidak diketahui',
                        'sub' => ($m->karyawan->badge ?? '') . ' · ' . ucfirst($m->status),
                        'value' => $m->karyawan->nama ?? '',
                    ]);
                break;

            case 'feedbacks':
                $results = Saran::with('anggota.karyawan')
                    ->whereHas('anggota.karyawan', function($query) use ($q) {
                        $query->where('nama', 'like', "%{$q}%")
                              ->orWhere('badge', 'like', "%{$q}%");
                    })
                    ->limit(8)
                    ->get()
                    ->map(fn($f) => [
                        'text' => $f->anggota->karyawan->nama ?? 'Tidak diketahui',
                        'sub' => ($f->anggota->karyawan->badge ?? '') . ' · ' . $f->status,
                        'value' => $f->anggota->karyawan->nama ?? '',
                    ])
                    ->unique('text')
                    ->values();
                break;

            case 'history':
                // Search by activity, description, actor name, or target member name
                $results = RiwayatAnggota::with(['pelaku', 'anggota.karyawan'])
                    ->where(function($query) use ($q) {
                        $query->where('aktivitas', 'like', "%{$q}%")
                              ->orWhere('deskripsi', 'like', "%{$q}%")
                              ->orWhereHas('pelaku', fn($qr) => $qr->where('nama', 'like', "%{$q}%"))
                              ->orWhereHas('anggota.karyawan', fn($qr) => $qr->where('nama', 'like', "%{$q}%"));
                    })
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(fn($l) => [
                        'text' => ucfirst($l->aktivitas) . ' — ' . ($l->anggota?->karyawan?->nama ?? 'Tidak diketahui'),
                        'sub' => ($l->pelaku?->nama ?? 'System') . ' · ' . $l->created_at->format('d M Y'),
                        'value' => $q,
                    ])
                    ->unique('text')
                    ->values();
                break;
        }

        return response()->json($results);
    }
}
