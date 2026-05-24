<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Employee;
use App\Models\MemberRole;
use App\Models\MemberLog;
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
        $member = Member::with(['employee', 'role'])->findOrFail($id);

        if ($member->status !== 'registered') {
            return response()->json([
                'success' => false,
                'message' => 'Kartu digital belum dapat di-generate. Status keanggotaan belum "Registered".'
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
        $member = Member::with(['employee', 'role'])->findOrFail($id);

        if ($member->status !== 'registered') {
            return back()->with('error', 'Kartu digital belum dapat diunduh. Status keanggotaan belum "Registered".');
        }

        $cardData = $this->buildCardData($member);

        $pdf = Pdf::loadView('card.digital-card', $cardData);
        $pdf->setPaper([0, 0, 242.65, 153]);

        $filename = 'Kartu_Digital_SPSI_' . str_replace(' ', '_', $member->employee->name) . '.pdf';

        MemberLog::create([
            'member_id' => $member->id,
            'actor_id' => auth()->id(),
            'activity' => 'Download Card',
            'description' => 'Card downloaded by admin.',
        ]);

        return $pdf->download($filename);
    }

    /**
     * Download card for the logged-in user (user route).
     */
    public function downloadOwn()
    {
        $user = auth()->user();
        $employee = Employee::where('badge', $user->badge)->first();

        if (!$employee) {
            return back()->with('error', 'Data employee tidak ditemukan.');
        }

        $member = Member::with(['employee', 'role'])
            ->where('employee_id', $employee->id)
            ->first();

        if (!$member) {
            return back()->with('error', 'Anda belum terdaftar sebagai member SPSI.');
        }

        if ($member->status !== 'registered') {
            return back()->with('error', 'Kartu digital belum dapat diunduh. Status keanggotaan belum "Registered".');
        }

        $cardData = $this->buildCardData($member);

        $pdf = Pdf::loadView('card.digital-card', $cardData);
        $pdf->setPaper([0, 0, 242.65, 153]);

        $filename = 'Kartu_Digital_SPSI_' . str_replace(' ', '_', $member->employee->name) . '.pdf';

        MemberLog::create([
            'member_id' => $member->id,
            'actor_id' => auth()->id(),
            'activity' => 'Download Card',
            'description' => 'Card downloaded by member.',
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
        // Decrypt the token to get the original UUID
        $uuid = self::decryptToken($token);

        if (!$uuid) {
            return view('card.verify', [
                'verified' => false,
                'message' => 'Data anggota tidak ditemukan. Token verifikasi tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        $member = Member::with(['employee', 'role'])
            ->where('uuid', $uuid)
            ->first();

        if (!$member) {
            return view('card.verify', [
                'verified' => false,
                'message' => 'Data anggota tidak ditemukan.',
            ]);
        }

        // Build verification data including signatures for display
        $verifyData = $this->buildVerifyData($member);

        MemberLog::create([
            'member_id' => $member->id,
            'actor_id' => null, // Public scan
            'activity' => 'Verify Card',
            'description' => 'QR Code scanned for verification.',
        ]);

        return view('card.verify', array_merge([
            'verified' => true,
            'member' => $member,
            'scanTime' => now()->format('Y-m-d H:i'),
            'token' => strtoupper(substr(md5($member->uuid . config('app.key')), 0, 10)),
        ], $verifyData));
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
        $uuid = self::decryptToken($token);

        if (!$uuid) {
            abort(404, 'Data anggota tidak ditemukan. Token verifikasi tidak valid.');
        }

        $member = Member::with(['employee', 'role'])
            ->where('uuid', $uuid)
            ->first();

        if (!$member) {
            abort(404, 'Data anggota tidak ditemukan.');
        }

        $cardData = $this->buildCardData($member);

        $pdf = Pdf::loadView('card.verify-pdf', $cardData);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Verifikasi_SPSI_' . str_replace(' ', '_', $member->employee->name) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Build card data array for rendering.
     */
    private function buildCardData(Member $member): array
    {
        $employee = $member->employee;

        // Get Ketua and Sekretaris from member_roles
        $ketuaRole = MemberRole::where('name', 'Ketua')->first();
        $sekretarisRole = MemberRole::where('name', 'Sekretaris')->first();

        $ketua = null;
        $sekretaris = null;

        if ($ketuaRole) {
            $ketua = Member::with('employee')
                ->where('member_role_id', $ketuaRole->id)
                ->where('status', 'registered')
                ->whereNull('deleted_at')
                ->first();
        }

        if ($sekretarisRole) {
            $sekretaris = Member::with('employee')
                ->where('member_role_id', $sekretarisRole->id)
                ->where('status', 'registered')
                ->whereNull('deleted_at')
                ->first();
        }

        // Build encrypted verification URL — QR contains ONLY this encrypted token
        $encryptedToken = self::encryptToken($member->uuid);
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
        if ($employee->image) {
            $photoPath = storage_path('app/public/' . $employee->image);
            if (file_exists($photoPath)) {
                $ext = pathinfo($photoPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/jpeg';
                $photoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
            }
        }

        // Ketua signature
        $ketuaSignBase64 = '';
        if ($ketua && $ketua->sign_image) {
            $signPath = storage_path('app/public/' . $ketua->sign_image);
            if (file_exists($signPath)) {
                $ext = pathinfo($signPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/png';
                $ketuaSignBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($signPath));
            }
        }

        // Sekretaris signature
        $sekretarisSignBase64 = '';
        if ($sekretaris && $sekretaris->sign_image) {
            $signPath = storage_path('app/public/' . $sekretaris->sign_image);
            if (file_exists($signPath)) {
                $ext = pathinfo($signPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/png';
                $sekretarisSignBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($signPath));
            }
        }

        return [
            'member' => $member,
            'employee' => $employee,
            'qrBase64' => $qrBase64,
            'encryptedToken' => $encryptedToken,
            'logoKspsi' => $logoKspsiBase64,
            'logoLemSpsi' => $logoLemSpsiBase64,
            'photo' => $photoBase64,
            'ketuaName' => $ketua ? $ketua->employee->name : '',
            'ketuaSign' => $ketuaSignBase64,
            'sekretarisName' => $sekretaris ? $sekretaris->employee->name : '',
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
    private function buildVerifyData(Member $member): array
    {
        $ketuaRole = MemberRole::where('name', 'Ketua')->first();
        $sekretarisRole = MemberRole::where('name', 'Sekretaris')->first();

        $ketua = null;
        $sekretaris = null;

        if ($ketuaRole) {
            $ketua = Member::with('employee')
                ->where('member_role_id', $ketuaRole->id)
                ->where('status', 'registered')
                ->whereNull('deleted_at')
                ->first();
        }

        if ($sekretarisRole) {
            $sekretaris = Member::with('employee')
                ->where('member_role_id', $sekretarisRole->id)
                ->where('status', 'registered')
                ->whereNull('deleted_at')
                ->first();
        }

        // Ketua signature as base64
        $ketuaSignBase64 = '';
        if ($ketua && $ketua->sign_image) {
            $signPath = storage_path('app/public/' . $ketua->sign_image);
            if (file_exists($signPath)) {
                $ext = pathinfo($signPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/png';
                $ketuaSignBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($signPath));
            }
        }

        // Sekretaris signature as base64
        $sekretarisSignBase64 = '';
        if ($sekretaris && $sekretaris->sign_image) {
            $signPath = storage_path('app/public/' . $sekretaris->sign_image);
            if (file_exists($signPath)) {
                $ext = pathinfo($signPath, PATHINFO_EXTENSION);
                $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
                $mime = $mimeTypes[strtolower($ext)] ?? 'image/png';
                $sekretarisSignBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($signPath));
            }
        }

        return [
            'ketuaName' => $ketua ? $ketua->employee->name : '',
            'ketuaSign' => $ketuaSignBase64,
            'sekretarisName' => $sekretaris ? $sekretaris->employee->name : '',
            'sekretarisSign' => $sekretarisSignBase64,
        ];
    }
}
