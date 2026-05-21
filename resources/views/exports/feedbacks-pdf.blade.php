<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Feedbacks</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #1b007c; }
        .header h1 { font-size: 16px; color: #1b007c; margin-bottom: 3px; }
        .header p { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #1b007c; color: #fff; padding: 6px 5px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 5px; border-bottom: 1px solid #e5e7eb; font-size: 8px; vertical-align: top; }
        tr:nth-child(even) td { background-color: #f9fafb; }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #999; }
        .status-completed { color: #16a34a; font-weight: bold; }
        .status-waiting { color: #d97706; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SARAN & MASUKAN</h1>
        <p>Exported on {{ now()->format('d F Y, H:i') }} — Total: {{ $feedbacks->count() }} records</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Pengirim</th>
                <th>Badge</th>
                <th style="width: 35%;">Isi Saran</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th style="width: 20%;">Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feedbacks as $index => $fb)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $fb->member->employee->name ?? 'Unknown' }}</td>
                <td>{{ $fb->member->employee->badge ?? '-' }}</td>
                <td>{{ $fb->description }}</td>
                <td>{{ $fb->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                    @if($fb->status === 'Completed')
                        <span class="status-completed">Completed</span>
                    @else
                        <span class="status-waiting">Waiting</span>
                    @endif
                </td>
                <td>{{ $fb->remark ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        SISCARD — Feedback Management System
    </div>
</body>
</html>
