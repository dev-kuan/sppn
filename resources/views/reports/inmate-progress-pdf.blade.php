<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Progress - {{ $inmate->full_name }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: black;
        }

        .doc-header th {
            padding: 5px;
            vertical-align: middle;
            border: 1px solid black;
        }
         .logo {
            border: none;
            padding: none;
        }
                .doc-header {
            width: 100%;
        }

        .section-title {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
        }

        /* ---- KOP SURAT ---- */
        .kop-table  { display: table; width: 100%; }
        .kop-row    { display: table-row; }
        .kop-logo   { display: table-cell; width: 75px; vertical-align: middle; text-align: center; }
        .kop-logo-circle {
            width: 60px; height: 60px;
            border: 2px solid #111111; border-radius: 50%;
            display: inline-block; line-height: 56px;
            text-align: center; font-size: 7pt; font-weight: bold;
        }
        .kop-text  { display: table-cell; vertical-align: middle; text-align: center; }
        .kop-space { display: table-cell; width: 75px; }
        .kop-induk { font-size: 9pt; text-transform: uppercase; letter-spacing: 0.3px; }
        .kop-nama  { font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 3px 0; }
        .kop-alamat{ font-size: 7.5pt; color: #444444; }
        .kop-border{ border-bottom: 3px double #111111; padding-bottom: 10px; margin-bottom: 14px; }

        /* ---- JUDUL ---- */
        .judul { text-align: center; margin: 14px 0 12px; }
        .judul h1 { font-size: 11.5pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }
        .judul p  { font-size: 9pt; margin-top: 4px; color: #333; }

        /* ---- SECTION BOX ---- */
        .box        { border: 1px solid #aaaaaa; margin-bottom: 11px; }
        .box-head   { background: #e8e8e8; border-bottom: 1px solid #aaaaaa; padding: 5px 10px;
                      font-size: 9pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }
        .box-body   { padding: 8px 12px; }

        /* ---- IDENTITAS ---- */
        .id-tbl  { display: table; width: 100%; }
        .id-row  { display: table-row; }
        .id-lbl  { display: table-cell; width: 34%; padding: 2.5px 5px; font-size: 9pt; font-weight: bold; vertical-align: top; }
        .id-sep  { display: table-cell; width: 3%; padding: 2.5px 2px; font-size: 9pt; }
        .id-val  { display: table-cell; padding: 2.5px 5px; font-size: 9pt; }

        /* ---- STATISTIK ---- */
        .stat-tbl  { display: table; width: 100%; border-collapse: collapse; }
        .stat-row  { display: table-row; }
        .stat-cell { display: table-cell; width: 33.33%; border: 1px solid #aaaaaa;
                     text-align: center; padding: 10px 6px; vertical-align: middle; background: #f7f7f7; }
        .stat-cell + .stat-cell { border-left: none; }
        .stat-cell.alt { background: #efefef; }
        .stat-lbl  { font-size: 7.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 5px; color: #444; }
        .stat-val  { font-size: 20pt; font-weight: bold; }
        .stat-sub  { font-size: 7.5pt; margin-top: 3px; color: #555; }

        /* ---- SECTION DIVIDER ---- */
        .sec-title {
            font-size: 9.5pt; font-weight: bold; text-transform: uppercase;
            padding: 5px 10px; margin: 13px 0 8px;
            background: #d8d8d8;
            border-left: 4px solid #111111;
            border-top: 1px solid #aaaaaa;
            border-bottom: 1px solid #aaaaaa;
        }
        /* ---- TABEL DATA ---- */
        table.dt { width: 100%; border-collapse: collapse; margin-bottom: 11px; }
        table.dt th {
            background: #d0d0d0; color: #111; padding: 6px 5px;
            text-align: center; font-size: 8pt; border: 1px solid #888;
        }
        table.dt td { padding: 5px 6px; border: 1px solid #bbbbbb; font-size: 8.5pt; vertical-align: middle; }
        table.dt tr:nth-child(even) td { background: #f4f4f4; }

        /* ---- PROGRESS BAR ---- */
        .pb-wrap { width: 100%; height: 13px; background: #dddddd; border: 1px solid #aaaaaa; overflow: hidden; }
        .pb-fill  { height: 100%; background: #555555; text-align: center; line-height: 13px;
                    color: #fff; font-size: 7pt; font-weight: bold; }

        /* ---- TREND BADGE ---- */
        .badge { display: inline-block; padding: 2px 6px; border: 1px solid #999; font-size: 7.5pt;
                 font-weight: bold; background: #f0f0f0; }

        /* ---- ANALISIS ---- */
        .an-box   { border: 1px solid #aaaaaa; margin-bottom: 8px; }
        .an-head  { background: #e0e0e0; border-bottom: 1px solid #aaaaaa; padding: 4px 10px;
                    font-size: 8.5pt; font-weight: bold; }
        .an-body  { padding: 7px 12px; font-size: 9pt; }
        .an-body ul { margin-left: 16px; line-height: 1.8; }

        /* ---- REKOMENDASI ---- */
        .rek-box  { border: 1.5px solid #888; margin-bottom: 13px; background: #f9f9f9; }
        .rek-head { background: #cccccc; border-bottom: 1px solid #888; padding: 5px 10px;
                    font-size: 9.5pt; font-weight: bold; text-transform: uppercase; }
        .rek-body { padding: 9px 13px; font-size: 9pt; }
        .rek-body ul { margin-left: 16px; line-height: 1.9; }

        /* ---- CATATAN ---- */
        .catatan { margin-top: 16px; border-top: 1px solid #bbbbbb; padding-top: 7px;
                   font-size: 8pt; color: #555; line-height: 1.5; }

        /* ---- TANDA TANGAN ---- */
        .ttd-tbl  { display: table; width: 100%; margin-top: 32px; }
        .ttd-row  { display: table-row; }
        .ttd-cell { display: table-cell; width: 50%; text-align: center; padding: 0 15px; }
        .ttd-pos  { font-size: 9pt; margin-bottom: 50px; }
        .ttd-nama { font-size: 9pt; font-weight: bold; border-top: 1px solid #111; padding-top: 4px; }
        .ttd-nip  { font-size: 8.5pt; }

        /* ---- FOOTER ---- */
        .footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            border-top: 1px solid #aaaaaa; padding: 4px 20px;
            background: #ffffff; display: table; width: 100%;
        }
        .f-l { display: table-cell; text-align: left;   font-size: 7.5pt; color: #555; }
        .f-c { display: table-cell; text-align: center;  font-size: 7.5pt; color: #555; }
        .f-r { display: table-cell; text-align: right;   font-size: 7.5pt; color: #555; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

<!-- ===================== KOP SURAT ===================== -->
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

<!-- ===================== JUDUL ===================== -->
<div class="judul">
    <h1>Laporan Progress Pembinaan Narapidana</h1>
    <p>Periode: {{ $startDate->format('d F Y') }} s.d. {{ $endDate->format('d F Y') }}</p>
</div>

<!-- ===================== I. IDENTITAS ===================== -->
<div class="box">
    <div class="box-head">I. Identitas Narapidana</div>
    <div class="box-body">
        <div class="id-tbl">
            <div class="id-row">
                <div class="id-lbl">Nomor Registrasi</div>
                <div class="id-sep">:</div>
                <div class="id-val">{{ $inmate->no_registrasi }}</div>
            </div>
            <div class="id-row">
                <div class="id-lbl">Nama Lengkap</div>
                <div class="id-sep">:</div>
                <div class="id-val">{{ $inmate->nama }}</div>
            </div>
            <div class="id-row">
                <div class="id-lbl">Tempat, Tanggal Lahir</div>
                <div class="id-sep">:</div>
                <div class="id-val">
                    {{ $inmate->tempat_lahir ?? '-' }},
                    {{ $inmate->tanggal_lahir?->format('d F Y') ?? '-' }}
                    ({{ $inmate->tanggal_lahir?->age ?? '-' }} tahun)
                </div>
            </div>
            <div class="id-row">
                <div class="id-lbl">Tingkat Pendidikan</div>
                <div class="id-sep">:</div>
                <div class="id-val">{{ $inmate->tingkat_pendidikan ?? '-' }}</div>
            </div>
            <div class="id-row">
                <div class="id-lbl">Kasus / Tindak Pidana</div>
                <div class="id-sep">:</div>
                <div class="id-val">{{ $inmate->crimeType->nama }}</div>
            </div>
            <div class="id-row">
                <div class="id-lbl">Tanggal Masuk</div>
                <div class="id-sep">:</div>
                <div class="id-val">{{ $inmate->tanggal_masuk?->format('d F Y') ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- ===================== II. STATISTIK ===================== -->
<div class="box">
    <div class="box-head">II. Ringkasan Statistik Penilaian</div>
    <div class="box-body" style="padding:0;">
        <div class="stat-tbl">
            <div class="stat-row">
                <div class="stat-cell">
                    <div class="stat-lbl">Total Penilaian</div>
                    <div class="stat-val">{{ $assessments->count() }}</div>
                    <div class="stat-sub">kali penilaian</div>
                </div>
                <div class="stat-cell alt">
                    <div class="stat-lbl">Rata-rata Skor Total</div>
                    <div class="stat-val">{{ number_format(collect($progressData['total'])->avg(), 1) }}</div>
                    <div class="stat-sub">dari 100</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-lbl">Tren Perkembangan</div>
                    <div class="stat-val" style="font-size:14pt; margin-top:3px;">
                        @if($progressData['trend'] == 'naik')     &#8679; NAIK
                        @elseif($progressData['trend'] == 'turun') &#8681; TURUN
                        @else                                       &#8680; STABIL
                        @endif
                    </div>
                    <div class="stat-sub">{{ ucfirst($progressData['trend']) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ===================== IV. RIWAYAT PENILAIAN ===================== -->
<div class="sec-title">IV. Riwayat Penilaian Detail</div>
<table class="dt">
    <thead>
        <tr>
            <th style="width:4%;">No.</th>
            <th style="width:14%;">Tanggal</th>
            <th style="width:11%;">Periode</th>
            <th style="width:8%;">Skor </th>
            <th style="width:19%;">Progress</th>
            <th style="width:9%;">Tren</th>
            <th style="width:18%;">Penilai</th>
            <th style="width:17%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assessments as $index => $assessment)
        @php
            $prev       = $index > 0 ? $assessments[$index - 1] : null;
            $trend      = 'stable';
            $trendVal   = 0;
            if ($prev) {
                $trendVal = ($assessment->skor_total ?? 0) - ($prev->skor_total ?? 0);
                if ($trendVal > 2)      $trend = 'up';
                elseif ($trendVal < -2) $trend = 'down';
            }
        @endphp
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td>{{ $assessment->tanggal_penilaian->format('d/m/Y') }}</td>
            <td style="text-align:center;">{{ $assessment->tanggal_penilaian->translatedFormat('M Y') ?? '-' }}</td>
            <td style="text-align:center; font-weight:bold;">
                {{ number_format($assessment->skor_total ?? 0, 1) }}
            </td>
            <td style="text-align:center;">
                         Kepribadian: {{ number_format($assessment->skor_kepribadian ?? 0, 1) }} |
                            Kemandirian: {{ number_format($assessment->skor_kemandirian ?? 0, 1) }} |
                            Mental: {{ number_format($assessment->skor_mental ?? 0, 1) }} |
                            Sikap: {{ number_format($assessment->skor_sikap ?? 0, 1) }} |
                            Komitmen: {{ number_format($assessment->skor_komitmen ?? 0, 1) }} |
            </td>
            <td style="text-align:center;">
                @if($index > 0)
                    <span class="badge">
                        @if($trend == 'up')       &#8679; +{{ number_format($trendVal, 1) }}
                        @elseif($trend == 'down') &#8681; {{ number_format($trendVal, 1) }}
                        @else                     &#8680; 0
                        @endif
                    </span>
                @else
                    <span class="badge">Awal</span>
                @endif
            </td>
            <td>{{ $assessment->creator->name }}</td>
            <td style="text-align:center;">
                @if($assessment->status == 'diterima')
                    <strong>&#10003; Diterima</strong>
                @else
                    &#9888; {{ ucfirst($assessment->status) }}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- ===================== PAGE BREAK ===================== -->
<div class="page-break"></div>

<!-- Header ringkas halaman 2 -->
<div style="border-bottom:2px solid #888; padding-bottom:5px; margin-bottom:12px;
            font-size:8.5pt; text-align:center; color:#333;">
    <strong>LAPORAN PROGRESS PEMBINAAN NARAPIDANA</strong>
    &nbsp;|&nbsp; {{ $inmate->nama }} ({{ $inmate->no_registrasi }})
    &nbsp;|&nbsp; {{ $startDate->format('d/m/Y') }} &ndash; {{ $endDate->format('d/m/Y') }}
</div>

<!-- ===================== V. ANALISIS ===================== -->
<div class="sec-title">V. Analisis dan Insight</div>

@php
    $totalScores   = collect($progressData['total']);
    $kepScores     = collect($progressData['kepribadian']);
    $manScores     = collect($progressData['kemandirian']);
    $sikScores     = collect($progressData['sikap']);
    $menScores     = collect($progressData['mental']);
@endphp

<div class="an-box">
    <div class="an-head">A. Ringkasan Statistik Per Aspek</div>
    <div class="an-body">
        <ul>
            <li>Skor total tertinggi          : <strong>{{ number_format($totalScores->max(), 1) }}</strong></li>
            <li>Skor total terendah           : <strong>{{ number_format($totalScores->min(), 1) }}</strong></li>
            <li>Rata-rata skor total          : <strong>{{ number_format($totalScores->avg(), 1) }}</strong></li>
            <li>Rata-rata variabel kepribadian   : <strong>{{ number_format($kepScores->avg(), 1) }}</strong></li>
            <li>Rata-rata variabel kemandirian   : <strong>{{ number_format($manScores->avg(), 1) }}</strong></li>
            <li>Rata-rata variabel sikap         : <strong>{{ number_format($sikScores->avg(), 1) }}</strong></li>
            <li>Rata-rata variabel mental        : <strong>{{ number_format($menScores->avg(), 1) }}</strong></li>
        </ul>
    </div>
</div>

<div class="an-box">
    <div class="an-head">B. Analisis Tren Perkembangan</div>
    <div class="an-body">
        <ul>
            @if($progressData['trend'] == 'naik')
                <li>Narapidana menunjukkan tren perkembangan <strong>positif</strong> dengan kenaikan skor dari
                    <strong>{{ number_format($totalScores->first(), 1) }}</strong> menjadi
                    <strong>{{ number_format($totalScores->last(), 1) }}</strong> poin.</li>
            @elseif($progressData['trend'] == 'turun')
                <li>Narapidana menunjukkan tren <strong>penurunan</strong> skor total dari
                    <strong>{{ number_format($totalScores->first(), 1) }}</strong> menjadi
                    <strong>{{ number_format($totalScores->last(), 1) }}</strong> poin.</li>
            @else
                <li>Skor narapidana relatif <strong>stabil</strong> tanpa perubahan signifikan selama periode evaluasi.</li>
            @endif
            <li>Perubahan skor total                   : <strong>{{ number_format($totalScores->last() - $totalScores->first(), 1) }}</strong> poin.</li>
            <li>Jumlah penilaian yang telah dilakukan  : <strong>{{ $assessments->count() }}</strong> kali.</li>
        </ul>
    </div>
</div>

<!-- ===================== VI. REKOMENDASI ===================== -->
<div class="sec-title">VI. Rekomendasi Tindak Lanjut</div>
<div class="rek-box">
    <div class="rek-head">Rekomendasi Pembinaan</div>
    <div class="rek-body">
        @php $avgTotal = collect($progressData['total'])->avg(); @endphp
        <ul>
            @if($avgTotal >= 70)
                <li>Narapidana menunjukkan perkembangan yang <strong>baik</strong>. Pertahankan program pembinaan yang sedang berjalan.</li>
                <li>Dapat dipertimbangkan untuk mengikuti program asimilasi atau pembebasan bersyarat sesuai ketentuan yang berlaku.</li>
            @elseif($avgTotal >= 40)
                <li>Perlu peningkatan pada beberapa aspek pembinaan yang masih di bawah standar.</li>
                <li>Identifikasi area yang memerlukan perbaikan dan berikan program pendampingan tambahan.</li>
            @else
                <li>Narapidana memerlukan <strong>perhatian khusus</strong> dan evaluasi mendalam terhadap program pembinaan.</li>
                <li>Disarankan melakukan konseling intensif serta penyesuaian metode dan pendekatan pembinaan.</li>
            @endif
            @if($progressData['trend'] == 'naik')
                <li>Tren positif mengindikasikan efektivitas program. Lanjutkan dengan pendekatan yang sama.</li>
            @elseif($progressData['trend'] == 'turun')
                <li>Tren negatif memerlukan evaluasi ulang terhadap strategi pembinaan yang diterapkan saat ini.</li>
            @endif
            <li>Lakukan penilaian berkala secara konsisten untuk memantau perkembangan berkelanjutan.</li>
            <li>Dokumentasikan praktik terbaik (best practices) untuk dapat diterapkan kepada narapidana lainnya.</li>
        </ul>
    </div>
</div>

<!-- ===================== CATATAN ===================== -->
<div class="catatan">
    <strong>Catatan:</strong> Laporan ini disusun berdasarkan data penilaian yang telah disetujui pada periode
    {{ $startDate->format('d F Y') }} hingga {{ $endDate->format('d F Y') }}.
    Data merupakan hasil agregasi dari <strong>{{ $assessments->count() }}</strong> kali penilaian.
    Untuk informasi lebih rinci dapat merujuk pada laporan penilaian individual masing-masing periode.
</div>

<!-- ===================== TANDA TANGAN ===================== -->
<div class="ttd-tbl">
    <div class="ttd-row">
        <div class="ttd-cell">
            <div class="ttd-pos">Mengetahui,<br>Kepala Lembaga Pemasyarakatan</div>
            <div class="ttd-nama">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
            <div class="ttd-nip">NIP. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
        </div>
        <div class="ttd-cell">
            <div class="ttd-pos">[Kota], {{ now()->format('d F Y') }}<br>Petugas Pembinaan</div>
            <div class="ttd-nama">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
            <div class="ttd-nip">NIP. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
        </div>
    </div>
</div>

<!-- ===================== FOOTER ===================== -->
<div class="footer">
    <div class="f-l">Laporan Progress Narapidana &mdash; Dokumen Resmi</div>
    <div class="f-c">{{ $inmate->nama }} ({{ $inmate->no_registrasi }})</div>
    <div class="f-r">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</div>

</body>
</html>
