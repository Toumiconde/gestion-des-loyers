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
            <td style="width: 150px; border: none; text-align: left; vertical-align: top;">
                @if($logo)
                    <img src="{{ $logo }}" style="width: 120px; height: auto;">
                @endif
            </td>
            <td style="border: none; text-align: center; vertical-align: middle;">
                <div style="font-size: 16pt; font-weight: bold; color: #1e293b;">{{ $company_name ?? 'GESTLOYER Immobilier' }}</div>
                <div style="font-size: 14pt; font-weight: bold; color: #475569; margin-top: 5px;">{{ $title }}</div>
                <div style="font-size: 9pt; color: #94a3b8;">Généré le {{ date('d/m/Y H:i') }}</div>
            </td>
            <td style="width: 150px; border: none; text-align: right; vertical-align: top;">
                <!-- Espace vide à droite du header si besoin -->
            </td>
        </tr>
    </table>

    <!-- DATA TABLE -->
    <table style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th style="background-color: #f8fafc; color: #334155; font-weight: bold; border: 1px solid #e2e8f0; padding: 12px; text-align: left; font-size: 10pt;">
                        {{ $h }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $cell)
                        <td style="border: 1px solid #e2e8f0; padding: 10px; text-align: left; font-size: 10pt; color: #475569;">
                            {{ $cell }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER -->
    <table style="width: 100%; border: none; margin-top: 40px;">
        <tr>
            <td style="border: none;"></td>
            <td style="width: 300px; border: none; text-align: right; vertical-align: bottom;">
                <p style="font-weight: bold; margin-bottom: 10px; font-size: 10pt;">Signature et Cachet Officiel :</p>
                @if($stamp)
                    <img src="{{ $stamp }}" style="width: 180px; height: auto;">
                @endif
                <p style="font-size: 8pt; color: #94a3b8; margin-top: 5px;">Document généré par GESTLOYER - Système Certifié</p>
            </td>
        </tr>
    </table>
</body>
</html>
