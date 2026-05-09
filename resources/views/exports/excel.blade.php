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
    <div class="header">
        @if($logo)
            <img src="{{ $logo }}" class="logo"><br>
        @endif
        <span class="company-name">{{ $company_name ?? 'GESTLOYER - AGENCE IMMOBILIÈRE' }}</span><br>
        <span class="title">{{ $title }}</span><br>
        <span>Généré le {{ date('d/m/Y H:i') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Signature et Cachet de l'Agence :</p>
        @if($stamp)
            <img src="{{ $stamp }}" class="stamp">
        @else
            <div style="height: 100px;"></div>
        @endif
        <p>Document Officiel GESTLOYER</p>
    </div>
</body>
</html>
