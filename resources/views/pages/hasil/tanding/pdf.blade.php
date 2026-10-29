<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Hasil Tanding</title>
    <style>
        @page {
            margin: 1cm;
            size: a4 landscape;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 8.5pt;
            color: #222;
            line-height: 1.2;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 8px;
        }

        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 14pt;
            color: #0f172a;
            letter-spacing: 1px;
        }

        .header h2 {
            margin: 3px 0 0 0;
            font-size: 10pt;
            font-weight: normal;
            color: #475569;
        }

        .header .meta {
            margin-top: 5px;
            font-size: 8pt;
            color: #64748b;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.main-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 4px;
            font-size: 8pt;
            text-transform: uppercase;
            color: #334155;
            font-weight: bold;
        }

        table.main-table td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: middle;
            font-size: 8pt;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .badge-partai {
            display: inline-block;
            font-weight: bold;
            font-size: 9pt;
            color: #0f172a;
        }

        .box-sudut {
            padding: 4px 6px;
            border-radius: 4px;
        }

        .sudut-biru {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .sudut-merah {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }

        .nama-atlet {
            font-weight: bold;
            font-size: 8.5pt;
        }

        .nama-biru {
            color: #1d4ed8;
        }

        .nama-merah {
            color: #b91c1c;
        }

        .kontingen {
            font-size: 7.5pt;
            color: #64748b;
        }

        .skor-box {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 4px 2px;
        }

        .badge-pemenang {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            color: #ffffff;
            font-weight: bold;
            font-size: 7.5pt;
            text-transform: uppercase;
        }

        .bg-pemenang-biru {
            background-color: #2563eb;
        }

        .bg-pemenang-merah {
            background-color: #dc2626;
        }

        .alasan {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 2px;
        }

        .badge-status {
            font-size: 7.5pt;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 3px;
        }

        .status-selesai {
            background-color: #dcfce7;
            color: #15803d;
        }

        .status-berlangsung {
            background-color: #fef3c7;
            color: #b45309;
        }

        .status-belum {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .footer-ttd {
            margin-top: 25px;
            width: 100%;
            border-collapse: collapse;
        }

        .footer-ttd td {
            border: none;
            padding: 0;
            text-align: center;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $pengaturan->nama_kejuaraan ?? 'KEJUARAAN PENCAK SILAT' }}</h1>
        <h2>REKAPITULASI HASIL PERTANDINGAN TANDING - {{ $pengaturan->gelanggang ?? 'GELANGGANG' }}</h2>
        <div class="meta">
            Dicetak pada: {{ date('d/m/Y H:i:s') }} | Total Partai: {{ $partais->count() }}
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 45px;">Partai</th>
                <th style="width: 75px;">Kelas</th>
                <th>Sudut Biru</th>
                <th style="width: 65px;">Skor</th>
                <th>Sudut Merah</th>
                <th style="width: 130px;">Pemenang</th>
                <th style="width: 110px;">Keterangan</th>
                <th style="width: 60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($partais as $index => $partai)
                @php
                    $scores = $partaiScores[$partai->id] ?? null;
                    $scoreBiru = $scores['biru']['total'] ?? 0;
                    $scoreMerah = $scores['merah']['total'] ?? 0;
                    $pemenangSudut = $partai->sudut_pemenang;
                    $isSelesai = $partai->status_pertandingan === 'Selesai';
                @endphp
                <tr>
                    {{-- NO --}}
                    <td class="text-center">{{ $index + 1 }}</td>

                    {{-- PARTAI --}}
                    <td class="text-center">
                        <span class="badge-partai">{{ $partai->no_partai }}</span>
                    </td>

                    {{-- KELAS --}}
                    <td>
                        <span class="font-bold">{{ $partai->kelas ?? '-' }}</span><br>
                        <span class="kontingen">{{ $partai->fase_babak ?? 'Penyisihan' }}</span>
                    </td>

                    {{-- SUDUT BIRU --}}
                    <td>
                        <div class="box-sudut sudut-biru">
                            <div class="nama-atlet nama-biru">{{ $partai->pesertaBiru->nama ?? 'Belum Ditentukan' }}</div>
                            <div class="kontingen">{{ $partai->pesertaBiru->kontingen ?? '-' }}</div>
                        </div>
                    </td>

                    {{-- SKOR --}}
                    <td class="text-center">
                        <div class="skor-box">
                            <span class="nama-biru">{{ $scoreBiru }}</span>
                            <span style="color: #94a3b8; font-weight: normal; font-size: 8pt;">-</span>
                            <span class="nama-merah">{{ $scoreMerah }}</span>
                        </div>
                    </td>

                    {{-- SUDUT MERAH --}}
                    <td>
                        <div class="box-sudut sudut-merah">
                            <div class="nama-atlet nama-merah">{{ $partai->pesertaMerah->nama ?? 'Belum Ditentukan' }}</div>
                            <div class="kontingen">{{ $partai->pesertaMerah->kontingen ?? '-' }}</div>
                        </div>
                    </td>

                    {{-- PEMENANG --}}
                    <td>
                        @if ($isSelesai && $pemenangSudut)
                            <span class="badge-pemenang {{ strtolower($pemenangSudut) == 'biru' ? 'bg-pemenang-biru' : 'bg-pemenang-merah' }}">
                                SUDUT {{ strtoupper($pemenangSudut) }}
                            </span>
                            <div class="nama-atlet" style="font-size: 8pt; margin-top: 2px;">
                                {{ $partai->pemenang->nama ?? ($pemenangSudut === 'Biru' ? ($partai->pesertaBiru->nama ?? '-') : ($partai->pesertaMerah->nama ?? '-')) }}
                            </div>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">-</span>
                        @endif
                    </td>

                    {{-- ALASAN / KETERANGAN --}}
                    <td>
                        @if ($isSelesai && $partai->alasan_pemenang)
                            <div class="alasan">{{ $partai->alasan_pemenang }}</div>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">-</span>
                        @endif
                    </td>

                    {{-- STATUS --}}
                    <td class="text-center">
                        @if ($partai->status_pertandingan === 'Selesai')
                            <span class="badge-status status-selesai">SELESAI</span>
                        @elseif ($partai->status_pertandingan === 'Berlangsung')
                            <span class="badge-status status-berlangsung">TANDING</span>
                        @else
                            <span class="badge-status status-belum">BELUM</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data pertandingan tanding yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-ttd">
        <tr>
            <td style="width: 33%;">
                Mengetahui,<br>
                <strong>Ketua Pertandingan</strong>
                <br><br><br><br>
                ( .................................................. )
            </td>
            <td style="width: 33%;"></td>
            <td style="width: 33%;">
                {{ $pengaturan->gelanggang ?? 'Gelanggang' }}, {{ date('d F Y') }}<br>
                <strong>Ketua Dewan Juri</strong>
                <br><br><br><br>
                ( .................................................. )
            </td>
        </tr>
    </table>

</body>
</html>

