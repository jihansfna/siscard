<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Export PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #1b007c;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #1b007c;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Riwayat Aktivitas</h2>
        <p>Data diekspor pada: {{ date('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 15%">Aktor</th>
                <th style="width: 15%">Aktivitas</th>
                <th style="width: 20%">Target Member</th>
                <th style="width: 30%">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->actor ? $log->actor->name : 'Sistem' }}</td>
                    <td>{{ ucfirst($log->activity) }}</td>
                    <td>{{ $log->member && $log->member->employee ? $log->member->employee->name : '-' }}</td>
                    <td>{{ $log->description ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
