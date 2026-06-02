<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $employee->full_name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.25;
            margin: 0;
        }

        .letterhead {
            align-items: start;
            display: grid;
            grid-template-columns: 82px 1fr 82px;
            gap: 10px;
            margin-bottom: 12px;
        }

        .logo {
            height: 64px;
            object-fit: contain;
            width: 82px;
        }

        .agency,
        .title,
        .meta {
            text-align: center;
        }

        .agency {
            line-height: 1.18;
        }

        .title {
            font-size: 17px;
            font-weight: 700;
            margin-top: 8px;
            text-transform: uppercase;
        }

        .meta {
            margin-bottom: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        th {
            font-weight: 700;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        thead {
            display: table-header-group;
        }

        tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <header class="letterhead">
        <img class="logo" src="{{ asset('image/bagongpilipinas.png') }}" alt="Bagong Pilipinas">

        <div>
            <div class="agency">
                Republic of the Philippines<br>
                Asuncion Water District<br>
                Purok.4 Cambanoy, Asuncion<br>
                Davao del Norte
            </div>
            <div class="title">{{ $title }}</div>
        </div>

        <img class="logo" src="{{ asset('image/wdatransparent.png') }}" alt="WDA Logo">
    </header>

    <div class="meta">
        {{ $employee->full_name }} | {{ $employee->designation?->title }} | {{ $employee->department?->name }}<br>
        Date Generated: {{ $generatedDate }}
    </div>

    @if ($report === 'leave-history')
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Leave Type</th>
                    <th>Deducted</th>
                    <th>LWOP</th>
                    <th>Status</th>
                    <th>Comment</th>
                    <th>Approved and Checked by HR</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leaveRows as $leave)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="center">{{ $leave->leave_from?->format('m/d/Y') }}</td>
                        <td class="center">{{ $leave->leave_to?->format('m/d/Y') }}</td>
                        <td>{{ $leave->leave_type }}</td>
                        <td class="right">{{ number_format((float) $leave->deducted_points, 2) }}</td>
                        <td class="center">{{ $leave->is_lwop ? 'LWOP' : '' }}</td>
                        <td>{{ $leave->status }}</td>
                        <td>{{ $leave->approval_comment }}</td>
                        <td>{{ $leave->approved_checked_by_hr }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="center">No leave history yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table>
            <thead>
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
            </thead>
            <tbody>
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
            </tbody>
        </table>
    @endif

    @if ($autoPrint)
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif
</body>
</html>
