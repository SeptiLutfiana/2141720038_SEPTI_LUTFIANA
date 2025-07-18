<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Cetak Bank IDP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        .page {
            page-break-after: always;
            padding: 20px;
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
            top: -60px;
            width: 100px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #d3fba2;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* Supaya halaman terakhir tidak ada page break */
        .page:last-child {
            page-break-after: auto;
        }

        /* Styling khusus untuk heading section */
        .section-header {
            background-color: #1d6c0e;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .kompetensi-header {
            background-color: #a6c73a;
            color: #fff;
            font-weight: bold;
        }

        .aksi-header {
            background-color: #49aa36;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .sub-aksi-header {
            background-color: #b4e0ab;
            color: #000;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="header-container">
            <img src="{{ public_path('./img/logo-perhutani.png') }}" class="logo" alt="Logo Perhutani">
            <div class="title">Data Mapping IDP Perum Perhutani</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Proyeksi Karir</th>
                    <th>Deskripsi IDP</th>
                    <th>Supervisor</th>
                    <th>Waktu</th>
                    <th>Maksimal Kuota</th>
                    <th>Total Daftar</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($idps as $idp)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $idp->proyeksi_karir }}</td>
                        <td>{{ $idp->deskripsi_idp }}</td>
                        <td>{{ $idp->supervisor->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($idp->waktu_mulai)->translatedFormat('F Y') }} -
                            {{ \Carbon\Carbon::parse($idp->waktu_selesai)->translatedFormat('F Y') }}</td>
                        <td>{{ $idp->max_applies }} Karyawan</td>
                        <td>{{ $idp->current_applies }} Karyawan</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
