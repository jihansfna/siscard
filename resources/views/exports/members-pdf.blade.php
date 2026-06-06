<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Anggota</title>
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
        .status-registered { color: #16a34a; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }
        .status-inactive { color: #6b7280; }
    </style>
</head>
<body>
    <header>
        <h1>DATA ANGGOTA</h1>
        <p>Diekspor pada {{ now()->format('d F Y, H:i') }} — Total: {{ $members->count() }} data</p>
    </header>

    <footer>
        SISCARD — Sistem Manajemen Anggota | Halaman <span class="page-number"></span>
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
                <th>Jabatan Anggota</th>
                <th>Status</th>
                <th>Terdaftar Pada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $index => $member)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $member->karyawan->badge ?? '-' }}</td>
                <td>{{ $member->karyawan->nama ?? '-' }}</td>
                <td>{{ $member->karyawan->departemen ?? '-' }}</td>
                <td>{{ $member->karyawan->jabatan ?? '-' }}</td>
                <td>{{ $member->jabatan->nama ?? 'Member' }}</td>
                <td>
                    @if($member->status == 'registered')
                        <span class="status-registered">Registered</span>
                    @elseif($member->status == 'pending')
                        <span class="status-pending">Pending</span>
                    @else
                        <span class="status-inactive">{{ ucfirst($member->status) }}</span>
                    @endif
                </td>
                <td>{{ $member->created_at?->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    </main>
</body>
</html>
