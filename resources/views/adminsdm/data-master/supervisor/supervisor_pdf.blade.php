<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Data Supervisor Individual Development Plan Perum Perhutani</title>
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
            top: -35px;
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
        <div class="title">Data Supervisor <br>Individual Development Plan <br> Perum Perhutani</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Supervisor</th>
                <th>NPK</th>
                <th>No Telepon</th>
                <th>Jabatan</th>
                <th>Penempatan</th>
                <th>Divisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supervisor as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $item->user->name }}</td>
                    <td class="text-left">{{ $item->user->npk }}</td>
                    <td class="text-left">{{ $item->user->no_hp }}</td>
                    <td class="text-left">{{ $item->user->jabatan->nama_jabatan }}</td>
                    <td class="text-left">{{ $item->user->penempatan->nama_penempatan }}</td>
                    <td class="text-left">{{ $item->user->divisi->nama_divisi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
