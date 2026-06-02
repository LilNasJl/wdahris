<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        th {
            font-weight: bold;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .no-border {
            border: 0;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="11" class="center no-border">
                Republic of the Philippines<br>
                Asuncion Water District<br>
                Purok.4 Cambanoy, Asuncion<br>
                Davao del Norte
            </td>
        </tr>
        <tr>
            <td colspan="11" class="title no-border">{{ $title }}</td>
        </tr>
        <tr>
            <td colspan="11" class="center no-border">
                {{ $employee->full_name }} | {{ $employee->designation?->title }} | {{ $employee->department?->name }}<br>
                Date Generated: {{ $generatedDate }}
            </td>
        </tr>

        @if ($report === 'leave-history')
            <tr>
                <th>#</th>
                <th>From</th>
                <th>To</th>
                <th>Leave Type</th>
                <th>Deducted</th>
                <th>LWOP</th>
                <th>Status</th>
                <th colspan="2">Comment</th>
                <th colspan="2">Approved and Checked by HR</th>
            </tr>
            @forelse ($leaveRows as $leave)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="center">{{ $leave->leave_from?->format('m/d/Y') }}</td>
                    <td class="center">{{ $leave->leave_to?->format('m/d/Y') }}</td>
                    <td>{{ $leave->leave_type }}</td>
                    <td class="right">{{ number_format((float) $leave->deducted_points, 2) }}</td>
                    <td class="center">{{ $leave->is_lwop ? 'LWOP' : '' }}</td>
                    <td>{{ $leave->status }}</td>
                    <td colspan="2">{{ $leave->approval_comment }}</td>
                    <td colspan="2">{{ $leave->approved_checked_by_hr }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center">No leave history yet.</td>
                </tr>
            @endforelse
        @else
            <tr>
                <th rowspan="3">#</th>
                <th colspan="3">Leaves Earned</th>
                <th colspan="3">Vacation Leave</th>
                <th colspan="3">Sick Leave</th>
                <th rowspan="3">Date & Action Taken</th>
            </tr>
            <tr>
                <th colspan="2">Period</th>
                <th rowspan="2">Particulars</th>
                <th rowspan="2">Earned</th>
                <th rowspan="2">Abs. Und/WP</th>
                <th rowspan="2">Bal</th>
                <th rowspan="2">Earned</th>
                <th rowspan="2">Abs. Und/WP</th>
                <th rowspan="2">Bal</th>
            </tr>
            <tr>
                <th>From</th>
                <th>To</th>
            </tr>
            @forelse ($creditRows as $row)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="center">{{ $row['from_label'] }}</td>
                    <td class="center">{{ $row['to_label'] }}</td>
                    <td>{{ $row['particulars'] }}</td>
                    <td class="right">{{ blank($row['vacation_earned']) ? '' : number_format((float) $row['vacation_earned'], 3) }}</td>
                    <td class="right">{{ is_numeric($row['vacation_absence']) ? number_format((float) $row['vacation_absence'], 3) : $row['vacation_absence'] }}</td>
                    <td class="right">{{ number_format((float) $row['vacation_balance'], 3) }}</td>
                    <td class="right">{{ blank($row['sick_earned']) ? '' : number_format((float) $row['sick_earned'], 3) }}</td>
                    <td class="right">{{ is_numeric($row['sick_absence']) ? number_format((float) $row['sick_absence'], 3) : $row['sick_absence'] }}</td>
                    <td class="right">{{ number_format((float) $row['sick_balance'], 3) }}</td>
                    <td>{{ $row['action_taken'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center">No leave credit history yet.</td>
                </tr>
            @endforelse
        @endif
    </table>
</body>
</html>
