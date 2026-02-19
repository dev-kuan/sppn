<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Penilaian - {{ $assessment->inmate->full_name }}</title>

<style>
@page { margin: 2cm 1.5cm; }

body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 11pt;
    color: black;
}

/* HEADER */
.header {
    text-align:center;
    margin-bottom:30px;
    border-bottom:2px solid black;
    padding-bottom:15px;
}

/* SECTION */
.section { margin-bottom:20px; }

.section-title {
    font-size:13pt;
    font-weight:bold;
    padding:6px;
    margin-bottom:10px;
}

.inmate-data{
    width: 100%;
    border-collapse: collapse;
}

.inmate-data td, th{
    padding: 5px;
    vertical-align: middle;
}

.header{
    text-align: center;
    font-weight: bold;
    font-size: 14px;
}

.sub-header{
    text-align: center;
    font-weight: bold;
    font-size: 13px;
}

.section-title{
    text-align: center;
    font-weight: bold;
    font-size: 13px;
}

.label{
    font-weight: bold;
    width: 18%;
}

.colon{
    width: 2%;
    text-align: center;
}

.value{
    width: 30%;
}

.right-label{
    font-weight: bold;
    width: 22%;
}

.right-value{
    width: 28%;
}


/* INFO GRID */
.info-grid { display:table; width:100%; }
.info-row { display:table-row; }

.info-label {
    display:table-cell;
    width:35%;
    padding:6px;
    border:1px solid black;
    font-weight:bold;
    background:#f2f2f2;
}

.info-value {
    display:table-cell;
    padding:6px;
    border:1px solid black;
}

/* TABLE */
.observation { width:100%; border: 1px solid black; border-collapse:collapse; }

.observation th {
    border:1px solid black;
    padding:8px;
    font-weight:bold;
    text-align:left;
    background:#e6e6e6;
}

.observation td {
    border:1px solid black;
    padding:6px;
}


/* BOX */
.commitment-box,
.recommendation-box {
    border:1px solid black;
    padding:10px;
    margin-bottom:10px;
}

/* SIGNATURE */
.signature-section { margin-top:40px; }

.signature-cell {
    width:33%;
    display:table-cell;
    text-align:center;
}

.signature-line {
    margin-top:60px;
    border-top:1px solid black;
}
</style>
</head>

<body>

<table class="inmate-data">

<!-- HEADER INSTANSI -->
<tr>
<td colspan="6" class="header">
DIREKTORAT JENDERAL PEMASYARAKATAN<br>
KEMENTERIAN HUKUM DAN HAK ASASI MANUSIA<br>
REPUBLIK INDONESIA
</td>
</tr>

<tr>
<td colspan="6" class="sub-header">
LEMBAR PENILAIAN PEMBINAAN NARAPIDANA<br>
LAPAS MEDIUM SECURITY
</td>
</tr>

<tr>
<td colspan="6" class="section-title">
DATA DEMOGRAFI NARAPIDANA
</td>
</tr>

<!-- DATA -->
<tr>
<td class="label" colspan="3">Nama Narapidana</td>
<td class="colon">:</td>
<td colspan="5" class="value">{{ $assessment->inmate->nama }}</td>
</tr>

<tr>
<td class="label" colspan="3">Nama Lembaga Pemasyarakatan</td>
<td class="colon">:</td>
<td colspan="5" class="value">LP KELAS III LEMBATA</td>
</tr>

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
                    {{ $assessment->inmate->tanggal_lahir?->translatedFormat('d F Y') ?? '-' }}</td>

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
<td rowspan="1"class="right-value">1x</td>
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

<td class="right-label"></td>
<td class="colon"></td>
<td class="right-value"></td>
</tr>

<!-- FOOTER DATA -->
<tr>
<td colspan="2"><b>Tanggal Awal Pengisian</b></td>
<td>{{ $assessment->tanggal_penilaian->format('d') }}</td>

<td colspan="2"><b>Bulan Pengisian</b></td>
<td>{{ $assessment->tanggal_penilaian->translatedFormat('F Y') }} ({{ $assessment->tanggal_penilaian->daysInMonth }} hari)</td>
</tr>

</table>
<!-- OBSERVASI -->
<div class="section">
    <div class="section-title">Hasil Observasi</div>

    @foreach($observationData as $varIndex => $variabel)

        @if($varIndex > 0)
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
                @foreach($variabel['aspects'] as $aspekIndex => $aspek)

                    {{-- Baris Nama Aspek (colspan penuh) --}}
                    <tr style="background: #e6e6e6;">
                        <td colspan="6" style="font-weight: bold; padding: 6px 8px;">
                            {{ $aspekIndex + 1 }}. {{ $aspek['nama'] }}
                        </td>
                    </tr>

                    {{-- Baris item-item --}}
                    @foreach($aspek['items'] as $itemIndex => $item)
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

<!-- Pernyataan Komitmen -->
    @if($assessment->commitmentStatements->isNotEmpty())
    <div class="section">
        <div class="section-title">Pernyataan Komitmen</div>
        @foreach($assessment->commitmentStatements as $index => $statement)
        <div class="commitment-box">
            <strong>{{ $index + 1 }}.</strong> {{ $statement->statement }}
        </div>
        @endforeach
    </div>
    @endif

    <!-- Rekomendasi -->
    @if($assessment->commitmentRecommendations->isNotEmpty())
    <div class="section">
        <div class="section-title">Rekomendasi</div>
        @foreach($assessment->commitmentRecommendations as $index => $recommendation)
        <div class="recommendation-box">
            <p><strong>{{ $index + 1 }}.</strong> {{ $recommendation->recommendation }}</p>
            <p style="margin-top: 8px; font-size: 9pt; color: #6b7280;">
                <em>Oleh: {{ $recommendation->recommender->name }} - {{ $recommendation->created_at->format('d/m/Y H:i') }}</em>
            </p>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Catatan -->
    @if($assessment->catatan)
    <div class="section">
        <div class="section-title">Catatan</div>
        <div style="padding: 12px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px;">
            {{ $assessment->catatan }}
        </div>
    </div>
    @endif

<!-- CATATAN -->
<div class="section">
<div class="section-title">Catatan</div>
Perkembangan perilaku cukup baik dan menunjukkan peningkatan disiplin.
</div>

<!-- TTD -->
<div class="signature-section">
<div style="display:table; width:100%;">

<div class="signature-cell">
<p>Penilai</p>
<div class="signature-line">Budi Santoso</div>
</div>

<div class="signature-cell">
<p>Menyetujui</p>
<div class="signature-line">Ahmad Wijaya</div>
</div>

<div class="signature-cell">
<p>Narapidana</p>
<div class="signature-line">Andi Saputra</div>
</div>

</div>
</div>

</body>
</html>
