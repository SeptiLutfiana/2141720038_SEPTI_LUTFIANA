<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        .header-container {
            position: relative;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }

        .logo {
            position: absolute;
            left: 0;
            top: -25px;
            width: 100px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>

<body>
    <div class="header-container">
        <img src="{{ public_path('img/logo-perhutani.png') }}" class="logo" alt="Logo Perhutani">
        <div class="title">
            Daftar IDP Masih Progres<br>
            Perhutani Forestry Institute <br><br>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Proyeksi Karir</th>
                <th>Nama Mentor</th>
                <th>Jenjang</th>
                <th>Jabatan</th>
                <th>Divisi</th>
                <th>Penempatan</th>
                <th>Direktorat</th>
                <th>Progres IDP</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($idps as $i => $item)
                @php
                    $total = $item->idpKompetensis->count();
                    $selesai = 0;
                    foreach ($item->idpKompetensis as $kom) {
                        $upload = $kom->pengerjaans->count();
                        $disetujui = $kom->pengerjaans->where('status_pengerjaan', 'Disetujui Mentor')->count();
                        if ($upload > 0 && $upload == $disetujui) {
                            $selesai++;
                        }
                    }
                    $persen = $total > 0 ? round(($selesai / $total) * 100) : 0;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->karyawan->name ?? '-' }}</td>
                    <td>{{ $item->proyeksi_karir ?? '-' }}</td>
                    <td>{{ $item->mentor->name ?? '-' }}</td>
                    <td>{{ $item->jenjang->nama_jenjang ?? '-' }}</td>
                    <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                    <td>{{ $item->divisi->nama_divisi ?? '-' }}</td>
                    <td>{{ $item->penempatan->nama_penempatan ?? '-' }}</td>
                    <td>{{ $item->learningGroup->nama_LG ?? '-' }}</td>
                    <td>{{ $selesai }}/{{ $total }} ({{ $persen }}%)</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p style="text-align: right; font-size: 10px; margin-top: 20px;">
        Dicetak: {{ $waktuCetak }}</p>
</body>

</html>
