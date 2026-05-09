<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        .header { text-align: center; margin-bottom: 20px; }
        .logo { width: 100px; height: auto; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #f1f5f9; color: #000; font-weight: bold; border: 1px solid #cbd5e1; padding: 10px; }
        td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        .title { font-size: 18pt; font-weight: bold; color: #1e293b; margin-bottom: 10px; }
        .footer { margin-top: 30px; text-align: right; }
        .stamp { width: 150px; height: auto; }
        .company-name { font-size: 14pt; font-weight: bold; color: #475569; }
    </style>
</head>
<body>
    <!-- HEADER -->
    <table style="width: 100%; border: none; margin-bottom: 20px;">
        <tr>
            <td style="width: 100px; border: none; text-align: left; vertical-align: top;">
                @if($logo)
                    <img src="{{ $logo }}" width="60" height="60" style="width: 60px; height: 60px;">
                @endif
            </td>
            <td style="border: none; text-align: center; vertical-align: middle;">
                <div style="font-size: 14pt; font-weight: bold; color: #1e293b;">{{ $company_name ?? 'GESTLOYER Immobilier' }}</div>
                <div style="font-size: 12pt; font-weight: bold; color: #475569;">{{ $title }}</div>
                <div style="font-size: 8pt; color: #94a3b8;">{{ date('d/m/Y H:i') }}</div>
            </td>
            <td style="width: 100px; border: none;"></td>
        </tr>
    </table>

    <!-- DATA TABLE -->
    <table style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th style="background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 5px; text-align: left; font-size: 9pt;">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $cell)
                        <td style="border: 1px solid #cbd5e1; padding: 5px; text-align: left; font-size: 9pt;">{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER -->
    <table style="width: 100%; border: none; margin-top: 20px;">
        <tr>
            <td style="border: none;"></td>
            <td style="width: 200px; border: none; text-align: right; vertical-align: bottom;">
                <p style="font-weight: bold; font-size: 9pt; margin-bottom: 5px;">Cachet de l'Agence :</p>
                @if($stamp)
                    <img src="{{ $stamp }}" width="100" height="100" style="width: 100px; height: 100px;">
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
