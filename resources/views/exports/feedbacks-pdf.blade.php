<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Saran</title>
    <style>
        @page { margin: 80px 40px 50px 40px; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; margin: 0; padding: 0; }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; text-align: center; padding-bottom: 10px; }
        header h1 { font-size: 16px; color: #1b007c; margin: 0 0 3px 0; text-transform: uppercase; }
        header p { font-size: 9px; color: #666; margin: 0; }
        footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 20px; text-align: right; font-size: 8px; color: #999; }
        .page-number:after { content: counter(page); }
        main { margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        th { background-color: #1b007c; color: #fff; padding: 6px 5px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 5px; border-bottom: 1px solid #e5e7eb; font-size: 8px; vertical-align: top; }
        tr:nth-child(even) td { background-color: #f9fafb; }
        .status-completed { color: #16a34a; font-weight: bold; }
        .status-waiting { color: #d97706; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <h1>SARAN & MASUKAN</h1>
        <p>Diekspor pada {{ now()->format('d F Y, H:i') }} — Total: {{ $feedbacks->count() }} data</p>
    </header>

    <footer>
        SISCARD — Sistem Manajemen Saran | Halaman <span class="page-number"></span>
    </footer>

    <main>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Pengirim</th>
                <th>Badge</th>
                <th style="width: 35%;">Isi Saran</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th style="width: 20%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feedbacks as $index => $fb)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $fb->anonim ? 'Anonim' : ($fb->anggota->karyawan->nama ?? 'Tidak diketahui') }}</td>
                <td>{{ $fb->anonim ? 'Rahasia' : ($fb->anggota->karyawan->badge ?? '-') }}</td>
                <td>{{ $fb->deskripsi }}</td>
                <td>{{ $fb->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                    @if($fb->status === 'Completed')
                        <span class="status-completed">Completed</span>
                    @else
                        <span class="status-waiting">Waiting</span>
                    @endif
                </td>
                <td>{{ $fb->catatan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    </main>
</body>
</html>
