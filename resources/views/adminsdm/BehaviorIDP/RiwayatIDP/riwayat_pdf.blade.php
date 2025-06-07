<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Detail IDP</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
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
            top: -15px;
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
        }

        th {
            background-color: #d3fba2;
            text-align: left;
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

    @foreach ($idps as $idp)
        <div class="page">
            <div class="header-container">
                <img src="{{ public_path('img/logo-perhutani.png') }}" class="logo" alt="Logo Perhutani">
                <div class="title">
                    Individual Development Plan <br>
                    Perhutani Forestry Institute <br><br>
                </div>
            </div>

            <small class="text-muted d-block mt-1">
                Nama Lengkap: {{ $idp->karyawan->name }} <br>
                NPK: {{ $idp->karyawan->npk }} <br>
                Jenjang: {{ $idp->jenjang->nama_jenjang ?? '-' }} <br>
                Jabatan: {{ $idp->jabatan->nama_jabatan ?? '-' }} <br>
                Divisi: {{ $idp->divisi->nama_divisi ?? '-' }} <br>
                Penempatan: {{ $idp->penempatan->nama_penempatan ?? '-' }} <br>
                Learning Group: {{ $idp->learninggroup->nama_LG ?? '-' }} <br>
                Semester: {{ $idp->semester->nama_semester ?? '-' }} <br>
                Angkatan PSP: {{ $idp->angkatanpsp->bulan ?? '-' }} {{ $idp->angkatanpsp->tahun ?? '-' }} <br>
            </small>
            <br>

            <table>
                <tr>
                    <td style="width: 180px;">Proyeksi Karir</td>
                    <td colspan="4">{{ $idp->proyeksi_karir }}</td>
                </tr>
                <tr>
                    <td>Deskripsi IDP</td>
                    <td colspan="4">{{ $idp->deskripsi_idp }}</td>
                </tr>
                <tr>
                    <td>Mentor</td>
                    <td colspan="4">{{ $idp->mentor->name }}</td>
                </tr>
                <tr>
                    <td>Supervisor</td>
                    <td colspan="4">{{ $idp->supervisor->name }}</td>
                </tr>
                <tr>
                    <td>Waktu Mulai</td>
                    <td colspan="4">{{ \Carbon\Carbon::parse($idp->waktu_mulai)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td>Waktu Selesai</td>
                    <td colspan="4">{{ \Carbon\Carbon::parse($idp->waktu_selesai)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td>Hasil Rekomendasi</td>
                    <td colspan="4">
                        <div style="font-weight: bold;">
                            {{ $idp->rekomendasis->first()->hasil_rekomendasi}}
                        </div>
                        <div>
                            {{ $idp->rekomendasis->first()->deskripsi_rekomendasi }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <th colspan="5" class="section-header">
                        Development Area
                    </th>
                </tr>

                {{-- Soft Kompetensi --}}
                <tr>
                    <th colspan="5" class="kompetensi-header text-left">
                        Soft Kompetensi
                    </th>
                </tr>
                @foreach ($idp->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Soft Kompetensi') as $kom)
                    <tr>
                        <th colspan="5">{{ $kom->kompetensi->nama_kompetensi }}</th>
                    </tr>
                    <tr>
                        <td colspan="5">{{ $kom->kompetensi->keterangan }}</td>
                    </tr>
                    <tr>
                        <td>Sasaran</td>
                        <td colspan="4">{!! nl2br(e($kom->sasaran)) !!}</td>
                    </tr>
                    <tr>
                        <td>Metode Belajar</td>
                        <td colspan="4">{{ $kom->metodeBelajars->pluck('nama_metodeBelajar')->implode(', ') }}</td>
                    </tr>
                    <tr>
                        <td>Aksi</td>
                        <td colspan="4">{!! nl2br(e($kom->aksi)) !!}</td>
                    </tr>
                    <tr>
                        <th colspan="5" class="aksi-header">
                            Aksi Implementasi
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" class="sub-aksi-header">
                            Keterangan
                        </th>
                        <th class="sub-aksi-header" style="width:150px; max-width:150px;">
                            Rating
                        </th>
                    </tr>
                    @foreach ($kom->pengerjaans as $peng)
                        <tr>
                            <td colspan="4" class="text-left">{{ $peng->keterangan_hasil }}</td>
                            <td class="text-center">{{ $peng->nilaiPengerjaanIdp->rating }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <tr>
                    <th colspan="4">Nilai Soft Kompetensi</th>
                    <th class="text-center">{{ $idp->rekomendasis->first()->nilai_akhir_soft ?? '-' }}</th>
                </tr>

                {{-- Hard Kompetensi --}}
                <tr>
                    <th colspan="5" class="kompetensi-header">
                        Hard Kompetensi
                    </th>
                </tr>
                @foreach ($idp->idpKompetensis->where('kompetensi.jenis_kompetensi', 'Hard Kompetensi') as $kom)
                    <tr>
                        <th colspan="5">{{ $kom->kompetensi->nama_kompetensi }}</th>
                    </tr>
                    <tr>
                        <td colspan="5">{{ $kom->kompetensi->keterangan }}</td>
                    </tr>
                    <tr>
                        <td>Sasaran</td>
                        <td colspan="4">{!! nl2br(e($kom->sasaran)) !!}</td>
                    </tr>
                    <tr>
                        <td>Metode Belajar</td>
                        <td colspan="4">{{ $kom->metodeBelajars->pluck('nama_metodeBelajar')->implode(', ') }}</td>
                    </tr>
                    <tr>
                        <td>Aksi</td>
                        <td colspan="4">{!! nl2br(e($kom->aksi)) !!}</td>
                    </tr>
                    <tr>
                        <th colspan="5" class="aksi-header">
                            Aksi Implementasi
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" class="sub-aksi-header">
                            Keterangan
                        </th>
                        <th class="sub-aksi-header" style="width:150px; max-width:150px;">
                            Rating
                        </th>
                    </tr>
                    @foreach ($kom->pengerjaans as $peng)
                        <tr>
                            <td colspan="4" class="text-left">{{ $peng->keterangan_hasil }}</td>
                            <td class="text-center">{{ $peng->nilaiPengerjaanIdp->rating }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <tr>
                    <th colspan="4">Nilai Hard Kompetensi</th>
                    <th class="text-center">{{ $idp->rekomendasis->first()->nilai_akhir_hard}}</th>
                </tr>
            </table>

            <p style="text-align: right; font-size: 10px; margin-top: 20px;">
                Dicetak pada: {{ $waktuCetak }}
            </p>
        </div>
    @endforeach

</body>

</html>
