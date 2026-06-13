<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Record - {{ $employee->full_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 11mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            margin: 0;
        }

        .sheet {
            margin: 0 auto;
            width: 100%;
        }

        .letterhead {
            align-items: start;
            display: grid;
            grid-template-columns: 74px 1fr 74px;
            gap: 8px;
            margin-bottom: 8px;
        }

        .logo {
            height: 58px;
            object-fit: contain;
            width: 74px;
        }

        .letterhead-text {
            text-align: center;
        }

        .agency {
            font-size: 10px;
            line-height: 1.12;
            margin-bottom: 10px;
        }

        .title {
            font-size: 18px;
            letter-spacing: 1px;
            line-height: 1;
            margin: 0;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 10px;
            margin-top: 2px;
        }

        .identity {
            margin-bottom: 8px;
            width: 100%;
        }

        .identity td {
            padding: 2px 3px;
            vertical-align: bottom;
        }

        .identity .label {
            font-weight: 700;
            white-space: nowrap;
            width: 42px;
        }

        .line {
            border-bottom: 1px solid #000;
            height: 14px;
            text-align: center;
        }

        .hint {
            font-size: 9px;
            text-align: center;
        }

        .note {
            font-size: 9.5px;
            margin: 6px 0 5px;
            text-align: justify;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .records {
            table-layout: fixed;
        }

        .records th,
        .records td {
            border: 1px solid #000;
            padding: 2px 3px;
            vertical-align: top;
        }

        .records th {
            font-weight: 700;
            line-height: 1.05;
            text-align: center;
        }

        .records .top-group {
            font-size: 10px;
            text-transform: uppercase;
        }

        .records .sub-group {
            font-size: 9px;
        }

        .records .number-row th {
            font-size: 9px;
            font-weight: 400;
        }

        .record-row td {
            height: 30px;
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

        .footer {
            align-items: end;
            display: grid;
            grid-template-columns: 160px 1fr 230px;
            gap: 18px;
            margin-top: 12px;
        }

        .issued {
            font-size: 9px;
            line-height: 1.25;
            text-align: center;
        }

        .certified {
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            height: 32px;
            margin: 0 auto 4px;
            width: 170px;
        }

        .signatory-name {
            font-weight: 700;
            text-transform: uppercase;
        }

        .signatory-position {
            font-size: 9px;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <main class="sheet">
        <header class="letterhead">
            <img class="logo" src="{{ asset('image/bagongpilipinas.png') }}" alt="Bagong Pilipinas">

            <div class="letterhead-text">
                <div class="agency">
                    Republic of the Philippines<br>
                    Asuncion Water District<br>
                    Purok.4 Cambanoy, Asuncion<br>
                    Davao del Norte
                </div>
                <h1 class="title">Service Record</h1>
                <div class="subtitle">( To be accomplished by employer )</div>
            </div>

            <img class="logo" src="{{ asset('image/wdatransparent.png') }}" alt="WDA Logo">
        </header>

        <table class="identity">
            <tbody>
                <tr>
                    <td class="label">NAME:</td>
                    <td class="line">{{ $employee->lastname }}</td>
                    <td class="line">{{ $employee->firstname }}</td>
                    <td class="line">{{ $employee->middlename ? substr($employee->middlename, 0, 1) . '.' : '' }}</td>
                    <td style="width: 12px;"></td>
                    <td colspan="2">( If married woman, give also full maiden name )</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="hint">(Surname)</td>
                    <td class="hint">(Given Name)</td>
                    <td class="hint">(M.I.)</td>
                    <td></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td class="label">BIRTH:</td>
                    <td class="line" colspan="2">{{ $employee->birthdate ? \Illuminate\Support\Carbon::parse($employee->birthdate)->format('F d, Y') : '' }}</td>
                    <td class="line" colspan="2">{{ $employee->address }}</td>
                    <td style="width: 12px;"></td>
                    <td>( Date herein should be checked from birth or baptismal certificate or some other reliable documents )</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="hint" colspan="2">(Date)</td>
                    <td class="hint" colspan="2">(Place)</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <p class="note">
            This is to certify that the employee named herein above actually rendered services in this office as shown by the service record below, each line of which is supported by appointment and other papers actually issued by this office and approved by the authority.
        </p>

        <table class="records">
            <colgroup>
                <col style="width: 7.5%;">
                <col style="width: 7.5%;">
                <col style="width: 15%;">
                <col style="width: 12%;">
                <col style="width: 12%;">
                <col style="width: 13%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
            </colgroup>
            <thead>
                <tr>
                    <th class="top-group" colspan="2">Service</th>
                    <th class="top-group" colspan="3">Record of Appointment</th>
                    <th class="top-group">Office Entity</th>
                    <th class="top-group" rowspan="2">Branch</th>
                    <th class="top-group" rowspan="2">LV. / AB.<br>w/o Pay</th>
                    <th class="top-group" colspan="2">Separation</th>
                </tr>
                <tr>
                    <th class="sub-group" colspan="2">Inclusive Dates</th>
                    <th class="sub-group">Designation</th>
                    <th class="sub-group">Status</th>
                    <th class="sub-group">Salary</th>
                    <th class="sub-group"><em>Station/Place of Assignment</em></th>
                    <th class="sub-group">Date</th>
                    <th class="sub-group">Caused</th>
                </tr>
                <tr class="number-row">
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
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr class="record-row">
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
                    <tr class="record-row">
                        <td class="center" colspan="10">No service credits encoded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <section class="footer">
            <div>
                <div class="line">{{ $generatedDate }}</div>
                <div class="hint">(Date)</div>
            </div>

            <div class="issued">
                issued in compliance with Executive Order No. 5<br>
                August 10, 1954 and in accordance with Circular No. 5<br>
                August 10, 1954 of the system:
            </div>

            <div class="certified">
                <div>CERTIFIED CORRECT:</div>
                <div class="signature-line"></div>
                <div class="signatory-name">{{ $signatory?->name }}</div>
                <div class="signatory-position">{{ $signatory?->position }}</div>
            </div>
        </section>
    </main>

    @if ($autoPrint)
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif
</body>
</html>
