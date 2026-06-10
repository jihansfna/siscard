<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Anggota;
use App\Models\Saran;
use App\Models\RiwayatAnggota;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $karyawan = Karyawan::where('badge', $user->badge)->first();
        
        $accessStatus = 'no_access';
        $memberId = null;

        $member = null;
        $feedbacks = collect();

        if ($karyawan) {
            // Check if employee has passed their tanggal_keluar
            if ($karyawan->tanggal_keluar && Carbon::parse($karyawan->tanggal_keluar)->lt(now()->startOfDay())) {
                $accessStatus = 'inactive';
            } else {
                $member = Anggota::with('jabatan')->where('karyawan_id', $karyawan->id)->first();
                if ($member) {
                    $accessStatus = $member->status; // 'pending' or 'registered' etc.
                    $memberId = $member->id;
                    
                    if ($accessStatus === 'registered') {
                        $perPage = $request->query('perPage', 5);
                        $feedbacks = Saran::where('anggota_id', $memberId)->latest()->paginate($perPage)->withQueryString();
                    } else {
                        $feedbacks = new LengthAwarePaginator([], 0, 5);
                    }
                } else {
                    $feedbacks = new LengthAwarePaginator([], 0, 5);
                }
            }
        } else {
            $feedbacks = new LengthAwarePaginator([], 0, 5);
        }

        return view('home', compact('accessStatus', 'memberId', 'feedbacks', 'member') + ['employee' => $karyawan]);
    }

    public function confirmMembership(Request $request, $id)
    {
        $anggota = Anggota::findOrFail($id);
        
        // Pastikan member ini milik user yang sedang login
        $karyawan = Karyawan::where('badge', auth()->user()->badge)->first();
        if (!$karyawan || $anggota->karyawan_id !== $karyawan->id) {
            abort(403);
        }

        // Pastikan karyawan masih aktif
        if ($karyawan->tanggal_keluar && Carbon::parse($karyawan->tanggal_keluar)->lt(now()->startOfDay())) {
            abort(403, 'Masa kerja Anda telah berakhir.');
        }

        $anggota->update([
            'status' => 'registered',
            'disetujui_pada' => now()
        ]);

        RiwayatAnggota::create([
            'anggota_id' => $anggota->id,
            'pelaku_id' => auth()->id(),
            'aktivitas' => 'Update Status',
            'status' => 'registered',
            'deskripsi' => 'Anggota telah mengkonfirmasi keanggotaan',
        ]);

        return redirect()->route('user.home')->with('success', 'Keanggotaan berhasil dikonfirmasi. Selamat datang!');
    }
}
