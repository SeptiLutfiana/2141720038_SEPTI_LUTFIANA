<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Data Pertanyaan Evaluasi</title>
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
            top: 0;
            width: 140px;
            /* diperbesar */
            height: auto;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
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
        <div class="title">Data Pertanyaan Evaluasi</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pertanyaan</th>
                <th>Untuk Role</th>
                <th>Jenis Pertanyaan</th>
                <th>Jenis Evaluasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bankEvaluasi as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $item->pertanyaan }}</td>
                    <td class="text-left">{{ $item->untuk_role }}</td>
                    <td class="text-left">{{ $item->tipe_pertanyaan }}</td>
                    <td class="text-left">{{ $item->jenis_evaluasi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p style="text-align: right; font-size: 10px; margin-top: 20px;">
        Dicetak pada: {{ $waktuCetak }}
    </p>
</body>

</html>
