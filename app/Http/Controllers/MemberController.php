<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\RiwayatAnggota;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status');
        $sort = $request->query('sort', 'desc');

        $sortDirection = $sort === 'asc' ? 'asc' : 'desc';

        // Optimized: removed 'riwayatAnggota.pelaku' — now loaded on-demand via AJAX
        $members = Anggota::with(['karyawan', 'jabatan'])
            ->when($q, function($query, $q) {
                $query->whereHas('karyawan', function($q2) use ($q) {
                    $q2->where('nama', 'like', "%{$q}%")
                       ->orWhere('badge', 'like', "%{$q}%");
                });
            })
            ->when($status && $status !== 'Semua Status', function($query) use ($status) {
                $statusMap = [
                    'Anggota Terdaftar' => 'registered',
                    'Menunggu Verifikasi' => 'pending',
                    'Tidak Aktif' => 'inactive'
                ];
                if (isset($statusMap[$status])) {
                    if ($statusMap[$status] === 'inactive') {
                        $query->where(function($q) {
                            $q->where('status', 'inactive')
                              ->orWhereHas('karyawan', function($q2) {
                                  $q2->whereNotNull('tanggal_keluar')
                                     ->where('tanggal_keluar', '<=', today());
                              });
                        });
                    } else {
                        $query->where('status', $statusMap[$status])
                              ->whereHas('karyawan', function($q2) {
                                  $q2->where(function($q3) {
                                      $q3->whereNull('tanggal_keluar')
                                         ->orWhere('tanggal_keluar', '>', today());
                                  });
                              });
                    }
                }
            })
            ->orderBy('updated_at', $sortDirection)
            ->paginate($request->query('perPage', 10))
            ->withQueryString();

        $today = now()->toDateString();

        // Optimized: only select columns needed for the modal table
        $availableEmployees = Karyawan::select('id', 'badge', 'nama', 'departemen', 'line', 'jabatan', 'tanggal_keluar')
            ->whereNotIn('id', function($query) {
                $query->select('karyawan_id')->from('anggota')->whereNull('deleted_at');
            })
            ->where(function($query) use ($today) {
                $query->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>=', $today);
            })
            ->get();

        $memberRoles = Jabatan::where('nama', '!=', 'Anggota')->orderBy('nama')->get();

        // Optimized: single query for Ketua & Sekretaris instead of 4 separate queries
        $signRoles = Jabatan::whereIn('nama', ['Ketua', 'Sekretaris'])->pluck('id', 'nama');
        $signMembers = Anggota::with('karyawan')
            ->whereIn('jabatan_id', $signRoles->values())
            ->where('status', 'registered')
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('jabatan_id');

        $ketua = $signMembers->get($signRoles->get('Ketua'));
        $sekretaris = $signMembers->get($signRoles->get('Sekretaris'));

        return view('dashboard.members', compact('members', 'availableEmployees', 'memberRoles', 'ketua', 'sekretaris', 'sort'));
    }

    /**
     * AJAX endpoint: fetch member logs on-demand (when drawer is opened)
     */
    public function logs(Anggota $member)
    {
        $logs = $member->riwayatAnggota()->with('pelaku')->orderBy('created_at', 'asc')->get();

        return response()->json($logs->map(function($log) {
            return [
                'activity' => $log->aktivitas,
                'description' => $log->deskripsi,
                'actor_name' => $log->pelaku ? $log->pelaku->nama : 'System',
                'actor_badge' => $log->pelaku ? $log->pelaku->badge : '',
                'created_at_date' => $log->created_at->format('l'),
                'created_at_time' => $log->created_at->format('d F Y, H.i'),
            ];
        }));
    }

    public function update(Request $request, Anggota $member)
    {
        $request->validate([
            'jabatan_id' => 'required|exists:jabatan,id',
            'tanda_tangan' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ], [
            'jabatan_id.required' => 'Jabatan wajib dipilih.',
            'jabatan_id.exists' => 'Jabatan tidak valid.',
            'tanda_tangan.image' => 'Tanda tangan harus berupa gambar.',
            'tanda_tangan.mimes' => 'Format tanda tangan harus png, jpg, atau jpeg.',
            'tanda_tangan.max' => 'Ukuran tanda tangan maksimal 2MB.',
        ]);

        $newRole = Jabatan::findOrFail($request->jabatan_id);

        // Enforce tunggal: if the new role is single-holder, demote the current holder
        if ($newRole->tunggal) {
            $defaultRole = Jabatan::where('nama', 'Anggota')->first();
            if ($defaultRole) {
                // Find old holders of this specific role
                $oldHolders = Anggota::where('jabatan_id', $newRole->id)
                    ->where('id', '!=', $member->id)
                    ->whereNull('deleted_at')
                    ->get();
                
                foreach ($oldHolders as $oldHolder) {
                    // Delete signature file from storage
                    if ($oldHolder->tanda_tangan) {
                        $oldPath = storage_path('app/public/' . $oldHolder->tanda_tangan);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    
                    // Demote to Member and clear signature
                    $oldHolder->update([
                        'jabatan_id' => $defaultRole->id,
                        'tanda_tangan' => null
                    ]);
                }
            }
        }

        $data = [
            'jabatan_id' => $request->jabatan_id,
        ];

        // Handle signature upload
        if ($request->hasFile('tanda_tangan')) {
            // Delete old signature if exists
            if ($member->tanda_tangan) {
                $oldPath = storage_path('app/public/' . $member->tanda_tangan);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $data['tanda_tangan'] = $request->file('tanda_tangan')->store('signatures', 'public');
        }

        $member->update($data);

        RiwayatAnggota::create([
            'anggota_id' => $member->id,
            'pelaku_id' => auth()->id(),
            'aktivitas' => 'Update',
            'status' => $member->status,
            'deskripsi' => 'Jabatan diperbarui menjadi "' . $newRole->nama . '"' . ($request->hasFile('tanda_tangan') ? ' dan tanda tangan diperbarui.' : '.'),
        ]);

        $message = 'Jabatan anggota berhasil diperbarui menjadi "' . $newRole->nama . '".';
        return back()->with('success', $message);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:karyawan,id',
        ], [
            'employee_ids.required' => 'Sistem menampilkan pesan validasi bahwa karyawan belum dipilih'
        ]);

        $today = now()->toDateString();

        // Filter out any inactive employees (tanggal_keluar < today)
        $activeEmployees = Karyawan::whereIn('id', $request->employee_ids)
            ->where(function($query) use ($today) {
                $query->whereNull('tanggal_keluar')
                      ->orWhere('tanggal_keluar', '>=', $today);
            })
            ->pluck('id')
            ->toArray();

        $inactiveCount = count($request->employee_ids) - count($activeEmployees);

        if (empty($activeEmployees)) {
            return back()->withErrors(['employee_ids' => 'Semua karyawan yang dipilih sudah tidak aktif (tanggal_keluar sudah lewat).']);
        }

        $defaultRole = Jabatan::firstOrCreate(
            ['nama' => 'Anggota'],
            ['tunggal' => false, 'penandatangan' => false]
        );

        $anggotaToInsert = [];
        foreach ($activeEmployees as $empId) {
            $anggotaToInsert[] = [
                'uuid' => Str::uuid(),
                'karyawan_id' => $empId,
                'jabatan_id' => $defaultRole->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Anggota::insert($anggotaToInsert);

        $insertedAnggota = Anggota::whereIn('karyawan_id', $activeEmployees)->get();
        $logsToInsert = [];
        foreach ($insertedAnggota as $m) {
            $logsToInsert[] = [
                'anggota_id' => $m->id,
                'pelaku_id' => auth()->id(),
                'aktivitas' => 'Create',
                'status' => 'pending',
                'deskripsi' => 'Anggota ditambahkan',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $logsToInsert[] = [
                'anggota_id' => $m->id,
                'pelaku_id' => auth()->id(),
                'aktivitas' => 'Status Update',
                'status' => 'pending',
                'deskripsi' => 'Menunggu konfirmasi anggota',
                'created_at' => now()->addSecond(),
                'updated_at' => now()->addSecond(),
            ];
        }
        RiwayatAnggota::insert($logsToInsert);

        $message = count($activeEmployees) . ' Anggota berhasil ditambahkan.';
        if ($inactiveCount > 0) {
            $message .= ' ' . $inactiveCount . ' karyawan dilewati karena sudah tidak aktif.';
        }

        return back()->with('success', $message);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:anggota,id',
        ]);

        $anggotaList = Anggota::with('karyawan')->whereIn('id', $request->ids)->get();
        $logsToInsert = [];
        foreach ($anggotaList as $m) {
            $logsToInsert[] = [
                'anggota_id' => $m->id,
                'pelaku_id' => auth()->id(),
                'aktivitas' => 'Delete',
                'status' => $m->status,
                'deskripsi' => 'Menghapus data anggota: ' . ($m->karyawan ? $m->karyawan->nama : 'Tidak diketahui'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        RiwayatAnggota::insert($logsToInsert);

        Anggota::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard.members')
            ->with('success', count($request->ids) . ' anggota berhasil dihapus.');
    }
}
