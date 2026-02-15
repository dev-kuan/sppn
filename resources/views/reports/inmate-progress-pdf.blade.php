<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Progress - {{ $inmate->full_name }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2563eb;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14pt;
            color: #4b5563;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 9pt;
            color: #6b7280;
        }
        .profile-section {
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .profile-grid {
            display: table;
            width: 100%;
        }
        .profile-row {
            display: table-row;
        }
        .profile-label {
            display: table-cell;
            width: 30%;
            padding: 5px 10px;
            font-weight: 600;
            color: #374151;
        }
        .profile-value {
            display: table-cell;
            padding: 5px 10px;
            color: #1f2937;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e40af;
            margin: 20px 0 12px 0;
            padding: 8px 12px;
            background-color: #dbeafe;
            border-left: 4px solid #2563eb;
        }
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-row {
            display: table-row;
        }
        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 2px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .summary-card.success {
            border-color: #10b981;
            background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
        }
        .summary-card.warning {
            border-color: #f59e0b;
            background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        }
        .summary-card.info {
            border-color: #3b82f6;
            background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
        }
        .card-label {
            font-size: 8pt;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .card-value {
            font-size: 22pt;
            font-weight: bold;
            color: #1e40af;
        }
        .card-subtitle {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #1e40af;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
        }
        td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 9pt;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .progress-bar-container {
            width: 100%;
            height: 20px;
            background-color: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #1e40af 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 8pt;
            font-weight: 600;
        }
        .trend-indicator {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8pt;
            font-weight: 600;
        }
        .trend-up {
            background-color: #d1fae5;
            color: #065f46;
        }
        .trend-down {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .trend-stable {
            background-color: #fef3c7;
            color: #92400e;
        }
        .chart-placeholder {
            background-color: #f3f4f6;
            border: 2px dashed #9ca3af;
            padding: 80px 20px;
            text-align: center;
            color: #6b7280;
            margin: 15px 0;
            border-radius: 8px;
        }
        .insight-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .insight-box h4 {
            color: #1e40af;
            font-size: 10pt;
            margin-bottom: 8px;
        }
        .insight-box ul {
            margin-left: 20px;
            line-height: 1.6;
        }
        .recommendation-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .recommendation-box h4 {
            color: #92400e;
            font-size: 11pt;
            margin-bottom: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            padding: 10px;
            border-top: 1px solid #e5e7eb;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PROGRESS NARAPIDANA</h1>
        <h2>LEMBAGA PEMASYARAKATAN</h2>
        <p>Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</p>
    </div>

    <!-- Profile Narapidana -->
    <div class="profile-section">
        <h3 style="color: #1e40af; margin-bottom: 10px; font-size: 12pt;">Data Narapidana</h3>
        <div class="profile-grid">
            <div class="profile-row">
                <div class="profile-label">Nomor Registrasi</div>
                <div class="profile-value">{{ $inmate->registration_number }}</div>
            </div>
            <div class="profile-row">
                <div class="profile-label">Nama Lengkap</div>
                <div class="profile-value">{{ $inmate->full_name }}</div>
            </div>
            <div class="profile-row">
                <div class="profile-label">NIK</div>
                <div class="profile-value">{{ $inmate->nik ?? '-' }}</div>
            </div>
            <div class="profile-row">
                <div class="profile-label">Tempat, Tanggal Lahir</div>
                <div class="profile-value">
                    {{ $inmate->place_of_birth ?? '-' }},
                    {{ $inmate->date_of_birth?->format('d F Y') ?? '-' }}
                    ({{ $inmate->date_of_birth?->age ?? '-' }} tahun)
                </div>
            </div>
            <div class="profile-row">
                <div class="profile-label">Pendidikan</div>
                <div class="profile-value">{{ $inmate->education ?? '-' }}</div>
            </div>
            <div class="profile-row">
                <div class="profile-label">Kasus</div>
                <div class="profile-value">{{ $inmate->case_description }}</div>
            </div>
            <div class="profile-row">
                <div class="profile-label">Tanggal Masuk</div>
                <div class="profile-value">{{ $inmate->entry_date?->format('d F Y') ?? '-' }}</div>
            </div>
            <div class="profile-row">
                <div class="profile-label">Blok/Sel</div>
                <div class="profile-value">{{ $inmate->block_cell ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-row">
            <div class="summary-card info">
                <div class="card-label">Total Penilaian</div>
                <div class="card-value">{{ $assessments->count() }}</div>
                <div class="card-subtitle">penilaian</div>
            </div>
            <div class="summary-card {{ $progressData['trend'] == 'naik' ? 'success' : ($progressData['trend'] == 'turun' ? 'warning' : 'info') }}">
                <div class="card-label">Skor Rata-rata Total</div>
                <div class="card-value">{{ number_format(collect($progressData['total'])->avg(), 1) }}</div>
                <div class="card-subtitle">dari 100</div>
            </div>
            <div class="summary-card {{ $progressData['trend'] == 'naik' ? 'success' : ($progressData['trend'] == 'turun' ? 'warning' : 'info') }}">
                <div class="card-label">Trend</div>
                <div class="card-value" style="font-size: 16pt;">
                    @if($progressData['trend'] == 'naik')
                        ↑ Naik
                    @elseif($progressData['trend'] == 'turun')
                        ↓ Turun
                    @else
                        → Stabil
                    @endif
                </div>
                <div class="card-subtitle">
                    {{ ucfirst($progressData['trend']) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Progress -->
    <div class="section-title">Grafik Perkembangan Skor</div>
    <div class="chart-placeholder">
        <svg width="100%" height="200" style="max-width: 800px; margin: 0 auto;">
            @php
                $maxScore = 100;
                $width = 700;
                $height = 180;
                $padding = 40;
                $chartWidth = $width - (2 * $padding);
                $chartHeight = $height - (2 * $padding);
                $count = count($progressData['total']);
                $step = $count > 1 ? $chartWidth / ($count - 1) : 0;
            @endphp

            <!-- Grid lines -->
            @for($i = 0; $i <= 5; $i++)
                @php $y = $padding + ($chartHeight * $i / 5); @endphp
                <line x1="{{ $padding }}" y1="{{ $y }}" x2="{{ $width - $padding }}" y2="{{ $y }}"
                      stroke="#e5e7eb" stroke-width="1"/>
                <text x="{{ $padding - 10 }}" y="{{ $y + 4 }}" text-anchor="end" font-size="8" fill="#6b7280">
                    {{ 100 - ($i * 20) }}
                </text>
            @endfor

            <!-- Data line -->
            @if($count > 1)
            @php
                $points = [];
                foreach($progressData['total'] as $index => $score) {
                    $x = $padding + ($step * $index);
                    $y = $padding + ($chartHeight * (1 - ($score / $maxScore)));
                    $points[] = "$x,$y";
                }
                $pointsStr = implode(' ', $points);
            @endphp
            <polyline points="{{ $pointsStr }}" fill="none" stroke="#3b82f6" stroke-width="3"/>

            <!-- Data points -->
            @foreach($progressData['total'] as $index => $score)
                @php
                    $x = $padding + ($step * $index);
                    $y = $padding + ($chartHeight * (1 - ($score / $maxScore)));
                @endphp
                <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#1e40af"/>
                <text x="{{ $x }}" y="{{ $height - 10 }}" text-anchor="middle" font-size="7" fill="#6b7280">
                    {{ $progressData['labels'][$index] ?? '' }}
                </text>
            @endforeach
            @endif
        </svg>
    </div>

    <!-- Riwayat Penilaian -->
    <div class="section-title">Riwayat Penilaian Detail</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 12%;">Periode</th>
                <th style="width: 10%;">Skor</th>
                <th style="width: 15%;">Progress Bar</th>
                <th style="width: 10%;">Trend</th>
                <th style="width: 18%;">Penilai</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assessments as $index => $assessment)
            @php
                $prevAssessment = $index > 0 ? $assessments[$index - 1] : null;
                $trend = 'stable';
                $trendValue = 0;
                if ($prevAssessment) {
                    $trendValue = ($assessment->skor_total ?? 0) - ($prevAssessment->skor_total ?? 0);
                    if ($trendValue > 2) $trend = 'up';
                    elseif ($trendValue < -2) $trend = 'down';
                }
            @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $assessment->tanggal_penilaian->format('d F Y') }}</td>
                <td>{{ $assessment->periode ?? '-' }}</td>
                <td style="text-align: center; font-weight: 600;">
                    {{ number_format($assessment->skor_total ?? 0, 1) }}
                </td>
                <td>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: {{ $assessment->skor_total ?? 0 }}%;">
                            {{ number_format($assessment->skor_total ?? 0, 0) }}%
                        </div>
                    </div>
                </td>
                <td style="text-align: center;">
                    @if($index > 0)
                        <span class="trend-indicator trend-{{ $trend }}">
                            @if($trend == 'up')
                                ↑ +{{ number_format($trendValue, 1) }}
                            @elseif($trend == 'down')
                                ↓ {{ number_format($trendValue, 1) }}
                            @else
                                → 0
                            @endif
                        </span>
                    @else
                        <span class="trend-indicator trend-stable">Awal</span>
                    @endif
                </td>
                <td>{{ $assessment->creator->name }}</td>
                <td>
                    @if($assessment->status == 'diterima')
                        <span style="color: #065f46; font-weight: 600;">✓ Diterima</span>
                    @else
                        <span style="color: #92400e;">⚠ {{ ucfirst($assessment->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Analisis dan Insight -->
    <div class="page-break"></div>
    <div class="section-title">Analisis dan Insight</div>

    <div class="insight-box">
        <h4>📊 Ringkasan Statistik</h4>
        <ul>
            @php
                $totalScores = collect($progressData['total']);
                $kepribadiansScores = collect($progressData['kepribadian']);
                $kemandirianScores = collect($progressData['kemandirian']);
                $sikapScores = collect($progressData['sikap']);
                $mentalScores = collect($progressData['mental']);
            @endphp
            <li>Skor total tertinggi: <strong>{{ number_format($totalScores->max(), 1) }}</strong></li>
            <li>Skor total terendah: <strong>{{ number_format($totalScores->min(), 1) }}</strong></li>
            <li>Rata-rata skor total: <strong>{{ number_format($totalScores->avg(), 1) }}</strong></li>
            <li>Rata-rata skor kepribadian: <strong>{{ number_format($kepribadiansScores->avg(), 1) }}</strong></li>
            <li>Rata-rata skor kemandirian: <strong>{{ number_format($kemandirianScores->avg(), 1) }}</strong></li>
            <li>Rata-rata skor sikap: <strong>{{ number_format($sikapScores->avg(), 1) }}</strong></li>
            <li>Rata-rata skor mental: <strong>{{ number_format($mentalScores->avg(), 1) }}</strong></li>
        </ul>
    </div>

    <div class="insight-box">
        <h4>📈 Tren Perkembangan</h4>
        <ul>
            @if($progressData['trend'] == 'naik')
                <li style="color: #065f46;">✓ Menunjukkan tren perkembangan positif dengan kenaikan skor total dari
                    <strong>{{ number_format($totalScores->first(), 1) }}</strong> menjadi
                    <strong>{{ number_format($totalScores->last(), 1) }}</strong></li>
            @elseif($progressData['trend'] == 'turun')
                <li style="color: #991b1b;">⚠ Menunjukkan tren penurunan skor total dari
                    <strong>{{ number_format($totalScores->first(), 1) }}</strong> menjadi
                    <strong>{{ number_format($totalScores->last(), 1) }}</strong></li>
            @else
                <li style="color: #92400e;">→ Skor relatif stabil tanpa perubahan signifikan</li>
            @endif
{{--
            <li>Perubahan total: <strong>{{ number_format($totalScores->last() - $totalScores->first(), 1) }}</strong> poin</li>

            @php
                $improvement = (($totalScores->last() - $totalScores->first()) / $totalScores->first()) * 100;
            @endphp
            @if($improvement > 10)
                <li style="color: #065f46;">✓ Peningkatan performa sebesar
                    <strong>{{ number_format($improvement, 1) }}%</strong> dari penilaian awal</li>
            @elseif($improvement < -10)
                <li style="color: #991b1b;">⚠ Penurunan performa sebesar
                    <strong>{{ number_format(abs($improvement), 1) }}%</strong> dari penilaian awal</li>
            @endif --}}
        </ul>
    </div>

    <!-- Rekomendasi -->
    <div class="recommendation-box">
        <h4>💡 Rekomendasi Tindak Lanjut</h4>
        <ul style="margin-left: 20px; line-height: 1.8;">
            @php
                $avgTotal = collect($progressData['total'])->avg();
            @endphp
            @if($avgTotal >= 70)
                <li>Narapidana menunjukkan perkembangan yang baik. Pertahankan program pembinaan yang sudah berjalan.</li>
                <li>Dapat dipertimbangkan untuk program asimilasi atau pembebasan bersyarat.</li>
            @elseif($avgTotal >= 40)
                <li>Perlu peningkatan pada beberapa aspek pembinaan.</li>
                <li>Identifikasi area yang perlu perbaikan dan berikan program pendampingan tambahan.</li>
            @else
                <li>Perlu perhatian khusus dan evaluasi mendalam terhadap program pembinaan.</li>
                <li>Disarankan melakukan konseling intensif dan penyesuaian metode pembinaan.</li>
            @endif

            @if($progressData['trend'] == 'naik')
                <li>Tren positif menunjukkan efektivitas program. Lanjutkan dengan pendekatan yang sama.</li>
            @elseif($progressData['trend'] == 'turun')
                <li>Tren negatif memerlukan evaluasi ulang strategi pembinaan yang diterapkan.</li>
            @endif

            <li>Lakukan penilaian berkala untuk memantau perkembangan berkelanjutan.</li>
            <li>Dokumentasikan best practices untuk diterapkan pada narapidana lain.</li>
        </ul>
    </div>

    <!-- Catatan Penutup -->
    <div style="margin-top: 30px; padding: 15px; background-color: #f9fafb; border-radius: 6px;">
        <p style="font-size: 9pt; color: #6b7280; line-height: 1.6;">
            <strong>Catatan:</strong> Laporan ini dibuat berdasarkan data penilaian yang telah disetujui
            pada periode {{ $startDate->format('d F Y') }} hingga {{ $endDate->format('d F Y') }}.
            Data yang ditampilkan merupakan hasil agregasi dari {{ $assessments->count() }} kali penilaian.
            Untuk informasi lebih detail, dapat merujuk pada laporan penilaian individual.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dicetak pada: {{ now()->format('d F Y H:i') }} | Laporan Progress Narapidana
    </div>
</body>
</html>
