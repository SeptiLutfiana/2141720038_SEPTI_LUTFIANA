<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Data Soft Kompetensi IDP Perum Perhutani</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        .header-container {
            position: relative;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }

        .logo {
            position: absolute;
            left: 0;
            top: -25;
            width: 100px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        h3 {
            margin-top: 40px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header-container">
        <img src="{{ public_path('./img/logo-perhutani.png') }}" class="logo" alt="Logo Perhutani">
        <div class="title">Data Soft Kompetensi <br>Individual Development Plan <br> Perum Perhutani</div>
    </div>
    {{-- Tabel Soft Competency --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kompetensi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($kompetensi->where('jenis_kompetensi', 'Soft Kompetensi') as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-left">{{ $item->nama_kompetensi }}</td>
                    <td class="text-left">{{ $item->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p style="text-align: right; font-size: 10px; margin-top: 20px;">
        Dicetak pada: {{ $waktuCetak }}
    </p>
</body>

</html>
