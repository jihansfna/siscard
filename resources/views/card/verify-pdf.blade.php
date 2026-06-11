<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Verifikasi Anggota SPSI - {{ $employee->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 18mm 20mm 18mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a1a;
            font-size: 11pt;
            line-height: 1.4;
            position: relative;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 72pt;
            color: rgba(5, 150, 105, 0.06);
            font-weight: 900;
            letter-spacing: 12pt;
            z-index: 0;
            white-space: nowrap;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        /* Header */
        .header-table {
            width: 100%;
            margin-bottom: 8pt;
            border-collapse: collapse;
        }
        .header-logo {
            width: 50pt;
            vertical-align: middle;
        }
        .header-logo img {
            width: 45pt;
            height: 45pt;
            border-radius: 50%;
            object-fit: contain;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
            line-height: 1.3;
        }
        .header-text .h1 {
            font-size: 12pt;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 0.5pt;
        }
        .header-text .h2 {
            font-size: 13pt;
            font-weight: bold;
            color: #1a1a1a;
        }
        .header-text .h3 {
            font-size: 14pt;
            font-weight: bold;
            color: #1a1a1a;
        }

        .header-divider {
            border: none;
            border-top: 2pt solid #1a1a1a;
            margin: 10pt 0 20pt 0;
        }

        /* Photo section */
        .photo-section {
            text-align: center;
            margin-bottom: 16pt;
        }
        .photo-wrapper {
            width: 100pt;
            height: 120pt;
            margin: 0 auto;
            background-color: #dc2626;
            border: 2pt solid #e5e7eb;
            border-radius: 6pt;
            overflow: hidden;
            display: inline-block;
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
        }
        .photo-fallback {
            line-height: 120pt;
            color: white;
            font-size: 36pt;
            font-weight: bold;
            text-align: center;
        }

        /* Member name */
        .member-name {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 14pt 0 4pt 0;
            letter-spacing: 0.5pt;
        }
        .member-kta {
            text-align: center;
            font-size: 11pt;
            color: #6b7280;
            margin-bottom: 20pt;
        }

        /* Info table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6pt;
        }
        .info-table td {
            padding: 6pt 0;
            vertical-align: top;
        }
        .info-label {
            font-size: 9pt;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            padding-bottom: 2pt;
        }
        .info-value {
            font-size: 11pt;
            font-weight: bold;
            color: #1f2937;
        }
        .info-cell {
            width: 50%;
            padding: 6pt 4pt;
            vertical-align: top;
        }

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 3pt 10pt;
            border-radius: 10pt;
            font-size: 10pt;
            font-weight: bold;
            background-color: #ecfdf5;
            color: #059669;
            border: 1pt solid #a7f3d0;
        }

        /* Divider */
        .section-divider {
            border: none;
            border-top: 1pt solid #e5e7eb;
            margin: 14pt 0;
        }

        /* Address full width */
        .full-row {
            padding: 6pt 0;
        }

        /* Signature section */
        .sig-section {
            margin-top: 30pt;
        }
        .sig-location {
            text-align: right;
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 6pt;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10pt;
        }
        .sig-stamp-cell {
            width: 0%;
            text-align: center;
            vertical-align: middle;
        }
        .sig-img-container {
            height: 40pt;
            display: block;
            margin: 4pt auto;
        }
        .sig-img {
            max-height: 36pt;
            max-width: 80pt;
            display: block;
            margin: 0 auto;
        }
        .sig-name {
            font-size: 10pt;
            font-weight: bold;
            color: #1a1a1a;
            border-top: 1pt solid #1a1a1a;
            padding-top: 3pt;
            display: inline-block;
        }
        .sig-role {
            font-size: 9pt;
            color: #6b7280;
        }

        /* Stamp */
        .stamp-wrapper {
            text-align: center;
            margin: 0 auto;
        }
        .stamp-wrapper img {
            width: 65pt;
            height: 65pt;
            opacity: 0.7;
        }

        /* Footer verification */
        .verify-footer {
            margin-top: 30pt;
            padding-top: 10pt;
            border-top: 1pt solid #e5e7eb;
            text-align: center;
        }
        .verify-text {
            font-size: 9pt;
            color: #6b7280;
        }
        .verify-text strong {
            color: #059669;
        }
    </style>
</head>
<body>
    <div class="watermark">VERIFIED SPSI</div>

    <div class="content">
        <!-- Header with logos -->
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header-logo" style="text-align: left;">
                    @if($logoLemSpsi)
                        <img src="{{ $logoLemSpsi }}" alt="Logo LEM SPSI">
                    @endif
                </td>
                <td class="header-text">
                    <div class="h1">PIMPINAN UNIT KERJA</div>
                    <div class="h2">SP LEM SPSI</div>
                    <div class="h3">PT.SAT NUSAPERSADA TBK</div>
                </td>
                <td class="header-logo" style="text-align: right;">
                    @if($logoKspsi)
                        <img src="{{ $logoKspsi }}" alt="Logo KSPSI">
                    @endif
                </td>
            </tr>
        </table>

        <hr class="header-divider">

        <!-- Photo -->
        <div class="photo-section">
            @if($photo)
                <div class="photo-wrapper" style="background-image: url('{{ $photo }}');"></div>
            @else
                <div class="photo-wrapper">
                    <div class="photo-fallback">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                </div>
            @endif
        </div>

        <!-- Member Name -->
        <div class="member-name">{{ strtoupper($employee->name) }}</div>
        <div class="member-kta">No. KTA : {{ $employee->badge }}</div>

        <hr class="section-divider">

        <!-- Info Grid -->
        <table class="info-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="info-cell">
                    <div class="info-label">Place and Date of Birth</div>
                    <div class="info-value">{{ $employee->birth_place ?? '-' }}, {{ $employee->birth_date ? $employee->birth_date->format('d F Y') : '-' }}</div>
                </td>
                <td class="info-cell">
                    <div class="info-label">Status</div>
                    <div class="info-value"><span class="status-badge">Registered Member</span></div>
                </td>
            </tr>
            <tr>
                <td class="info-cell">
                    <div class="info-label">Join Date</div>
                    <div class="info-value">{{ $employee->join_date ? $employee->join_date->format('d F Y') : '-' }}</div>
                </td>
                <td class="info-cell">
                    <div class="info-label">PUK</div>
                    <div class="info-value">PT XYZ</div>
                </td>
            </tr>
        </table>

        <table class="info-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding: 6pt 4pt;">
                    <div class="info-label">Address</div>
                    <div class="info-value">{{ $employee->address ?? '-' }}</div>
                </td>
            </tr>
        </table>

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
                        <div class="sig-role">Ketua</div>
                    </td>
                    <td class="sig-stamp-cell">
                        <div class="stamp-wrapper">
                            @if($logoLemSpsi)
                                <img src="{{ $logoLemSpsi }}" alt="Stempel" style="opacity: 0.35;">
                            @endif
                        </div>
                    </td>
                    <td class="sig-cell">
                        <div class="sig-img-container">
                            @if($sekretarisSign)
                                <img src="{{ $sekretarisSign }}" class="sig-img" alt="TTD Sekretaris">
                            @endif
                        </div>
                        <div class="sig-name">{{ $sekretarisName }}</div>
                        <div class="sig-role">Sekretaris</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer Verification Info -->
        <div class="verify-footer">
            <p class="verify-text">
                <strong>Verified from SPSI Registry</strong> &bull; Scan time: {{ $scanTime }} &bull; Token: {{ $token }}
            </p>
        </div>
    </div>
</body>
</html>
