<!DOCTYPE html>
<html>

<head>
    <title>Laporan Hasil Solo</title>
    <style>
        /* Pengaturan Kertas & Font Utama */
        @page {
            margin: 1.5cm;
            size: a4 landscape;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.3;
        }

        /* Header Laporan */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }

        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 16pt;
            color: #2c3e50;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 9pt;
            color: #7f8c8d;
        }

        /* Tabel Utama */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.main-table>thead>tr>th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
            border: 1px solid #cbd5e1;
            padding: 10px 8px;
            font-size: 8.5pt;
            text-transform: uppercase;
            color: #475569;
        }

        table.main-table>tbody>tr>td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        /* Kotak Komparasi Sudut */
        .box-sudut {
            padding: 6px 10px;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .box-sudut:last-child {
            margin-bottom: 0;
        }

        .sudut-biru {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
        }

        .sudut-merah {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }

        /* Inner Table (Trik agar sejajar di DomPDF) */
        table.inner-table {
            width: 100%;
            border: none;
            margin: 0;
            padding: 0;
        }

        table.inner-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .td-info {
            width: 75%;
            text-align: left;
        }

        .td-total {
            width: 25%;
            text-align: right;
        }

        /* Tipografi dalam Kotak */
        .nama {
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }

        .kontingen {
            font-size: 8pt;
            color: #64748b;
            margin-bottom: 4px;
            display: block;
            font-style: italic;
        }

        .stats {
            font-size: 7.5pt;
            color: #475569;
            letter-spacing: -0.2px;
        }

        .total-nilai {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: -0.5px;
        }

        .name-total {
            font-size: 10pt;
            font-weight: bold;
        }

        /* Warna Teks */
        .text-biru {
            color: #0369a1;
        }

        .text-merah {
            color: #b91c1c;
        }

        /* Mode Diskualifikasi */
        .label-disq {
            text-decoration: line-through;
            color: #dc2626;
        }

        .badge-disq {
            display: inline-block;
            background: #fee2e2;
            color: #b91c1c;
            padding: 3px 6px;
            border: 1px solid #fca5a5;
            font-size: 7.5pt;
            font-weight: bold;
            margin-top: 4px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rekapitulasi Hasil Pertandingan Solo</h1>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">Partai</th>
                <th style="width: 15%;">Kelas</th>
                <th style="width: 60%;">Komparasi Nilai Sudut</th>
                <th style="width: 13%;">Pemenang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($partais as $index => $partai)
                @php
                    $biru =
                        $partai->detail_peserta->firstWhere('sudut', 'Biru') ??
                        $partai->detail_peserta->firstWhere('sudut', 'biru');
                    $merah =
                        $partai->detail_peserta->firstWhere('sudut', 'Merah') ??
                        $partai->detail_peserta->firstWhere('sudut', 'merah');

                    $isDisqBiru = ($biru->is_disqualifikasi ?? ($biru->is_disqulifikasi ?? 0)) == 1;
                    $isDisqMerah = ($merah->is_disqualifikasi ?? ($merah->is_disqulifikasi ?? 0)) == 1;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold" style="font-size: 11pt;">{{ $partai->no_partai }}</td>
                    <td class="text-center" style="font-size: 8.5pt;">{{ $partai->kelas->label_kelas ?? '-' }}</td>

                    {{-- KOLOM KOMPARASI --}}
                    <td>
                        {{-- BLOK BIRU --}}
                        @if ($biru)
                            <div class="box-sudut sudut-biru">
                                <table class="inner-table">
                                    <tr>
                                        <td class="td-info">
                                            <span class="nama text-biru {{ $isDisqBiru ? 'label-disq' : '' }}">
                                                {{ $biru->peserta->nama ?? ($biru->peserta->nama_peserta ?? $biru->peserta->nama_lengkap) }}
                                            </span>
                                            <span class="kontingen">({{ $biru->peserta->kontingen }})</span>

                                            @if ($isDisqBiru)
                                                <span class="badge-disq">DISKUALIFIKASI</span>
                                            @else
                                                <div class="stats">
                                                    MED: <b>{{ number_format($biru->nilai_median, 3) }}</b> |
                                                    DEV: <b>{{ $biru->nilai_deviasi }}</b> |
                                                    HUK: <b>{{ number_format($biru->hukuman_ketua, 2) }}</b>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="td-total">
                                            @if (!$isDisqBiru)
                                                <span class="name-total text-merah">Total Nilai</span><br>
                                                <span
                                                    class="total-nilai text-biru">{{ number_format($biru->total_nilai, 3) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endif

                        {{-- BLOK MERAH --}}
                        @if ($merah)
                            <div class="box-sudut sudut-merah">
                                <table class="inner-table">
                                    <tr>
                                        <td class="td-info">
                                            <span class="nama text-merah {{ $isDisqMerah ? 'label-disq' : '' }}">
                                                {{ $merah->peserta->nama ?? ($merah->peserta->nama_peserta ?? $merah->peserta->nama_lengkap) }}
                                            </span>
                                            <span class="kontingen">({{ $merah->peserta->kontingen }})</span>

                                            @if ($isDisqMerah)
                                                <span class="badge-disq">DISKUALIFIKASI</span>
                                            @else
                                                <div class="stats">
                                                    MED: <b>{{ number_format($merah->nilai_median, 3) }}</b> |
                                                    DEV: <b>{{ $merah->nilai_deviasi }}</b> |
                                                    HUK: <b>{{ number_format($merah->hukuman_ketua, 2) }}</b>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="td-total">
                                            @if (!$isDisqMerah)
                                                <span class="name-total text-merah">Total Nilai</span><br>
                                                <span
                                                    class="total-nilai text-merah">{{ number_format($merah->total_nilai, 3) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endif
                    </td>

                    {{-- KOLOM PEMENANG --}}
                    <td class="text-center">
                        <span style="font-size: 8pt; color: #94a3b8; font-style: italic;">(Proses Rekap)</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
