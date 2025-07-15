<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Data Jabatan Perum Perhutani</title>
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
            top: -55px;
            width: 100px;
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
        <div class="title">Data Jabatan Perum Perhutani</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Jabatan</th>
                <th>Jenjang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jabatan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $item->nama_jabatan }}</td>
                    <td class="text-left">{{ $item->jenjang->nama_jenjang }}</td>
                    <td class="text-left">{{ $item->keterangan }}</td>
                    <!-- Tampilkan nama jenjang -->
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
