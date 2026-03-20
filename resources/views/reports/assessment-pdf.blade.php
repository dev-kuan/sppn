<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penilaian - {{ $assessment->inmate->full_name }}</title>

    <style>
        @page {
            margin: 2cm 1.5cm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: black;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border: none;
            padding-bottom: 15px;
        }



        .logo {
            border: none;
            padding: none;
        }

        /* SECTION */
        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            padding: 6px;
            margin-bottom: 10px;
        }
        .doc-header {
            width: 100%;
        }

        .inmate-data,
        .inmate-header,{
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .inmate-header td,
        .inmate-data td,
        .inmate-data th,
        .doc-header th {
            padding: 5px;
            vertical-align: middle;
            border: 1px solid black;
        }

        .inmate-header {
            border-bottom: none;
        }

        .label,
        .header-label {
            font-weight: bold;
            width: 24%;
        }


        .header-colon {
            width: 2%;
            text-align: center;
            white-space: nowrap;
        }

        .colon {
            width: 2%;
            text-align: center;
        }

        .value {
            width: 25%;
            text-transform: capitalize;
        }

        .right-label {
            font-weight: bold;
            width: 22%;
        }

        .right-value {
            width: 25%;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        .sub-header {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        .section-title {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
        }


        /* INFO GRID */
        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            padding: 6px;
            border: 1px solid black;
            font-weight: bold;
            background: #f2f2f2;
        }

        .info-value {
            display: table-cell;
            padding: 6px;
            border: 1px solid black;
        }

        /* TABLE */
        .observation {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
        }

        .observation th {
            border: 1px solid black;
            padding: 8px;
            font-size: 12px;
            font-weight: bold;
            text-align: left;
            background: #e6e6e6;
        }

        .observation td {
            border: 1px solid black;
            padding: 6px;
        }


        /* BOX */
        .commitment-box,
        .recommendation-box {
            border: 1px solid black;
            padding: 10px;
            margin-bottom: 10px;
        }

        /* SIGNATURE */
        .signature-section {
            margin-top: 40px;
        }

        .signature-cell {
            position: relative;
            width: 33%;
            display: table-cell;
            text-align: center;
        }

        .signature-line {
            padding-top: 4rem;
        }

        .role,
        .name,
        .nip {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-weight: bold;
        }

        .name {
            margin: -2;
            padding: -2;
        }

        .result h3 {
            margin: 0;
            padding: 0;
        }

        .result .text-center {
            text-align: center;
        }

        .result .text-right {
            text-align: right;
        }

        .result .fw-bold {
            font-weight: bold;
        }

        .result table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #000;
        }

        .result-data table,
        .result-data th,
        .result td,
        .result-data td {
            border: 1px solid #000;
        }

        .result th {
            background: #f2f2f2;
            font-weight: bold;
            padding: 6px;
        }

        .result td {
            padding: 6px;
            vertical-align: top;
        }

        .result .no-border td {
            border: none;
            padding: 3px 0;
        }

        .result .section-title {
            background: #e6e6e6;
            padding: 6px;
            font-weight: bold;
            border: 1px solid #000;
            margin-top: 10px;
        }

        .result .recommendation-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <!-- HEADER INSTANSI -->
    <table class="doc-header">
        <tr>
            <td class="logo" style="width: 80px; text-align: center; vertical-align: middle;">
                <img src="{{ public_path('image066.png') }}" alt="logo">
            </td>
            <td colspan="5" class="header" style="padding-right: 80px; text-align: center; vertical-align: middle;">
                DIREKTORAT JENDERAL PEMASYARAKATAN<br>
                KEMENTERIAN HUKUM DAN HAK ASASI MANUSIA<br>
                REPUBLIK INDONESIA
            </td>
        </tr>

        <tr>
            <td colspan="6" class="sub-header" style="text-align: center; vertical-align: middle;">
                LEMBAR PENILAIAN PEMBINAAN NARAPIDANA<br>
                LAPAS MEDIUM SECURITY
            </td>
        </tr>

        <tr>
            <td colspan="6" class="section-title">
                DATA DEMOGRAFI NARAPIDANA
            </td>
        </tr>
    </table>
    <table class="inmate-header">
        <tr>
            <td class="header-label">Nama Narapidana</td>
            <td class="header-colon">:</td>
            <td colspan="4" class="header-value">{{ $assessment->inmate->nama }}</td>
        </tr>

        <tr>
            <td class="header-label">Nama Lembaga Pemasyarakatan</td>
            <td class="header-colon">:</td>
            <td colspan="4" class="header-value">{{ $institution['name'] ?: 'Lapas' }}</td>
        </tr>

    </table>
    <table class="inmate-data">
        <!-- DATA -->
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td class="colon">:</td>
            <td class="value">{{ $assessment->inmate->jenis_kelamin }}</td>

            <td class="right-label">Tindak Pidana</td>
            <td class="colon">:</td>
            <td class="right-value">{{ $assessment->inmate->crimeType->nama }}</td>
        </tr>

        <tr>
            <td class="label">Tempat & Tanggal Lahir</td>
            <td class="colon">:</td>
            <td class="value"> {{ $assessment->inmate->tempat_lahir ?? '-' }},
                {{ $assessment->inmate->tanggal_lahir?->translatedFormat('d F Y') ?? '-' }}
            </td>

            <td class="right-label">Lama Pidana (bulan)</td>
            <td class="colon">:</td>
            <td class="right-value">{{ $assessment->inmate->lama_pidana_bulan ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Usia</td>
            <td class="colon">:</td>
            <td class="value">36 Tahun</td>

            <td class="right-label">Sisa Pidana (bulan)</td>
            <td class="colon">:</td>
            <td class="right-value">{{ $assessment->inmate->sisa_pidana_bulan ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Agama</td>
            <td class="colon">:</td>
            <td class="value">{{ $assessment->inmate->agama }}</td>

            <td rowspan="1" class="right-label">Jumlah Residivisme</td>
            <td class="colon">:</td>
            <td rowspan="1" class="right-value">1x</td>
        </tr>

        <tr>
            <td class="label">Pendidikan Terakhir</td>
            <td class="colon">:</td>
            <td class="value">{{ $assessment->inmate->tingkat_pendidikan ?? '-' }}</td>

            <td class="right-label">Penyakit / Perawatan</td>
            <td class="colon">:</td>
            <td class="right-value">Tidak Ada</td>
        </tr>

        <tr>
            <td class="label">Pekerjaan Terakhir</td>
            <td class="colon">:</td>
            <td class="value">{{ $assessment->inmate->pekerjaaan_terakhir ?? '-' }}</td>

            <td class="right-label">Kegiatan Produksi Kerja</td>
            <td class="colon">:</td>
            <td class="right-value">{{ $assessment->inmate->program_kerja ?? '-' }}</td>
        </tr>


        <tr>
            <td class="label">Pelatihan Keterampilan</td>
            <td class="colon">:</td>
            <td class="value">{{ $assessment->inmate->pelatihan ?? '-' }}</td>

            <td></td>
            <td></td>
            <td></td>
        </tr>

        <!-- FOOTER DATA -->
        <tr>
            <td colspan="1"><b>Tanggal Awal Pengisian</b></td>
            <td class="colon">:</td>
            <td>{{ $assessment->tanggal_penilaian->format('d') }}</td>

            <td colspan="1"><b>Bulan Pengisian</b></td>
            <td class="colon">:</td>
            <td>{{ $assessment->tanggal_penilaian->translatedFormat('F Y') }}
                ({{ $assessment->tanggal_penilaian->daysInMonth }} hari)</td>
        </tr>

    </table>
    <!-- OBSERVASI -->
    <div class="section">
        <div class="section-title">Hasil Observasi</div>

        @foreach ($observationData as $varIndex => $variabel)
            @if ($varIndex > 0)
                <div style="page-break-before: always;"></div>
            @endif

            {{-- Nama Variabel --}}
            <div style="font-weight: bold; font-size: 12pt; margin-bottom: 8px;">
                {{ $variabel['nama'] }}
            </div>

            <table class="observation">
                {{-- Header kolom — hanya sekali di atas tabel variabel ini --}}
                <thead>
                    <tr>
                        <th style="width:5%;">No</th>
                        <th style="width:49%;">Item Observasi Narapidana</th>
                        <th style="width:12%;">Frekuensi</th>
                        <th style="width:12%;">Tercatat</th>
                        <th style="width:12%;">Persentase</th>
                        <th style="width:10%;">Skor</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($variabel['aspects'] as $aspekIndex => $aspek)
                        {{-- Baris Nama Aspek (colspan penuh) --}}
                        <tr style="background: #e6e6e6;">
                            <td colspan="6" style="font-weight: bold; padding: 6px 8px;">
                                {{ $aspekIndex + 1 }}. {{ $aspek['nama'] }}
                            </td>
                        </tr>

                        {{-- Baris item-item --}}
                        @foreach ($aspek['items'] as $itemIndex => $item)
                            <tr>
                                <td style="text-align:center;">{{ $itemIndex + 1 }}</td>
                                <td>{{ $item['nama_item'] }}</td>
                                <td style="text-align:center;">{{ $item['frekuensi'] }}</td>
                                <td style="text-align:center;">{{ $item['checked_count'] }}</td>
                                <td style="text-align:center;">{{ number_format($item['percentage'], 1) }}%</td>
                                <td style="text-align:center;">{{ number_format($item['item_score'], 1) }}</td>
                            </tr>
                        @endforeach
                        {{-- Baris Skor Aspek --}}
                        <tr>
                            <td colspan="5" style="text-align:right; font-weight:bold; font-style:italic; padding-right: 10px;">
                                Skor Aspek {{ $aspek['nama'] }}
                            </td>
                            <td style="text-align:center; font-weight:bold;">
                                {{ $aspek['skor_aspek'] }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- Baris Skor Variabel --}}
                    <tr style="background: #f2f2f2;">
                        <td colspan="5" style="text-align:right; font-weight:bold; padding-right: 10px;">
                            Skor Variabel {{ $variabel['nama'] }}
                        </td>
                        <td style="text-align:center; font-weight:bold;">
                            {{ $variabel['skor_variabel'] }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
    {{--
    <!-- Pernyataan Komitmen -->
    @if ($assessment->commitmentStatements->isNotEmpty())
    <div class="section">
        <div class="section-title">Pernyataan Komitmen</div>
        @foreach ($assessment->commitmentStatements as $index => $statement)
        <div class="commitment-box">
            <strong>{{ $index + 1 }}.</strong> {{ $statement->statement }}
        </div>
        @endforeach
    </div>
    @endif --}}
    {{-- hasil Penilaian --}}
    <!-- HEADER -->
    <div style="page-break-before: always;"></div>
    <div class="result">
        <div class="text-center" style="margin-bottom:15px;">
            <h3 class="fw-bold">HASIL PENILAIAN PEMBINAAN NARAPIDANA</h3>
        </div>

        <!-- DATA NARAPIDANA -->
        <table style="margin-bottom:15px;">
            <tr>
                <td width="25%">Nama Narapidana</td>
                <td width="2%">:</td>
                <td>{{ $assessment->inmate->nama ?? '-' }}</td>

                <td width="25%">Nama Lapas</td>
                <td width="2%">:</td>
                <td>{{ $institution['name'] ?: 'Lapas' }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $assessment->inmate->jenis_kelamin ?? '-' }}</td>

                <td>Kategori Lapas</td>
                <td>:</td>
                <td>{{ $institution['kategori_lapas'] ?: 'Kategori Lapas' }}</td>
            </tr>
            <tr>
                <td>Tindak Pidana</td>
                <td>:</td>
                <td>{{ $assessment->inmate->crimeType->nama ?? '-' }}</td>

                <td>Bulan Penilaian</td>
                <td>:</td>
                <td>
                    {{ optional($assessment->tanggal_penilaian)->translatedFormat('F Y') ?? '-' }}
                </td>
            </tr>
            <tr>
                <td>Lama Pidana</td>
                <td>:</td>
                <td>{{ $assessment->inmate->lama_pidana_bulan ?? 0 }} Bulan</td>

                <td>Keterangan Lainnya</td>
                <td>:</td>
                <td> - </td>
            </tr>
        </table>

        <!-- Assessment Result DATA -->
        @foreach ($rekapData as $varIndex => $variabel)
            {{-- @if ($varIndex > 0)
            <div style="page-break-before: always;"></div>
            @endif --}}

            {{-- <div class="section-title">
                {{ $variabel['nama'] }}
            </div> --}}

            <table class="result-data">
                <thead>
                    <tr>
                        <th style="width:30%;">Variabel & Aspek</th>
                        <th style="width:15%;">Skor</th>
                        <th style="width:20%;">Kategori Skor</th>
                        <th style="width:35%;">Catatan Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: #e6e6e6;">
                        <td style="font-weight: bold; padding: 6px 8px;">
                            {{ $variabel['nama'] }}
                        </td>

                        @php
                            $key = strtolower(collect(explode(' ', trim($variabel['nama'])))->filter()->last());
                            $skorField = 'skor_' . $key;
                            $kategoriField = 'kategori_' . $key;
                        @endphp

                        <td class="text-center">
                            {{ number_format($assessment->$skorField ?? 0, 2) }}
                        </td>

                        <td class="text-center">
                            {{ $assessment->$kategoriField ?? '-' }}
                        </td>
                        <td class="text-center">
                            -
                        </td>
                    </tr>
                    @foreach ($variabel['aspects'] as $aspekIndex => $aspek)
                        <tr>
                            <td>
                                {{ $aspekIndex + 1 }}.
                                {{ $aspek['nama'] }}
                            </td>

                            <td class="text-center">
                                {{ number_format($aspek['skor_aspek'], 2, ',', '.') }}
                            </td>

                            <td class="text-center">
                                {{ $aspek['kategori'] ?? '-' }}
                            </td>

                            <td>
                                {{ $aspek['catatan'] ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

        <!-- REKOMENDASI -->
        @if ($assessment->commitmentRecommendations)
            <div class="section-title">REKOMENDASI</div>

            @foreach ($assessment->commitmentRecommendations as $index => $recommendation)
                <div class="recommendation-box">
                    <p>
                        <strong>{{ $index + 1 }}.</strong>
                        {{ $recommendation->recommendation }}
                    </p>

                    <p style="margin-top:6px; font-size:9pt;">
                        <em>
                            Oleh: {{ $recommendation->recommender->name ?? '-' }}
                            ({{ $recommendation->created_at->format('d/m/Y H:i') }})
                        </em>
                    </p>
                </div>
            @endforeach
        @endif

    </div>

    <!-- Rekomendasi -->
    @if ($assessment->commitmentRecommendations)
        <div class="section">
            <div class="section-title">Rekomendasi</div>
            @foreach ($assessment->commitmentRecommendations as $index => $recommendation)
                <div class="recommendation-box">
                    <p><strong>{{ $index + 1 }}.</strong> {{ $recommendation->recommendation }}</p>
                    <p style="margin-top: 8px; font-size: 9pt; color: #6b7280;">
                        <em>Oleh: {{ $recommendation->recommender->name }} -
                            {{ $recommendation->created_at->format('d/m/Y H:i') }}</em>
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- TTD -->
    <div class="signature-section">
        <div style="display:table; width:100%;">

            <div class="signature-cell">
                <p class="role">{{ $institution['officers']['officer1']['position'] ?: 'Pejabat 1' }},</p>
                <div class="signature-line">
                    @if (
                            !empty($institution['officers']['officer1']['signature']) &&
                            file_exists($institution['officers']['officer1']['signature'])
                        )
                        <img src="{{ $institution['officers']['officer1']['signature'] }}" width="240" height="240"
                            alt="tdt" style="position: absolute; top: -32; left: -8;">
                    @else
                        <div style="height: 60px;"></div>
                    @endif
                    <p class="name">{{ $institution['officers']['officer1']['name'] ?: '-' }}</p>
                    <span class="nip">{{ $institution['officers']['officer1']['nip'] ?: '-' }}</span>
                </div>
            </div>

            <div class="signature-cell">
                <p class="role">{{ $institution['officers']['officer2']['position'] ?: 'Pejabat 1' }},,</p>
                <div class="signature-line">
                    @if (
                            !empty($institution['officers']['officer2']['signature']) &&
                            file_exists($institution['officers']['officer2']['signature'])
                        )
                        <img src="{{ $institution['officers']['officer2']['signature'] }}" width="110" height="110"
                            alt="tdt" style="position: absolute; top: 20; left:80">
                    @else
                        <div style="height: 60px;"></div>
                    @endif
                    <p class="name">{{ $institution['officers']['officer2']['name'] ?: '-' }}</p>
                    <span class="nip">{{ $institution['officers']['officer2']['nip'] ?: '-' }}</sp>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
