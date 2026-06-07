<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kartu Digital SPSI - {{ $employee->name }}</title>
    <style>
        @page {
            size: 85.6mm 53.98mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a1a;
        }

        .card {
            /* 85.6mm = 242.65pt, 53.98mm = 153pt */
            /* Since DomPDF uses content-box model (box-sizing: border-box is not supported), */
            /* we subtract the padding (6pt top/bottom, 8pt left/right) from width/height: */
            /* Width: 242.65pt - (8pt * 2) = 226.65pt */
            /* Height: 153pt - (6pt * 2) = 141pt */
            width: 226.65pt;
            height: 141pt;
            padding: 6pt 8pt;
            overflow: hidden;
            position: relative;
            background-color: #ffffff;
            page-break-inside: avoid;
        }

        .page-break {
            page-break-after: always;
        }

        /* ===== FRONT VIEW ===== */
        .front-header {
            text-align: center;
            line-height: 1;
            margin-bottom: 5pt;
        }
        .front-header .title-1 { font-size: 7pt; font-weight: bold; color: #333; margin-bottom: 2pt; }
        .front-header .title-2 { font-size: 7pt; font-weight: bold; color: #f97316; }
        .front-header .title-3 { font-size: 8pt; font-weight: bold; color: #1a1a1a; margin-top: 2pt; }
        .front-header .title-4 { font-size: 7pt; font-weight: bold; color: #1a1a1a; margin-top: 1pt; }
        .front-header .title-5 { font-size: 3.5pt; color: #666; margin-top: 2pt; }

        .kta-label {
            font-size: 6.5pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 3pt;
        }
        .kta-value {
            color: #3b82f6;
        }

        .front-content {
            width: 100%;
            border-collapse: collapse;
        }
        
        .front-photo-wrapper {
            width: 55pt;
            height: 70pt;
            background-color: #dc2626;
            text-align: center;
            border-radius: 2pt;
            overflow: hidden;
            display: inline-block;
        }
        .front-photo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
        }
        .photo-fallback {
            line-height: 70pt;
            color: white;
            font-size: 24pt;
            font-weight: bold;
        }

        .front-qr-wrapper {
            text-align: center;
            vertical-align: bottom;
            padding-bottom: 5pt;
        }
        .front-qr-wrapper img {
            width: 50pt;
            height: 50pt;
            border: 1px solid #ccc;
            padding: 2pt;
            border-radius: 4pt;
        }

        .front-logo-wrapper {
            text-align: center;
            vertical-align: bottom;
        }
        .front-logo-wrapper img {
            max-width: 55pt;
            max-height: 55pt;
            width: auto;
            height: auto;
        }
        .front-logo-text {
            font-size: 6pt;
            font-weight: bold;
            color: #333;
            margin-top: 4pt;
        }

        /* ===== BACK VIEW ===== */
        .back-header {
            width: 100%;
            border-bottom: 0.5pt solid #eee;
            padding-bottom: 4pt;
            margin-bottom: 5pt;
        }
        .back-header-img-left { width: 25pt; height: 25pt; padding-bottom: 2pt; }
        .back-header-img-right { width: 25pt; height: 25pt; padding-bottom: 2pt; }
        .back-header-text {
            text-align: center;
            line-height: 1.1;
        }
        .back-header-text p { margin: 0; }
        .back-header-text .t1 { font-size: 6pt; font-weight: bold; }
        .back-header-text .t2 { font-size: 6pt; font-weight: bold; }
        .back-header-text .t3 { font-size: 7pt; font-weight: bold; }

        .back-info {
            width: 100%;
            font-size: 5.5pt;
            line-height: 1.3;
            border-collapse: collapse;
            margin-bottom: 2pt;
        }
        .back-info td {
            vertical-align: top;
            padding: 1.5pt 0;
        }
        .info-label { width: 50pt; font-weight: bold; }
        .info-sep { width: 5pt; }

        .back-footer {
            width: 100%;
            margin-top: 2pt;
        }
        .back-sig-city {
            text-align: center;
            font-size: 5pt;
            margin-bottom: 2pt;
            color: #333;
        }
        .back-sig-box {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .back-sig-img-container {
            height: 22pt;
            display: block;
        }
        .back-sig-img {
            max-height: 22pt;
            max-width: 50pt;
            margin: 0 auto;
            display: block;
        }
        .back-sig-name {
            font-size: 5pt;
            color: #333;
            margin-top: 0pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .back-sig-role {
            font-size: 5pt;
            color: #333;
            margin-top: 0pt;
        }
        .back-sig-line {
            border-bottom: 0.3pt solid #999;
            width: 85%;
            margin: 1pt auto 0 auto;
        }
    </style>
</head>
<body>

    <!-- ========================================== -->
    <!-- FRONT VIEW (PAGE 1)                        -->
    <!-- ========================================== -->
    <div class="card page-break">
        <div class="front-header">
            <div class="title-1">KARTU ANGGOTA</div>
            <div class="title-2">DEWAN PIMPINAN CABANG</div>
            <div class="title-3">Federasi SP LEM SPSI - KOTA BATAM</div>
            <div class="title-4">(DPC FSP LEM - SPSI - BTM)</div>
            <div class="title-5">(Branch Leader Executive Union Worker's Metal, Electronic and Machine Federation - All Indonesia Worker's Union)</div>
        </div>

        <div class="kta-label">NO. KTA : <span class="kta-value">{{ $employee->badge }}</span></div>

        <table class="front-content">
            <tr>
                <!-- Left: Photo -->
                <td style="width: 30%; vertical-align: top; text-align: left;">
                    @if($photo)
                        <div class="front-photo-wrapper">
                            <img src="{{ $photo }}" alt="Foto">
                        </div>
                    @else
                        <div class="front-photo-wrapper">
                            <div class="photo-fallback">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                        </div>
                    @endif
                </td>
                
                <!-- Center: QR -->
                <td style="width: 40%;" class="front-qr-wrapper">
                    <img src="{{ $qrBase64 }}" alt="QR">
                </td>

                <!-- Right: Logo LEM -->
                <td style="width: 30%;" class="front-logo-wrapper">
                    @if($logoLemSpsi)
                        <img src="{{ $logoLemSpsi }}" alt="Logo">
                    @endif
                    <div class="front-logo-text">SP LEM - SPSI</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================== -->
    <!-- BACK VIEW (PAGE 2)                         -->
    <!-- ========================================== -->
    <div class="card">
        <!-- Header -->
        <table class="back-header" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 30pt; text-align: left;">
                    @if($logoLemSpsi)
                        <img src="{{ $logoLemSpsi }}" class="back-header-img-left" alt="Logo LEM">
                    @endif
                </td>
                <td class="back-header-text">
                    <p class="t1">PIMPINAN UNIT KERJA</p>
                    <p class="t2">SP LEM SPSI</p>
                    <p class="t3">PT XYZ</p>
                </td>
                <td style="width: 30pt; text-align: right;">
                    @if($logoKspsi)
                        <img src="{{ $logoKspsi }}" class="back-header-img-right" alt="Logo KSPSI">
                    @endif
                </td>
            </tr>
        </table>

        <!-- Info Table -->
        <table class="back-info" cellpadding="0" cellspacing="0">
            <tr>
                <td class="info-label">Nama</td>
                <td class="info-sep">:</td>
                <td>{{ $employee->name }}</td>
            </tr>
            <tr>
                <td class="info-label">Tempat/Tgl.Lahir</td>
                <td class="info-sep">:</td>
                <td>{{ $employee->birth_place ?? '-' }} / {{ $employee->birth_date ? $employee->birth_date->format('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">P.U.K</td>
                <td class="info-sep">:</td>
                <td>PT. Satnusa Persada Tbk</td>
            </tr>
            <tr>
                <td class="info-label">Alamat</td>
                <td class="info-sep">:</td>
                <td>{{ $employee->address ?? '-' }}</td>
            </tr>
        </table>

        <!-- Footer Signatures -->
        <table class="back-footer" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 45%;"></td>
                <td style="width: 55%; vertical-align: top;">
                    <div class="back-sig-city">Batam,</div>
                    <table style="width: 100%;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="back-sig-box">
                                <div class="back-sig-img-container">
                                    @if($ketuaSign)
                                        <img src="{{ $ketuaSign }}" class="back-sig-img" alt="TTD Ketua">
                                    @endif
                                </div>
                                <div class="back-sig-name">{{ $ketuaName }}</div>
                                <div class="back-sig-line"></div>
                                <div class="back-sig-role">Ketua</div>
                            </td>
                            <td class="back-sig-box">
                                <div class="back-sig-img-container">
                                    @if($sekretarisSign)
                                        <img src="{{ $sekretarisSign }}" class="back-sig-img" alt="TTD Sekretaris">
                                    @endif
                                </div>
                                <div class="back-sig-name">{{ $sekretarisName }}</div>
                                <div class="back-sig-line"></div>
                                <div class="back-sig-role">Sekretaris</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
