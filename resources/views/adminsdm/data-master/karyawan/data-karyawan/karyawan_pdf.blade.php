<!DOCTYPE html>
<html>

<head>
    <title>Data Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 15px;
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
        <div class="title">Data Karyawan <br> Individual Development Plan <br> Perhutani Forestry Institute</div>

    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NPK</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Jenjang</th>
                <th>Learning Group</th>
                <th>Jabatan</th>
                <th>Penempatan</th>
                <th>Divisi</th>
                <th>Semester</th>
                <th>Jenjang</th>
                <th>Bulan Angkatan PSP</th>
                <th>Tahun Angkatan PSP</th>
                <th>Role</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user as $karyawan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $karyawan->npk ?? '-' }}</td>
                    <td>{{ $karyawan->name }}</td>
                    <td>{{ $karyawan->email }}</td>
                    <td>{{ $karyawan->no_hp ?? '-' }}</td>
                    <td>{{ $karyawan->jenjang->nama_jenjang ?? '-' }}</td>
                    <td>{{ $karyawan->learningGroup->nama_LG ?? '-' }}</td>
                    <td>{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                    <td>{{ $karyawan->penempatan->nama_penempatan ?? '-' }}</td>
                    <td>{{ $karyawan->divisi->nama_divisi ?? '-' }}</td>
                    <td>{{ $karyawan->semester->nama_semester ?? '-' }}</td>
                    <td>{{ $karyawan->jenjang->nama_jenjang ?? '-' }}</td>
                    <td>{{ $karyawan->angkatanpsp->bulan ?? '-' }}</td>
                    <td>{{ $karyawan->angkatanpsp->tahun ?? '-' }}</td>
                    <td> {{ $karyawan->roles->pluck('nama_role')->implode(', ') }} </td>
                    <td>{{ $karyawan->status ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p style="text-align: right; font-size: 10px; margin-top: 20px;">
        Dicetak pada: {{ $waktuCetak }}
    </p>
</body>

</html>
