<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Bulanan - {{ $monthName }}</title>

<style>
@page {
    size: landscape;
    margin-top: 2.2cm;
    margin-bottom: 2cm;
    margin-left: 2.3cm;
    margin-right: 1.8cm;
}

body {
    font-family: "DejaVu Sans", Arial, sans-serif;
    font-size: 9pt;
    color: #000;
    padding-bottom: 1.5cm;
}


body {
    font-family: "DejaVu Sans", Arial, sans-serif;
    font-size: 9pt;
    color: #000;
}

/* ================= HEADER ================= */
.header {
    text-align: center;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.header h1 {
    font-size: 15pt;
    font-weight: bold;
}

.header h2 {
    font-size: 11pt;
    margin-top: 2px;
}

.header p {
    font-size: 9pt;
    margin-top: 4px;
}

/* ================= SUMMARY ================= */
.summary-grid {
    display: table;
    width: 100%;
    margin-bottom: 15px;
}

.summary-row {
    display: table-row;
}

.summary-card {
    display: table-cell;
    width: 25%;
    border: 1px solid #000;
    text-align: center;
    padding: 10px;
}

.summary-label {
    font-size: 8pt;
    font-weight: bold;
    margin-bottom: 4px;
}

.summary-value {
    font-size: 16pt;
    font-weight: bold;
}

/* ================= SECTION TITLE ================= */
.section-title {
    font-weight: bold;
    font-size: 11pt;
    border-bottom: 1px solid #000;
    margin: 15px 0 8px 0;
    padding-bottom: 3px;
}

/* ================= TABLE ================= */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
    font-size: 8.5pt;
}

th, td {
    border: 1px solid #000;
    padding: 6px;
}

th {
    text-align: center;
    font-weight: bold;
}

td {
    vertical-align: middle;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

/* ================= STATUS ================= */
.status-badge {
    border: 1px solid #000;
    padding: 2px 6px;
    font-size: 7.5pt;
    display: inline-block;
}

.score-cell {
    text-align: center;
    font-weight: bold;
}

/* ================= BAR GRAFIK ================= */
.bar-container {
    width: 100%;
    border: 1px solid #000;
    height: 14px;
}

.bar-fill {
    height: 100%;
    background-color: #000;
}

/* ================= FOOTER ================= */
.footer {
    position: fixed;
    bottom: -1.2cm;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 8pt;
    border-top: 1px solid #000;
    padding-top: 4px;
}


.label-cell {
    font-weight: bold;
    width: 40%;
    background: #f2f2f2;
}
</style>
</head>

<body>

<!-- Header -->
<div class="header">
    <h1>LAPORAN PENILAIAN BULANAN</h1>
    <h2>LEMBAGA PEMASYARAKATAN</h2>
    <p>
        @php
            $bulanIndo = [
                1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
            ];
        @endphp
        Periode: {{ $bulanIndo[$month] }} {{ $year }}
    </p>
</div>

<!-- Summary -->
<div class="summary-grid">
<div class="summary-row">
    <div class="summary-card">
        <div class="summary-label">Total Penilaian</div>
        <div class="summary-value">{{ $statistics['total'] ?? 0 }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Diterima</div>
        <div class="summary-value">{{ $statistics['diterima'] ?? 0 }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Pending</div>
        <div class="summary-value">{{ $statistics['pending'] ?? 0 }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Ditolak</div>
        <div class="summary-value">{{ $statistics['ditolak'] ?? 0 }}</div>
    </div>
</div>
</div>

<!-- Statistik -->
<div class="section-title">Statistik Detail</div>

<table>
<tr>
    <td class="label-cell">Rata-rata Skor</td>
    <td>{{ number_format($statistics['avg_score'] ?? 0,2) }}</td>
    <td class="label-cell">Skor Tertinggi</td>
    <td>{{ number_format($statistics['max_score'] ?? 0,2) }}</td>
</tr>
<tr>
    <td class="label-cell">Skor Terendah</td>
    <td>{{ number_format($statistics['min_score'] ?? 0,2) }}</td>
    <td class="label-cell">Jumlah Narapidana</td>
    <td>{{ $statistics['unique_inmates'] ?? 0 }} orang</td>
</tr>
<tr>
    <td class="label-cell">Tingkat Persetujuan</td>
    <td>{{ number_format($statistics['approval_rate'] ?? 0,1) }}%</td>
    <td class="label-cell">Penilai Aktif</td>
    <td>{{ $statistics['active_assessors'] ?? 0 }} orang</td>
</tr>
</table>

<!-- Daftar Penilaian -->
<div class="section-title">Daftar Penilaian</div>

<table>
<thead>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>No Reg</th>
    <th>Nama</th>
    <th>Kasus</th>
    <th>Skor</th>
    <th>Penilai</th>
    <th>Status</th>
    <th>Blok</th>
</tr>
</thead>

<tbody>
@forelse($assessments as $index => $assessment)
<tr>
    <td align="center">{{ $index+1 }}</td>
    <td>{{ $assessment->tanggal_penilaian->format('d/m/Y') }}</td>
    <td>{{ $assessment->inmate->no_registrasi }}</td>
    <td>{{ $assessment->inmate->nama }}</td>
    <td>{{ Str::limit($assessment->inmate->crimeType->nama,30) }}</td>
    <td class="score-cell">{{ number_format($assessment->total_score ?? 0,1) }}</td>
    <td>{{ $assessment->creator->name }}</td>
    <td>
        <span>{{ strtoupper($assessment->status) }}</span>
    </td>
</tr>
@empty
<tr>
<td colspan="9" align="center">Tidak ada data</td>
</tr>
@endforelse
</tbody>
</table>

@if($assessments->isNotEmpty())

<div class="section-title">Breakdown Skor</div>

<table>
<thead>
<tr>
    <th>Kategori</th>
    <th>Jumlah</th>
    <th>Persentase</th>
    <th>Grafik</th>
</tr>
</thead>

<tbody>
<tr>
<td>Skor Tinggi (≥70)</td>
<td align="center">{{ $statistics['score_high'] ?? 0 }}</td>
<td align="center">{{ number_format($statistics['score_high_pct'] ?? 0,1) }}%</td>
<td>
<div class="bar-container">
<div class="bar-fill" style="width:{{ $statistics['score_high_pct'] ?? 0 }}%"></div>
</div>
</td>
</tr>

<tr>
<td>Skor Sedang (40-69)</td>
<td align="center">{{ $statistics['score_medium'] ?? 0 }}</td>
<td align="center">{{ number_format($statistics['score_medium_pct'] ?? 0,1) }}%</td>
<td>
<div class="bar-container">
<div class="bar-fill" style="width:{{ $statistics['score_medium_pct'] ?? 0 }}%"></div>
</div>
</td>
</tr>

<tr>
<td>Skor Rendah (&lt;40)</td>
<td align="center">{{ $statistics['score_low'] ?? 0 }}</td>
<td align="center">{{ number_format($statistics['score_low_pct'] ?? 0,1) }}%</td>
<td>
<div class="bar-container">
<div class="bar-fill" style="width:{{ $statistics['score_low_pct'] ?? 0 }}%"></div>
</div>
</td>
</tr>
</tbody>
</table>

@endif

<div class="footer">
Dicetak pada: {{ now()->format('d F Y H:i') }}
</div>

</body>
</html>
