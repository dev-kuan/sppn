<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penilaian - {{ $assessment->inmate->full_name }}</title>
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
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
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
            font-size: 10pt;
            color: #6b7280;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding: 8px 12px;
            background-color: #dbeafe;
            border-left: 4px solid #2563eb;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 6px 10px;
            font-weight: 600;
            color: #374151;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
        }
        .info-value {
            display: table-cell;
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #1e40af;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10pt;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            font-size: 10pt;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .score-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10pt;
        }
        .score-high {
            background-color: #d1fae5;
            color: #065f46;
        }
        .score-medium {
            background-color: #fef3c7;
            color: #92400e;
        }
        .score-low {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 9pt;
        }
        .status-diterima {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-ditolak {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .observation-table {
            margin-bottom: 20px;
        }
        .observation-table td {
            vertical-align: top;
        }
        .commitment-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }
        .recommendation-box {
            background-color: #dbeafe;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-grid {
            display: table;
            width: 100%;
        }
        .signature-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: 600;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
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
        <h1>LAPORAN PENILAIAN NARAPIDANA</h1>
        <h2>LEMBAGA PEMASYARAKATAN</h2>
        <p>Nomor: {{ $assessment->nomor_penilaian ?? '-' }}</p>
    </div>

    <!-- Data Narapidana -->
    <div class="section">
        <div class="section-title">Data Narapidana</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nomor Registrasi</div>
                <div class="info-value">{{ $assessment->inmate->no_registrasi }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama Lengkap</div>
                <div class="info-value">{{ $assessment->inmate->nama }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Agama</div>
                <div class="info-value">{{ $assessment->inmate->agama }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tempat, Tanggal Lahir</div>
                <div class="info-value">
                    {{ $assessment->inmate->tempat_lahir ?? '-' }},
                    {{ $assessment->inmate->tanggal_lahir?->format('d F Y') ?? '-' }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Kelamin</div>
                <div class="info-value">{{ $assessment->inmate->jenis_kelamin }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Pendidikan</div>
                <div class="info-value">{{ $assessment->inmate->tingkat_pendidikan ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kasus</div>
                <div class="info-value">{{ $assessment->inmate->crimeType->nama }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Sisa Pidana</div>
                <div class="info-value">{{ $assessment->inmate->sisa_pidana_bulan ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Data Penilaian -->
    <div class="section">
        <div class="section-title">Data Penilaian</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Tanggal Penilaian</div>
                <div class="info-value">{{ $assessment->tanggal_penilaian->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Periode</div>
                <div class="info-value">{{ $assessment->periode ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Penilai</div>
                <div class="info-value">{{ $assessment->creator->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    @if($assessment->status == 'diterima')
                        <span class="status-badge status-diterima">DITERIMA</span>
                    @elseif($assessment->status == 'ditolak')
                        <span class="status-badge status-ditolak">DITOLAK</span>
                    @else
                        <span class="status-badge status-pending">PENDING</span>
                    @endif
                </div>
            </div>
            @if($assessment->approver)
            <div class="info-row">
                <div class="info-label">Disetujui Oleh</div>
                <div class="info-value">{{ $assessment->approver->name }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Hasil Observasi -->
    <div class="section page-break">
        <div class="section-title">Hasil Observasi</div>

        @foreach($variabels as $variabel)
        <div style="margin-bottom: 25px;">
            <h3 style="font-size: 12pt; color: #1e40af; margin-bottom: 10px;">
                {{ $variabel->name }}
            </h3>

            @foreach($variabel->aspect as $aspect)
            <table class="observation-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 40%;">Item Observasi</th>
                        <th style="width: 12%;">Frekuensi</th>
                        <th style="width: 12%;">Tercatat</th>
                        <th style="width: 15%;">Persentase</th>
                        <th style="width: 16%;">Hari dalam Bulan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background-color: #f3f4f6;">
                        <td colspan="6" style="font-weight: 600; color: #374151;">
                            {{ $aspect->name }}
                        </td>
                    </tr>
                    @foreach($aspect->observationItems as $index => $item)
                    @php
                        $itemData = $observationData[$item->id] ?? [
                            'checked_count' => 0,
                            'frekuensi' => 1,
                            'percentage' => 0
                        ];
                        $percentage = $itemData['percentage'];
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $item->nama_item }}</td>
                        <td style="text-align: center;">{{ $itemData['frekuensi'] }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $itemData['checked_count'] }}</td>
                        <td style="text-align: center;">
                            <span class="score-badge {{ $percentage >= 70 ? 'score-high' : ($percentage >= 40 ? 'score-medium' : 'score-low') }}">
                                {{ number_format($percentage, 1) }}%
                            </span>
                        </td>
                        <td style="text-align: center; font-size: 8pt; color: #6b7280;">
                            {{ $assessment->tanggal_penilaian->daysInMonth }} hari
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endforeach
        </div>
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

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-grid">
            <div class="signature-cell">
                <p>Penilai,</p>
                <div class="signature-line">{{ $assessment->creator->name }}</div>
                <p style="font-size: 9pt; color: #6b7280;">NIP: {{ $assessment->creator->nip ?? '-' }}</p>
            </div>
            @if($assessment->approver)
            <div class="signature-cell">
                <p>Menyetujui,</p>
                <div class="signature-line">{{ $assessment->approver->name }}</div>
                <p style="font-size: 9pt; color: #6b7280;">NIP: {{ $assessment->approver->nip ?? '-' }}</p>
            </div>
            @endif
            <div class="signature-cell">
                <p>Narapidana,</p>
                <div class="signature-line">{{ $assessment->inmate->full_name }}</div>
                <p style="font-size: 9pt; color: #6b7280;">Reg: {{ $assessment->inmate->registration_number }}</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dicetak pada: {{ now()->format('d F Y H:i') }} | Dokumen ini digenerate otomatis oleh sistem
    </div>
</body>
</html>
