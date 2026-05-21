<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Members</title>
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
        .status-registered { color: #16a34a; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }
        .status-inactive { color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MEMBERS DATA</h1>
        <p>Exported on {{ now()->format('d F Y, H:i') }} — Total: {{ $members->count() }} records</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Badge</th>
                <th>Nama</th>
                <th>Department</th>
                <th>Position</th>
                <th>Role</th>
                <th>Status</th>
                <th>Registered At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $index => $member)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $member->employee->badge ?? '-' }}</td>
                <td>{{ $member->employee->name ?? '-' }}</td>
                <td>{{ $member->employee->department ?? '-' }}</td>
                <td>{{ $member->employee->position ?? '-' }}</td>
                <td>{{ $member->role->name ?? 'Member' }}</td>
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

    <div class="footer">
        SISCARD — Member Management System
    </div>
</body>
</html>
