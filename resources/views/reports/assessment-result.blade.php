<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Penilaian Pembinaan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .container {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        th, td {
            padding: 6px;
            vertical-align: top;
        }

        .no-border td {
            border: none;
            padding: 3px 0;
        }

        .section-title {
            background: #e5e5e5;
            padding: 5px;
            font-weight: bold;
            border: 1px solid #000;
        }

        .footer {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="text-center mb-20">
        <h3 class="fw-bold">HASIL PENILAIAN PEMBINAAN NARAPIDANA</h3>
        <div>{{ $lapas ?? 'LEMBAGA PEMASYARAKATAN KELAS III LEMBATA' }}</div>
        <div>{{ $kategori_lapas ?? 'LAPAS MEDIUM SECURITY' }}</div>
    </div>

    <!-- DATA NARAPIDANA -->
    <table class="no-border mb-20">
        <tr>
            <td width="25%">Nama Narapidana</td>
            <td width="2%">:</td>
            <td>{{ $nama ?? 'PETRUS SABON AMA DOSI BIN ALM. LASARUS LESU DURAN' }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $jenis_kelamin ?? 'Laki-laki' }}</td>
        </tr>
        <tr>
            <td>Tindak Pidana</td>
            <td>:</td>
            <td>{{ $tindak_pidana ?? 'Korupsi' }}</td>
        </tr>
        <tr>
            <td>Lama Pidana</td>
            <td>:</td>
            <td>{{ $lama_pidana ?? '96 Bulan' }}</td>
        </tr>
        <tr>
            <td>Bulan Penilaian</td>
            <td>:</td>
            <td>{{ $bulan ?? 'Februari 2025' }}</td>
        </tr>
    </table>

    <!-- PENILAIAN KEPRIBADIAN -->
    <div class="section-title">Total Penilaian Pembinaan Kepribadian</div>
    <table class="mb-20">
        <tr>
            <th>Aspek</th>
            <th width="20%">Skor</th>
            <th width="25%">Keterangan</th>
        </tr>
        <tr>
            <td>Kesadaran Beragama</td>
            <td class="text-center">{{ $kesadaran_beragama ?? '99,26' }}</td>
            <td>Sangat Baik</td>
        </tr>
        <tr>
            <td>Kesadaran Hukum, Berbangsa, dan Bernegara</td>
            <td class="text-center">{{ $kesadaran_hukum ?? '50,00' }}</td>
            <td>Cukup Baik</td>
        </tr>
        <tr>
            <td>Kemampuan Intelektual</td>
            <td class="text-center">{{ $intelektual ?? '65,93' }}</td>
            <td>Baik</td>
        </tr>
        <tr>
            <td>Psikosomatis</td>
            <td class="text-center">{{ $psikosomatis ?? '36,82' }}</td>
            <td>Sangat Tidak Baik</td>
        </tr>
    </table>

    <!-- KONDISI MENTAL -->
    <div class="section-title">Total Penilaian Kondisi Mental</div>
    <table class="mb-20">
        <tr>
            <th>Aspek</th>
            <th width="20%">Skor</th>
            <th width="25%">Keterangan</th>
        </tr>
        <tr>
            <td>Depresi</td>
            <td class="text-center">{{ $depresi ?? '100,00' }}</td>
            <td>Sangat Sehat Mental</td>
        </tr>
        <tr>
            <td>Kecemasan</td>
            <td class="text-center">{{ $kecemasan ?? '100,00' }}</td>
            <td>Sangat Sehat Mental</td>
        </tr>
        <tr>
            <td>Ekspresi Simbolik</td>
            <td class="text-center">{{ $ekspresi ?? '97,11' }}</td>
            <td>Sangat Patuh</td>
        </tr>
    </table>

    <!-- KEMANDIRIAN -->
    <div class="section-title">Total Penilaian Pembinaan Kemandirian</div>
    <table class="mb-20">
        <tr>
            <th>Aspek</th>
            <th width="20%">Skor</th>
            <th width="25%">Keterangan</th>
        </tr>
        <tr>
            <td>Pelatihan Keterampilan</td>
            <td class="text-center">{{ $pelatihan ?? '0,00' }}</td>
            <td>Tidak Diikutkan</td>
        </tr>
        <tr>
            <td>Produksi Barang/Jasa</td>
            <td class="text-center">{{ $produksi ?? '73,64' }}</td>
            <td>Cukup Baik</td>
        </tr>
    </table>

    <!-- CATATAN -->
    <div class="section-title">Catatan Skor</div>
    <div style="border:1px solid #000; padding:8px;" class="mb-20">
        {{ $catatan ?? 'Bulan Februari tidak dilaksanakan upacara bendera dan tidak dilakukan pemeriksaan kesehatan maupun konseling.' }}
    </div>

    <!-- REKOMENDASI -->
    <div class="section-title">Rekomendasi</div>
    <div style="border:1px solid #000; padding:8px;">
        {{ $rekomendasi ?? 'WBP menunjukkan sikap yang wajar dan menyesali perbuatan, rutin mengikuti pembinaan, menghormati petugas dan sesama WBP. Dapat diusulkan memperoleh hak bersyarat berupa pembebasan bersyarat.' }}
    </div>

    <!-- FOOTER -->
    <div class="footer text-right">
        <br><br>
        Kupang, {{ date('d F Y') }}<br>
        Petugas Penilai,
        <br><br><br>
        _______________________
    </div>

</div>

</body>
</html>
