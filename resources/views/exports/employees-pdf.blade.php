<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Karyawan</title>
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
        td { padding: 5px; border-bottom: 1px solid #e5e7eb; font-size: 8px; }
        tr:nth-child(even) td { background-color: #f9fafb; }
        .badge-ended { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <h1>MASTER DATA KARYAWAN</h1>
        <p>Diekspor pada {{ now()->format('d F Y, H:i') }} — Total: {{ $employees->count() }} data</p>
    </header>

    <footer>
        SISCARD — Sistem Manajemen Karyawan | Halaman <span class="page-number"></span>
    </footer>

    <main>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Badge</th>
                <th>Nama</th>
                <th>Departemen</th>
                <th>Jabatan</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Keluar</th>
                <th>Tempat, Tgl Lahir</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $index => $emp)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $emp->badge }}</td>
                <td>{{ $emp->nama }}</td>
                <td>{{ $emp->departemen ?? '-' }}</td>
                <td>{{ $emp->jabatan ?? '-' }}</td>
                <td>{{ $emp->tanggal_masuk?->format('d/m/Y') ?? '-' }}</td>
                <td>
                    @if($emp->tanggal_keluar && $emp->tanggal_keluar->lt(now()->startOfDay()))
                        <span class="badge-ended">{{ $emp->tanggal_keluar->format('d/m/Y') }}</span>
                    @else
                        {{ $emp->tanggal_keluar?->format('d/m/Y') ?? '-' }}
                    @endif
                </td>
                <td>{{ ($emp->tempat_lahir ?? '-') . ', ' . ($emp->tanggal_lahir?->format('d/m/Y') ?? '-') }}</td>
                <td>{{ $emp->alamat ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    </main>
</body>
</html>
