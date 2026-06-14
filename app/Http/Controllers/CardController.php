<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\RiwayatAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CardController extends Controller
{
    /**
     * Encrypt a member's UUID into a URL-safe verification token.
     * QR code contains ONLY this encrypted token — no personal data.
     */
    public static function encryptToken(string $uuid): string
    {
        return rtrim(strtr(base64_encode(
            Crypt::encryptString($uuid)
        ), '+/', '-_'), '=');
    }

    /**
     * Decrypt a verification token back to the original UUID.
     * Returns null if token is invalid or tampered with.
     */
    public static function decryptToken(string $token): ?string
    {
        try {
            $encrypted = base64_decode(strtr($token, '-_', '+/'));
            return Crypt::decryptString($encrypted);
        } catch (DecryptException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate card preview data for a member.
     * Returns JSON data used by the front-end card preview.
     */
    public function preview($id)
    {
        $member = Anggota::with(['karyawan', 'jabatan'])->findOrFail($id);

        if ($member->status !== 'registered') {
            return response()->json([
                'success' => false,
                'message' => 'Kartu digital belum dapat di-generate. Status keanggotaan bukan "Registered".'
            ], 422);
        }

        $cardData = $this->buildCardData($member);

        return response()->json([
            'success' => true,
            'data' => $cardData
        ]);
    }

    /**
     * Download the digital card as PDF.
     */
    public function download($id)
    {
        $member = Anggota::with(['karyawan', 'jabatan'])->findOrFail($id);

        if ($member->status !== 'registered') {
            return back()->with('error', 'Kartu digital belum dapat diunduh. Status keanggotaan bukan "Registered".');
        }

        $cardData = $this->buildCardData($member);

        $pdf = Pdf::loadView('card.digital-card', $cardData);
        $pdf->setPaper([0, 0, 242.65, 153]);

        $filename = 'Kartu_Digital_SPSI_' . str_replace(' ', '_', $member->karyawan->nama) . '.pdf';

        RiwayatAnggota::create([
            'anggota_id' => $member->id,
            'pelaku_id' => auth()->id(),
            'aktivitas' => 'Download Card',
            'deskripsi' => 'Kartu diunduh oleh admin.',
        ]);

        return $pdf->download($filename);
    }

    /**
     * Download card for the logged-in user (user route).
     */
    public function downloadOwn()
    {
        $user = auth()->user();
        $karyawan = Karyawan::where('badge', $user->badge)->first();

        if (!$karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $member = Anggota::with(['karyawan', 'jabatan'])
            ->where('karyawan_id', $karyawan->id)
            ->first();

        if (!$member) {
            return back()->with('error', 'Anda tidak terdaftar sebagai anggota SPSI.');
        }

        if ($member->status !== 'registered') {
            return back()->with('error', 'Kartu digital belum dapat diunduh. Status keanggotaan bukan "Registered".');
        }

        $cardData = $this->buildCardData($member);

        $pdf = Pdf::loadView('card.digital-card', $cardData);
        $pdf->setPaper([0, 0, 242.65, 153]);

        $filename = 'Kartu_Digital_SPSI_' . str_replace(' ', '_', $member->karyawan->nama) . '.pdf';

        RiwayatAnggota::create([
            'anggota_id' => $member->id,
            'pelaku_id' => auth()->id(),
            'aktivitas' => 'Download Card',
            'deskripsi' => 'Kartu diunduh oleh anggota.',
        ]);

        return $pdf->download($filename);
    }

    /**
     * QR Code verification endpoint.
     * When QR code is scanned, this page displays member verification info.
     * The token parameter is an encrypted UUID — no personal data exposed in URL.
     */
    public function verify($token)
    {
        $member = Anggota::with(['karyawan', 'jabatan'])
            ->where('qr_token', $token)
            ->first();

        // Fallback for old encrypted token
        if (!$member) {
            $uuid = self::decryptToken($token);
            if ($uuid) {
                $member = Anggota::with(['karyawan', 'jabatan'])
                    ->where('uuid', $uuid)
                    ->first();
            }
        }

        if (!$member) {
            return view('card.verify', [
                'verified' => false,
                'message' => 'Data anggota tidak ditemukan. Token verifikasi tidak valid atau kedaluwarsa.',
            ]);
        }

        RiwayatAnggota::create([
            'anggota_id' => $member->id,
            'pelaku_id' => null, // Public scan
            'aktivitas' => 'Verify Card',
            'deskripsi' => 'QR Code dipindai untuk verifikasi.',
        ]);

        $cardData = $this->buildCardData($member);

        $pdf = Pdf::loadView('card.verify-pdf', $cardData);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Verifikasi_SPSI_' . str_replace(' ', '_', $member->karyawan->nama) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate QR Code image as SVG response.
     * Used by JavaScript on the frontend to render QR codes without external APIs.
     * Uses SVG format to avoid Imagick PHP extension dependency.
     */
    public function qrImage(Request $request)
    {
        $data = $request->query('data', '');
        $size = min((int) $request->query('size', 300), 500);

        if (empty($data)) {
            abort(400, 'Missing data parameter');
        }

        $qrSvg = QrCode::size($size)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($data);

        return response($qrSvg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * Generate and download verification PDF when QR is scanned.
     * Token is encrypted — decrypted server-side to fetch member data.
     */
    public function verifyPdf($token)
    {
        $member = Anggota::with(['karyawan', 'jabatan'])
            ->where('qr_token', $token)
            ->first();

        // Fallback for old encrypted token
        if (!$member) {
            $uuid = self::decryptToken($token);
            if ($uuid) {
                $member = Anggota::with(['karyawan', 'jabatan'])
                    ->where('uuid', $uuid)
                    ->first();
            }
        }

        if (!$member) {
            abort(404, 'Member data not found. Verification token is invalid.');
        }

        $cardData = $this->buildCardData($member);

        $pdf = Pdf::loadView('card.verify-pdf', $cardData);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Verifikasi_SPSI_' . str_replace(' ', '_', $member->karyawan->nama) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Build card data array for rendering.
     */
    private function buildCardData(Anggota $member): array
    {
        $karyawan = $member->karyawan;

        // Get Ketua and Sekretaris from jabatan
        $ketuaRole = Jabatan::where('nama', 'Ketua')->first();
        $sekretarisRole = Jabatan::where('nama', 'Sekretaris')->first();

        $ketua = null;
        $sekretaris = null;

        if ($ketuaRole) {
            $ketua = Anggota::with('karyawan')
                ->where('jabatan_id', $ketuaRole->id)
                ->where('status', 'registered')
                ->whereNull('deleted_at')
                ->first();
        }

        if ($sekretarisRole) {
            $sekretaris = Anggota::with('karyawan')
                ->where('jabatan_id', $sekretarisRole->id)
                ->where('status', 'registered')
                ->whereNull('deleted_at')
                ->first();
        }

        // Build encrypted verification URL — QR contains ONLY this encrypted token
        $encryptedToken = $member->verify_token;
        $verifyUrl = url('/verify/' . $encryptedToken);

        // Generate QR Code as base64 SVG for PDF embedding (no Imagick dependency)
        $qrSvg = QrCode::size(400)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($verifyUrl);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        // Logo paths (as base64 for PDF embedding)
        $logoKspsiPath = public_path('logo_kspsi.png');
        $logoLemSpsiPath = public_path('logo_lem_spsi.jpg');

        $logoKspsiBase64 = '';
        $logoLemSpsiBase64 = '';

        if (file_exists($logoKspsiPath)) {
            $logoKspsiBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoKspsiPath));
        }
        if (file_exists($logoLemSpsiPath)) {
            $logoLemSpsiBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoLemSpsiPath));
        }

        // Employee photo
        $photoBase64 = '';
        if ($karyawan->foto) {
            $photoPath = storage_path('app/public/' . $karyawan->foto);
            if (file_exists($photoPath)) {
                $ext = pathinfo($photoPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/jpeg';
                $photoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
            }
        }

        // Ketua signature
        $ketuaSignBase64 = '';
        if ($ketua && $ketua->tanda_tangan) {
            $signPath = storage_path('app/public/' . $ketua->tanda_tangan);
            if (file_exists($signPath)) {
                $ext = pathinfo($signPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/png';
                $ketuaSignBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($signPath));
            }
        }

        // Sekretaris signature
        $sekretarisSignBase64 = '';
        if ($sekretaris && $sekretaris->tanda_tangan) {
            $signPath = storage_path('app/public/' . $sekretaris->tanda_tangan);
            if (file_exists($signPath)) {
                $ext = pathinfo($signPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/png';
                $sekretarisSignBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($signPath));
            }
        }

        return [
            'member' => $member,
            'employee' => $karyawan,
            'qrBase64' => $qrBase64,
            'encryptedToken' => $encryptedToken,
            'logoKspsi' => $logoKspsiBase64,
            'logoLemSpsi' => $logoLemSpsiBase64,
            'photo' => $photoBase64,
            'ketuaName' => $ketua ? $ketua->karyawan->nama : '',
            'ketuaSign' => $ketuaSignBase64,
            'sekretarisName' => $sekretaris ? $sekretaris->karyawan->nama : '',
            'sekretarisSign' => $sekretarisSignBase64,
            'verifyUrl' => $verifyUrl,
            'scanTime' => now()->format('Y-m-d H:i'),
            'token' => strtoupper(substr(md5($member->uuid . config('app.key')), 0, 10)),
        ];
    }

    /**
     * Build verification data for the verify page display (non-PDF).
     * Uses base64 for signatures to display inline.
     */
    private function buildVerifyData(Anggota $member): array
    {
        $ketuaRole = Jabatan::where('nama', 'Ketua')->first();
        $sekretarisRole = Jabatan::where('nama', 'Sekretaris')->first();

        $ketua = null;
        $sekretaris = null;

        if ($ketuaRole) {
            $ketua = Anggota::with('karyawan')
                ->where('jabatan_id', $ketuaRole->id)
                ->where('status', 'registered')
                ->whereNull('deleted_at')
                ->first();
        }

        if ($sekretarisRole) {
            $sekretaris = Anggota::with('karyawan')
                ->where('jabatan_id', $sekretarisRole->id)
                ->where('status', 'registered')
                ->whereNull('deleted_at')
                ->first();
        }

        // Ketua signature as base64
        $ketuaSignBase64 = '';
        if ($ketua && $ketua->tanda_tangan) {
            $signPath = storage_path('app/public/' . $ketua->tanda_tangan);
            if (file_exists($signPath)) {
                $ext = pathinfo($signPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/png';
                $ketuaSignBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($signPath));
            }
        }

        // Sekretaris signature as base64
        $sekretarisSignBase64 = '';
        if ($sekretaris && $sekretaris->tanda_tangan) {
            $signPath = storage_path('app/public/' . $sekretaris->tanda_tangan);
            if (file_exists($signPath)) {
                $ext = pathinfo($signPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/png';
                $sekretarisSignBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($signPath));
            }
        }

        return [
            'ketuaName' => $ketua ? $ketua->karyawan->nama : '',
            'ketuaSign' => $ketuaSignBase64,
            'sekretarisName' => $sekretaris ? $sekretaris->karyawan->nama : '',
            'sekretarisSign' => $sekretarisSignBase64,
        ];
    }
}
