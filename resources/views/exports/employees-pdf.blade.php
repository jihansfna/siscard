<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Employees</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #1b007c; }
        .header h1 { font-size: 16px; color: #1b007c; margin-bottom: 3px; }
        .header p { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #1b007c; color: #fff; padding: 6px 5px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 5px; border-bottom: 1px solid #e5e7eb; font-size: 8px; }
        tr:nth-child(even) td { background-color: #f9fafb; }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #999; }
        .badge-ended { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MASTER DATA EMPLOYEES</h1>
        <p>Exported on {{ now()->format('d F Y, H:i') }} — Total: {{ $employees->count() }} records</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Badge</th>
                <th>Nama</th>
                <th>Department</th>
                <th>Position</th>
                <th>Join Date</th>
                <th>End Date</th>
                <th>Tempat, Tgl Lahir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $index => $emp)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $emp->badge }}</td>
                <td>{{ $emp->name }}</td>
                <td>{{ $emp->department ?? '-' }}</td>
                <td>{{ $emp->position ?? '-' }}</td>
                <td>{{ $emp->join_date?->format('d/m/Y') ?? '-' }}</td>
                <td>
                    @if($emp->end_date && $emp->end_date->lt(now()->startOfDay()))
                        <span class="badge-ended">{{ $emp->end_date->format('d/m/Y') }}</span>
                    @else
                        {{ $emp->end_date?->format('d/m/Y') ?? '-' }}
                    @endif
                </td>
                <td>{{ ($emp->birth_place ?? '-') . ', ' . ($emp->birth_date?->format('d/m/Y') ?? '-') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        SISCARD — Employee Management System
    </div>
</body>
</html>
