<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.45;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .company {
            font-size: 11px;
            text-align: center;
        }

        h1 {
            font-size: 18px;
            margin: 12px 0 4px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
        }

        th {
            text-align: center;
            font-weight: bold;
        }

        .no-border td {
            border: none;
            padding: 2px 0;
        }

        .highlight {
            background: #fff59d;
            font-weight: bold;
        }

        .right { text-align: right; }
        .center { text-align: center; }

        .footer-block {
            margin-top: 18px;
        }
    </style>
</head>

<body>

{{-- ================= HEADER ================= --}}
<div class="header">
    <div class="company">
        <strong>POLYCOOL</strong><br>
        POLYURETHANE INSULATION SERVICES<br>
        Henedina Street corner Obasa Avenue Carmenville, Calumpang, General Santos City<br>
        Globe/TM: 0955-336-2708 | Smart/Sun: 0932-191-8518<br>
        Email: polycoolpu@gmail.com
    </div>
</div>

<h1>Quotation</h1>
<p>{{ \Carbon\Carbon::parse($quotation->quotation_date)->format('F d, Y') }}</p>

{{-- ================= CLIENT ================= --}}
<p>
    <strong>{{ $quotation->project_name }}</strong><br>
    {{ $quotation->client->name }}<br>
    {{ $quotation->address }}
</p>

{{-- ================= DETAILS ================= --}}
<p class="section-title">Details:</p>
<p>
    Vessel: {{ $quotation->project_name }}<br>
    System: {{ $quotation->system }}<br>
    Scope of work: {{ $quotation->scope_of_work }}<br>
    Duration: {{ $quotation->duration_days }} working day(s)
</p>

{{-- ================= PARTICULARS ================= --}}
<p class="section-title">Particulars: {{ $quotation->project_name }}</p>

<table>
    <thead>
        <tr>
            <th>Substrate</th>
            <th>PU foam thickness</th>
            <th>PU foam volume</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quotation->items as $item)
        <tr>
            <td>{{ $item->substrate }}</td>
            <td class="center">{{ $item->thickness }}</td>
            <td class="right">{{ number_format($item->volume, 2) }} bd.ft.</td>
        </tr>
        @endforeach

        <tr>
            <td colspan="2" class="right"><strong>Totals:</strong></td>
            <td class="right"><strong>{{ number_format($quotation->total_bdft,2) }} bd.ft.</strong></td>
        </tr>
    </tbody>
</table>

{{-- ================= COSTING ================= --}}
<table style="margin-top:12px;">
    <tr>
        <td>Costing and terms: without VAT</td>
        <td class="center"><strong>Amount</strong></td>
    </tr>
    <tr>
        <td>
            {{ number_format($quotation->total_bdft,2) }} bd.ft.
            × Php {{ number_format($quotation->rate_per_bdft,2) }}/bd.ft.
        </td>
        <td class="right">
            Php {{ number_format($quotation->contract_price,2) }}
        </td>
    </tr>
    <tr class="highlight">
        <td><strong>Contract price</strong></td>
        <td class="right">
            Php {{ number_format($quotation->contract_price,2) }}
            – payable {{ $quotation->conditions ? 'as stated' : '30 days after completion' }}
        </td>
    </tr>
</table>

{{-- ================= CONDITIONS ================= --}}
<p class="section-title">Conditions:</p>
<p style="white-space: pre-line;">
{{ $quotation->conditions }}
</p>

{{-- ================= FOOTER ================= --}}
<div class="footer-block">
    <p>Thank you for your business! God bless!!!</p>

    <table class="no-border" width="100%">
        <tr>
            <td width="50%">
                Prepared by:<br><br>
                <strong>Elmer K. Tariao</strong><br>
                Project Manager<br>
                0955-336-2708
            </td>
            <td width="50%" class="right">
                Conforme:<br><br>
                ___________________________<br>
                Signature over printed name
            </td>
        </tr>
    </table>

    <p style="margin-top:12px;">
        Received by: ___________________________<br>
        Date received: _________________________
    </p>
</div>

</body>
</html>
