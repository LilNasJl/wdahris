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

        .title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            text-align: center;
        }

        .no-border {
            border: 0;
        }

        .line {
            border-bottom: 1px solid #000;
            text-align: center;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="10" class="center no-border">
                Republic of the Philippines<br>
                Asuncion Water District<br>
                Purok.4 Cambanoy, Asuncion<br>
                Davao del Norte
            </td>
        </tr>
        <tr>
            <td colspan="10" class="title no-border">SERVICE RECORD</td>
        </tr>
        <tr>
            <td colspan="10" class="center no-border">( To be accomplished by employer )</td>
        </tr>
        <tr>
            <td class="no-border"><strong>NAME:</strong></td>
            <td colspan="2" class="line">{{ $employee->lastname }}</td>
            <td colspan="2" class="line">{{ $employee->firstname }}</td>
            <td class="line">{{ $employee->middlename ? substr($employee->middlename, 0, 1) . '.' : '' }}</td>
            <td colspan="4" class="no-border">( If married woman, give also full maiden name )</td>
        </tr>
        <tr>
            <td class="no-border"></td>
            <td colspan="2" class="center no-border">(Surname)</td>
            <td colspan="2" class="center no-border">(Given Name)</td>
            <td class="center no-border">(M.I.)</td>
            <td colspan="4" class="no-border"></td>
        </tr>
        <tr>
            <td class="no-border"><strong>BIRTH:</strong></td>
            <td colspan="3" class="line">{{ $employee->birthdate ? \Illuminate\Support\Carbon::parse($employee->birthdate)->format('F d, Y') : '' }}</td>
            <td colspan="3" class="line">{{ $employee->address }}</td>
            <td colspan="3" class="no-border">( Date herein should be checked from birth or baptismal certificate or some other reliable documents )</td>
        </tr>
        <tr>
            <td colspan="10" class="no-border">
                This is to certify that the employee named herein above actually rendered services in this office as shown by the service record below, each line of which is supported by appointment and other papers actually issued by this office and approved by the authority.
            </td>
        </tr>
        <tr>
            <th colspan="2">SERVICE</th>
            <th colspan="3">RECORD OF APPOINTMENT</th>
            <th>OFFICE ENTITY</th>
            <th rowspan="2">BRANCH</th>
            <th rowspan="2">Leave of Absence<br>w/o Pay</th>
            <th colspan="2">Separation</th>
        </tr>
        <tr>
            <th colspan="2">Inclusive Dates</th>
            <th>Designation</th>
            <th>Status</th>
            <th>Salary</th>
            <th><em>Station/Place of Assignment</em></th>
            <th>Date</th>
            <th>Caused</th>
        </tr>
        <tr>
            <th>From</th>
            <th>To</th>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
            <th>6</th>
            <th colspan="2"></th>
        </tr>
        @forelse ($records as $record)
            <tr>
                <td class="center">{{ $record->from_date?->format('m/d/Y') }}</td>
                <td class="center">{{ $record->to_date?->format('m/d/Y') }}</td>
                <td>{{ $record->designation }}{{ filled($record->salary_step) ? ' / Step '.$record->salary_step : '' }}</td>
                <td>{{ $record->status }}</td>
                <td class="right">{{ blank($record->salary) ? '' : number_format((float) $record->salary, 2) }}</td>
                <td>{{ $record->station_place }}</td>
                <td>{{ $record->branch }}</td>
                <td>{{ $record->leave_without_pay }}</td>
                <td class="center">{{ $record->separation_date?->format('m/d/Y') }}</td>
                <td>{{ $record->separation_case ?: $record->remarks }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="center">No service credits encoded.</td>
            </tr>
        @endforelse
        <tr>
            <td colspan="2" class="line">{{ $generatedDate }}</td>
            <td colspan="5" class="center no-border">
                issued in compliance with Executive Order No. 5<br>
                August 10, 1954 and in accordance with Circular No. 5<br>
                August 10, 1954 of the system:
            </td>
            <td colspan="3" class="center no-border">CERTIFIED CORRECT:</td>
        </tr>
        <tr>
            <td colspan="7" class="no-border"></td>
            <td colspan="3" class="line">{{ $signatory?->name }}</td>
        </tr>
        <tr>
            <td colspan="7" class="no-border"></td>
            <td colspan="3" class="center no-border">{{ $signatory?->position }}</td>
        </tr>
    </table>
</body>
</html>
