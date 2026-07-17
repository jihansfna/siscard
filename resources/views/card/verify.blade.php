<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kartu SPSI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-header {
            background: linear-gradient(135deg, #1b007c 0%, #3b0ddb 100%);
            padding: 28px 24px;
            text-align: center;
            position: relative;
        }
        .card-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 20px;
            background: #ffffff;
            border-radius: 20px 20px 0 0;
        }
        .card-header .logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 12px;
        }
        .card-header .logos img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: contain;
            background: white;
            padding: 2px;
        }
        .card-header h1 {
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .card-header p {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            margin-top: 4px;
        }

        .card-body { padding: 8px 24px 28px; }

        /* Photo */
        .member-photo-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 16px;
        }
        .member-photo {
            width: 100px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background: #dc2626;
        }
        .member-photo-fallback {
            width: 100px;
            height: 120px;
            border-radius: 12px;
            border: 3px solid #e5e7eb;
            background: linear-gradient(135deg, #1b007c, #3b0ddb);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 36px;
            font-weight: 800;
        }

        /* Member name */
        .member-name {
            text-align: center;
            font-size: 22px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 4px;
        }
        .member-kta {
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 16px;
        }

        /* Status */
        .status-verified {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            margin-bottom: 20px;
        }
        .status-verified .icon {
            width: 36px; height: 36px;
            background: #059669;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .status-verified .icon svg { width: 20px; height: 20px; color: #fff; }
        .status-verified .text h3 { font-size: 14px; font-weight: 700; color: #065f46; }
        .status-verified .text p { font-size: 11px; color: #6b7280; margin-top: 2px; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .status-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #059669;
        }

        .status-failed {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            margin-bottom: 20px;
        }
        .status-failed .icon {
            width: 36px; height: 36px;
            background: #dc2626;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .status-failed .icon svg { width: 20px; height: 20px; color: #fff; }
        .status-failed .text h3 { font-size: 14px; font-weight: 700; color: #991b1b; }
        .status-failed .text p { font-size: 11px; color: #6b7280; margin-top: 2px; }

        /* Info rows */
        .info-grid { display: grid; gap: 14px; }
        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .info-item label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 3px;
        }
        .info-item p {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
        }

        .divider {
            height: 1px;
            background: #f3f4f6;
            margin: 16px 0;
        }

        /* Signatures */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }
        .sig-box {
            text-align: center;
        }
        .sig-box .sig-label {
            font-size: 10px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }
        .sig-box .sig-img-container {
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
        }
        .sig-box .sig-img-container img {
            max-height: 36px;
            max-width: 80px;
        }
        .sig-box .sig-name {
            font-size: 12px;
            font-weight: 700;
            color: #1f2937;
        }
        .sig-box .sig-role {
            font-size: 10px;
            color: #6b7280;
        }

        .footer-meta {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            color: #9ca3af;
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid #f3f4f6;
            text-align: center;
        }
        .footer-meta .verified-text {
            font-weight: 700;
            color: #059669;
            font-size: 11px;
        }

        /* Download button */
        .download-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
        }
        .download-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
        }
        .download-btn svg {
            width: 18px;
            height: 18px;
        }

        /* Loading animation */
        .download-loading {
            display: none;
            text-align: center;
            padding: 12px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
        }
        .download-loading.active {
            display: block;
        }
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #059669;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 6px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div class="logos">
                <img src="{{ asset('logo_lem_spsi.png') }}" alt="Logo LEM SPSI">
                <img src="{{ asset('logo_kspsi.png') }}" alt="Logo KSPSI">
            </div>
            <h1>VERIFIKASI KEANGGOTAAN SPSI</h1>
            <p>SP LEM SPSI &mdash; PT XYZ</p>
        </div>
        <div class="card-body">
            @if($verified)
                <div class="status-verified">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text">
                        <h3>Anggota Terverifikasi</h3>
                        <p>Data keanggotaan ditemukan dan valid.</p>
                    </div>
                </div>

                <!-- Photo -->
                <div class="member-photo-wrapper">
                    @if($member->employee->image)
                        <img src="{{ asset('storage/' . $member->employee->image) }}" alt="Foto {{ $member->employee->name }}" class="member-photo">
                    @else
                        <div class="member-photo-fallback">{{ strtoupper(substr($member->employee->name, 0, 1)) }}</div>
                    @endif
                </div>

                <!-- Name & KTA -->
                <div class="member-name">{{ strtoupper($member->employee->name) }}</div>
                <div class="member-kta">No. KTA : {{ $member->employee->badge }}</div>

                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-item">
                            <label>Tempat/Tgl Lahir</label>
                            <p>{{ $member->employee->birth_place ?? '-' }}, {{ $member->employee->birth_date ? $member->employee->birth_date->format('d F Y') : '-' }}</p>
                        </div>
                        <div class="info-item">
                            <label>Status</label>
                            <p><span class="status-badge"><span class="dot"></span> Registered Member</span></p>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">
                            <label>Tanggal Bergabung</label>
                            <p>{{ $member->employee->join_date ? $member->employee->join_date->format('d F Y') : '-' }}</p>
                        </div>
                        <div class="info-item">
                            <label>PUK</label>
                            <p>PT XYZ</p>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="info-row">
                        <div class="info-item" style="grid-column: span 2;">
                            <label>Alamat</label>
                            <p>{{ $member->employee->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Signatures -->
                @if($ketuaName || $sekretarisName)
                <div class="signatures">
                    <div class="sig-box">
                        <div class="sig-label">Ketua</div>
                        <div class="sig-img-container">
                            @if($ketuaSign)
                                <img src="{{ $ketuaSign }}" alt="TTD Ketua">
                            @endif
                        </div>
                        <div class="sig-name">{{ $ketuaName }}</div>
                        <div class="sig-role">Ketua</div>
                    </div>
                    <div class="sig-box">
                        <div class="sig-label">Sekretaris</div>
                        <div class="sig-img-container">
                            @if($sekretarisSign)
                                <img src="{{ $sekretarisSign }}" alt="TTD Sekretaris">
                            @endif
                        </div>
                        <div class="sig-name">{{ $sekretarisName }}</div>
                        <div class="sig-role">Sekretaris</div>
                    </div>
                </div>
                @endif

                <div class="footer-meta">
                    <span class="verified-text">✓ Verified from SPSI Registry</span>
                    <span>Scan time: {{ $scanTime }} &bull; Token: {{ $token }}</span>
                </div>

                <!-- Download PDF Button -->
                <a href="{{ route('card.verify.pdf', $member->verify_token) }}" class="download-btn" id="downloadPdfBtn" onclick="showLoading()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Unduh PDF Verifikasi
                </a>
                <div class="download-loading" id="downloadLoading">
                    <span class="spinner"></span> Generating PDF...
                </div>

                <script>
                    function showLoading() {
                        document.getElementById('downloadPdfBtn').style.display = 'none';
                        document.getElementById('downloadLoading').classList.add('active');
                        setTimeout(() => {
                            document.getElementById('downloadPdfBtn').style.display = 'flex';
                            document.getElementById('downloadLoading').classList.remove('active');
                        }, 3000);
                    }

                    // Auto-download PDF after page load
                    window.addEventListener('load', function() {
                        setTimeout(() => {
                            window.location.href = '{{ route('card.verify.pdf', $member->verify_token) }}';
                        }, 1500);
                    });
                </script>
            @else
                <div class="status-failed">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text">
                        <h3>Data Anggota Tidak Ditemukan</h3>
                        <p>{{ $message }}</p>
                    </div>
                </div>
                <p style="text-align: center; color: #9ca3af; font-size: 12px; margin-top: 16px;">
                    Token verifikasi tidak valid atau sudah tidak berlaku.<br>
                    Pastikan QR Code yang di-scan berasal dari kartu anggota resmi SPSI.
                </p>
            @endif
        </div>
    </div>
</body>
</html>
