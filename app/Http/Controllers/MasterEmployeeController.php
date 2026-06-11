<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Anggota;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MasterEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $karyawan = Karyawan::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%{$search}%")
                         ->orWhere('badge', 'like', "%{$search}%");
        })->orderBy('created_at', 'asc')->paginate($request->query('perPage', 10))->withQueryString();

        return view('dashboard.employees.index', ['employees' => $karyawan]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'badge' => 'required|string|unique:karyawan,badge',
            'nama' => 'required|string|max:255',
            'departemen' => 'nullable|string|max:255',
            'line' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date|before:tanggal_masuk',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pertanyaan_rahasia' => 'nullable|string|max:500',
            'jawaban_rahasia' => 'nullable|string|max:255|required_with:pertanyaan_rahasia',
        ], [
            'badge.required' => 'Badge ID wajib diisi.',
            'badge.unique' => 'Badge ID sudah terdaftar.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar harus sama atau setelah tanggal masuk.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum tanggal masuk.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'pertanyaan_rahasia.max' => 'Pertanyaan rahasia maksimal 500 karakter.',
            'jawaban_rahasia.max' => 'Jawaban rahasia maksimal 255 karakter.',
            'jawaban_rahasia.required_with' => 'Jawaban rahasia wajib diisi jika pertanyaan rahasia diisi.',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('employees', 'public');
        }

        // Extract security question fields before creating employee
        $pertanyaanRahasia = $validated['pertanyaan_rahasia'] ?? null;
        $jawabanRahasia = $validated['jawaban_rahasia'] ?? null;
        unset($validated['pertanyaan_rahasia'], $validated['jawaban_rahasia']);

        Karyawan::create($validated);

        // Create associated user account with default password and security question
        $userData = [
            'nama' => $validated['nama'],
            'password' => Hash::make('P4ssword'),
            'peran' => 'user',
        ];

        if ($pertanyaanRahasia && $jawabanRahasia) {
            $userData['pertanyaan_rahasia'] = $pertanyaanRahasia;
            $userData['jawaban_rahasia'] = Hash::make(strtolower(trim($jawabanRahasia)));
        }

        User::firstOrCreate(
            ['badge' => $validated['badge']],
            $userData
        );

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }


    public function update(Request $request, Karyawan $employee)
    {
        $validated = $request->validate([
            'badge' => 'required|string|unique:karyawan,badge,' . $employee->id,
            'nama' => 'required|string|max:255',
            'departemen' => 'nullable|string|max:255',
            'line' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date|before:tanggal_masuk',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pertanyaan_rahasia' => 'nullable|string|max:500',
            'jawaban_rahasia' => 'nullable|string|max:255|required_with:pertanyaan_rahasia',
        ], [
            'badge.required' => 'Badge ID wajib diisi.',
            'badge.unique' => 'Badge ID sudah terdaftar.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar harus sama atau setelah tanggal masuk.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum tanggal masuk.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'pertanyaan_rahasia.max' => 'Pertanyaan rahasia maksimal 500 karakter.',
            'jawaban_rahasia.max' => 'Jawaban rahasia maksimal 255 karakter.',
            'jawaban_rahasia.required_with' => 'Jawaban rahasia wajib diisi jika pertanyaan rahasia diisi.',
        ]);

        if ($request->hasFile('foto')) {
            if ($employee->foto) {
                Storage::disk('public')->delete($employee->foto);
            }
            $validated['foto'] = $request->file('foto')->store('employees', 'public');
        }

        // Extract security question fields before updating employee
        $pertanyaanRahasia = $validated['pertanyaan_rahasia'] ?? null;
        $jawabanRahasia = $validated['jawaban_rahasia'] ?? null;
        unset($validated['pertanyaan_rahasia'], $validated['jawaban_rahasia']);

        $oldBadge = $employee->badge;
        $employee->update($validated);

        // Update associated user account if it exists
        $user = User::where('badge', $oldBadge)->first();
        if ($user) {
            $userUpdate = [
                'badge' => $validated['badge'],
                'nama' => $validated['nama'],
            ];

            // Update security question if provided
            if ($pertanyaanRahasia) {
                $userUpdate['pertanyaan_rahasia'] = $pertanyaanRahasia;
                if ($jawabanRahasia) {
                    $userUpdate['jawaban_rahasia'] = Hash::make(strtolower(trim($jawabanRahasia)));
                }
            }

            $user->update($userUpdate);
        }

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $employee)
    {
        $badge = $employee->badge;
        $employee->delete();
        
        // Also delete the associated user account
        User::where('badge', $badge)->delete();

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Data karyawan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:karyawan,id',
        ]);

        $karyawanList = Karyawan::whereIn('id', $request->ids)->get();
        $badges = $karyawanList->pluck('badge')->toArray();

        // Delete associated user accounts
        User::whereIn('badge', $badges)->delete();

        // Delete the employees
        Karyawan::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard.employees.index')
            ->with('success', count($request->ids) . ' data karyawan berhasil dihapus.');
    }

    public function setInactive(Karyawan $employee)
    {
        // 1. Set employee tanggal_keluar to yesterday so they become inactive immediately
        $employee->update(['tanggal_keluar' => now()->subDay()->startOfDay()]);

        // 2. Set Anggota status to inactive
        $anggota = Anggota::where('karyawan_id', $employee->id)->first();
        if ($anggota) {
            $anggota->update(['status' => 'inactive']);
        }

        return redirect()->route('dashboard.employees.index')
            ->with('success', 'Karyawan ' . $employee->nama . ' telah dinonaktifkan dan akses login dicabut.');
    }
}
