<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Saran;
use App\Models\Karyawan;
use App\Models\Anggota;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class FeedbackController extends Controller
{
    // ADMIN: View all feedbacks
    public function indexAdmin(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status');
        $sort = $request->query('sort', 'desc');
        
        $feedbacks = Saran::with('anggota.karyawan')
            ->when($q, function($query, $q) {
                $query->whereHas('anggota.karyawan', function($q2) use ($q) {
                    $q2->where('nama', 'like', "%{$q}%")
                       ->orWhere('badge', 'like', "%{$q}%");
                });
            })
            ->when($status && $status !== 'All Status', function($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('updated_at', $sort)
            ->paginate($request->query('perPage', 10))
            ->withQueryString();
            
        return view('dashboard.feedbacks', compact('feedbacks'));
    }

    // ADMIN: Mark feedback as completed with remark
    public function complete(Request $request, Saran $feedback, \App\Services\FonnteService $fonnte)
    {
        $request->validate([
            'catatan' => 'required|string',
        ], [
            'catatan.required' => 'Catatan / balasan wajib diisi.',
        ]);

        $feedback->update([
            'status' => 'Completed',
            'catatan' => $request->catatan,
        ]);

        // Load relations to get phone number
        $feedback->load('anggota.karyawan');
        $namaAnggota = $feedback->anggota->karyawan->nama ?? 'Anggota';
        $nomorWa = $feedback->anggota->karyawan->nomor_telp ?? null;

        $pesanNotif = '';
        if ($nomorWa) {
            $pesan = "Halo {$namaAnggota},\n\nSaran yang Anda kirim melalui SIS-CARD telah mendapatkan tanggapan dari HRD.\n\nSilakan masuk ke sistem SIS-CARD untuk melihat detail tanggapan.\n\nTerima kasih.";
            
            $berhasilKirim = $fonnte->kirimPesan($nomorWa, $pesan);
            
            if (!$berhasilKirim) {
                $pesanNotif = ' namun notifikasi WhatsApp gagal dikirim (cek log).';
            }
        } else {
            $pesanNotif = ' namun nomor WA anggota belum terdaftar.';
        }

        return back()->with('success', 'Saran berhasil ditandai sebagai Selesai' . $pesanNotif);
    }

    // USER: Store a new feedback
    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,zip|max:10240',
        ], [
            'deskripsi.required' => 'Deskripsi saran wajib diisi.',
            'dokumen.max' => 'Ukuran file terlalu besar. Maksimal 10MB.',
            'dokumen.mimes' => 'Format file tidak didukung. Gunakan PDF, Word, Excel, Gambar, atau ZIP.',
        ]);

        $karyawan = Karyawan::where('badge', auth()->user()->badge)->first();
        if (!$karyawan) return back()->withErrors(['message' => 'Anda tidak memiliki akses.']);

        // Block if employee is inactive
        if ($karyawan->tanggal_keluar && Carbon::parse($karyawan->tanggal_keluar)->lt(now()->startOfDay())) {
            return back()->withErrors(['message' => 'Masa kerja Anda telah berakhir. Akun dinonaktifkan.']);
        }
        
        $anggota = Anggota::where('karyawan_id', $karyawan->id)->first();
        if (!$anggota || $anggota->status !== 'registered') {
            return back()->withErrors(['message' => 'Anda belum terdaftar sebagai anggota aktif.']);
        }

        $filePath = null;
        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            
            // Double-check file validity
            if (!$file->isValid()) {
                return back()->withErrors(['dokumen' => 'Upload file gagal. Pastikan ukuran file tidak melebihi batas maksimal (10MB).'])->withInput();
            }
            
            $filePath = $file->store('feedbacks', 'public');
        }

        Saran::create([
            'anggota_id' => $anggota->id,
            'deskripsi' => $request->deskripsi,
            'berkas' => $filePath,
            'anonim' => $request->boolean('anonim'),
            'status' => 'Waiting',
        ]);

        return back()->with('success', 'Saran berhasil dikirim. Menunggu tanggapan HRD.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:saran,id',
        ]);

        $feedbacks = Saran::whereIn('id', $request->ids)->get();
        foreach ($feedbacks as $fb) {
            if ($fb->berkas) {
                Storage::disk('public')->delete($fb->berkas);
            }
        }

        Saran::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard.feedbacks')
            ->with('success', count($request->ids) . ' saran berhasil dihapus.');
    }

    // USER: Delete a feedback
    public function destroyUser(Saran $feedback)
    {
        $karyawan = Karyawan::where('badge', auth()->user()->badge)->first();
        if (!$karyawan) {
            abort(403, 'Tidak memiliki akses');
        }

        $anggota = Anggota::where('karyawan_id', $karyawan->id)->first();
        if (!$anggota || $feedback->anggota_id !== $anggota->id) {
            abort(403, 'Tidak memiliki akses');
        }

        // Delete attachment if present
        if ($feedback->berkas) {
            Storage::disk('public')->delete($feedback->berkas);
        }

        $feedback->delete();

        return back()->with('success', 'Saran Anda berhasil dihapus.');
    }
}
