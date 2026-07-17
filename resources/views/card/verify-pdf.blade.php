<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Verifikasi Anggota SPSI - {{ $employee->name }}</title>
    <style>
        @page {
            size: 85.6mm 135mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a1a;
            font-size: 7pt;
            line-height: 1.3;
        }

        .badge {
            width: 100%;
            position: relative;
            overflow: hidden;
            background: #ffffff;
        }

        /* Header */
        .badge-header {
            background-color: #15803d;
            padding: 10pt 12pt 20pt 12pt;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo-cell {
            width: 32pt;
            vertical-align: middle;
        }
        .header-logo-cell img {
            width: 28pt;
            height: 28pt;
        }
        .header-text-cell {
            text-align: center;
            vertical-align: middle;
        }
        .header-line-1 {
            font-size: 6pt;
            font-weight: bold;
            color: #d1fae5;
            letter-spacing: 0.5pt;
        }
        .header-line-2 {
            font-size: 8pt;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.3pt;
            margin-top: 1pt;
        }
        .header-line-3 {
            font-size: 10pt;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.3pt;
            margin-top: 1pt;
        }

        /* Photo */
        .photo-section {
            text-align: center;
            margin-top: -14pt;
            margin-bottom: 5pt;
        }
        .photo-img {
            width: 65pt;
            height: 65pt;
            border: 2pt solid #15803d;
            border-radius: 8pt;
            object-fit: cover;
        }
        .photo-fallback {
            width: 65pt;
            height: 65pt;
            border: 2pt solid #15803d;
            border-radius: 8pt;
            background-color: #15803d;
            color: #ffffff;
            font-size: 24pt;
            font-weight: bold;
            text-align: center;
            line-height: 65pt;
            display: inline-block;
        }

        /* Name */
        .name-section {
            text-align: center;
            padding: 0 10pt;
            margin-bottom: 3pt;
        }
        .member-name {
            font-size: 11pt;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 0.3pt;
            margin-bottom: 1pt;
        }
        .member-kta {
            font-size: 6.5pt;
            color: #6b7280;
        }

        /* Status */
        .status-row {
            text-align: center;
            margin-bottom: 5pt;
        }
        .status-badge {
            display: inline-block;
            padding: 2pt 10pt;
            font-size: 6pt;
            font-weight: bold;
            background-color: #ecfdf5;
            color: #059669;
            border: 0.5pt solid #a7f3d0;
            border-radius: 4pt;
            letter-spacing: 0.3pt;
        }

        /* Accent */
        .accent-wrapper {
            text-align: center;
            margin-bottom: 4pt;
        }
        .accent-line {
            display: inline-block;
            width: 28pt;
            height: 1.5pt;
            background-color: #15803d;
        }

        /* Info */
        .info-section {
            padding: 0 12pt;
            margin-bottom: 2pt;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
            padding: 2pt 2pt;
            text-align: center;
        }
        .info-label {
            font-size: 4.5pt;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.4pt;
            font-weight: 600;
            margin-bottom: 0.5pt;
        }
        .info-value {
            font-size: 6pt;
            font-weight: bold;
            color: #1f2937;
        }

        /* Divider */
        .info-divider {
            border: none;
            border-top: 0.5pt solid #e5e7eb;
            margin: 10pt 12pt;
        }

        /* Signatures */
        .sig-section {
            padding: 0 12pt;
        }
        .sig-location {
            text-align: center;
            font-size: 5.5pt;
            color: #6b7280;
            margin-bottom: 1pt;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 3pt;
        }
        .sig-img-container {
            height: 18pt;
            display: block;
            margin: 1pt auto;
        }
        .sig-img {
            max-height: 16pt;
            max-width: 46pt;
            display: block;
            margin: 0 auto;
        }
        .sig-name {
            font-size: 5pt;
            font-weight: bold;
            color: #1a1a1a;
        }
        .sig-line {
            border-bottom: 0.5pt solid #1a1a1a;
            width: 70%;
            margin: 1pt auto;
        }
        .sig-role {
            font-size: 4.5pt;
            color: #6b7280;
        }

        /* Footer */
        .badge-footer {
            background-color: #15803d;
            text-align: center;
            padding: 4pt 8pt;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .footer-verified {
            font-size: 5pt;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.3pt;
            margin-bottom: 0.5pt;
        }
        .footer-meta {
            font-size: 4pt;
            color: #d1fae5;
        }
    </style>
</head>
<body>
    <div class="badge">
        <!-- Header -->
        <div class="badge-header">
            <table class="header-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="header-logo-cell" style="text-align: left;">
                        @if($logoLemSpsi)
                            <img src="{{ $logoLemSpsi }}" alt="Logo LEM SPSI">
                        @endif
                    </td>
                    <td class="header-text-cell">
                        <div class="header-line-1">PIMPINAN UNIT KERJA</div>
                        <div class="header-line-2">SP LEM SPSI</div>
                        <div class="header-line-3">PT XYZ</div>
                    </td>
                    <td class="header-logo-cell" style="text-align: right;">
                        @if($logoKspsi)
                            <img src="{{ $logoKspsi }}" alt="Logo KSPSI">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Photo -->
        <div class="photo-section">
            @if($photo)
                <img src="{{ $photo }}" alt="Foto {{ $employee->name }}" class="photo-img">
            @else
                <div class="photo-fallback">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
            @endif
        </div>

        <!-- Name -->
        <div class="name-section">
            <div class="member-name">{{ strtoupper($employee->name) }}</div>
            <div class="member-kta">No. KTA : {{ $employee->badge }}</div>
        </div>

        <!-- Status -->
        <div class="status-row">
            <span class="status-badge">REGISTERED MEMBER</span>
        </div>

        <!-- Accent -->
        <div class="accent-wrapper">
            <div class="accent-line"></div>
        </div>

        <!-- Info -->
        <div class="info-section">
            <table class="info-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 50%;">
                        <div class="info-label">Tempat/Tgl Lahir</div>
                        <div class="info-value">{{ $employee->birth_place ?? '-' }}, {{ $employee->birth_date ? $employee->birth_date->format('d M Y') : '-' }}</div>
                    </td>
                    <td style="width: 50%;">
                        <div class="info-label">Tanggal Bergabung</div>
                        <div class="info-value">{{ $employee->join_date ? $employee->join_date->format('d M Y') : '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="info-label">PUK</div>
                        <div class="info-value">PT XYZ</div>
                    </td>
                    <td>
                        <div class="info-label">Alamat</div>
                        <div class="info-value">{{ $employee->address ?? '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <hr class="info-divider">

        <!-- Signatures -->
        <div class="sig-section">
            <div class="sig-location">Batam,</div>
            <table class="sig-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="sig-cell">
                        <div class="sig-img-container">
                            @if($ketuaSign)
                                <img src="{{ $ketuaSign }}" class="sig-img" alt="TTD Ketua">
                            @endif
                        </div>
                        <div class="sig-name">{{ $ketuaName }}</div>
                        <div class="sig-line"></div>
                        <div class="sig-role">Ketua</div>
                    </td>
                    <td class="sig-cell">
                        <div class="sig-img-container">
                            @if($sekretarisSign)
                                <img src="{{ $sekretarisSign }}" class="sig-img" alt="TTD Sekretaris">
                            @endif
                        </div>
                        <div class="sig-name">{{ $sekretarisName }}</div>
                        <div class="sig-line"></div>
                        <div class="sig-role">Sekretaris</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="badge-footer">
            <div class="footer-verified">Verified from SPSI Registry</div>
            <div class="footer-meta">Scan: {{ $scanTime }} &bull; Token: {{ $token }}</div>
        </div>
    </div>
</body>
</html>
