<!DOCTYPE html>
<html>

<head>
    <title>Laporan Hasil Ganda</title>
    <style>
        @page {
            margin: 1cm;
            size: a4 landscape;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8.5pt;
            color: #333;
            line-height: 1.1;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
        }

        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 14pt;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.main-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px;
            font-size: 8pt;
            text-transform: uppercase;
        }

        /* Mengecilkan padding td agar tinggi kolom menyusut */
        table.main-table td {
            border: 1px solid #cbd5e1;
            padding: 3px 5px;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        /* Table pembungkus kiri-kanan */
        table.wrapper-komparasi {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        table.wrapper-komparasi td {
            border: none !important;
            padding: 0 !important;
            width: 50%;
            vertical-align: top;
        }

        /* Kotak Sudut - Diperketat */
        .box-sudut {
            padding: 4px 6px;
            border-radius: 3px;
            margin: 2px;
        }

        .sudut-biru {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
        }

        .sudut-merah {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }

        /* Inner Table untuk Nama & Total */
        table.inner-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.inner-table td {
            border: none !important;
            padding: 0 !important;
        }

        .nama {
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
            color: #1e293b;
        }

        .kontingen {
            font-size: 7pt;
            color: #64748b;
            font-style: italic;
            display: block;
            margin-bottom: 2px;
        }

        .stats {
            font-size: 6.5pt;
            color: #475569;
        }

        .total-nilai {
            font-size: 13pt;
            font-weight: bold;
        }

        .text-biru {
            color: #0369a1;
        }

        .text-merah {
            color: #b91c1c;
        }

        .badge-disq {
            background: #fee2e2;
            color: #b91c1c;
            padding: 1px 4px;
            border: 1px solid #fca5a5;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-pemenang {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
        }

        .bg-pemenang-biru {
            background-color: #2563eb;
        }

        /* Biru yang lebih solid untuk PDF */
        .bg-pemenang-merah {
            background-color: #dc2626;
        }

        /* Merah yang lebih solid untuk PDF */
        .text-muted {
            color: #94a3b8;
            font-style: italic;
            font-size: 8pt;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rekapitulasi Hasil Pertandingan Ganda</h1>
        <p>Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 50px;">Partai</th>
                <th style="width: 100px;">Kelas</th>
                <th>Komparasi Nilai Sudut</th>
                <th style="width: 100px;">Pemenang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($partais as $index => $partai)
                @php
                    // Ambil SEMUA peserta di sudut biru (koleksi)
                    $detailBiru = $partai->detail_peserta->filter(fn($d) => strtolower($d->sudut) == 'biru');
                    // Ambil SEMUA peserta di sudut merah (koleksi)
                    $detailMerah = $partai->detail_peserta->filter(fn($d) => strtolower($d->sudut) == 'merah');

                    // Ambil satu sampel untuk data nilai (karena nilai ganda biasanya sama per tim)
                    $biru = $detailBiru->first();
                    $merah = $detailMerah->first();

                    $isDisqBiru = ($biru->is_disqualifikasi ?? ($biru->is_disqulifikasi ?? 0)) == 1;
                    $isDisqMerah = ($merah->is_disqualifikasi ?? ($merah->is_disqulifikasi ?? 0)) == 1;
                @endphp

                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold">{{ $partai->no_partai }}</td>
                    <td class="text-center">{{ $partai->kelas->label_kelas ?? '-' }}</td>
                    <td>
                        <table class="wrapper-komparasi">
                            <tr>
                                {{-- SUDUT BIRU --}}
                                <td class="td-kiri">
                                    @if ($biru)
                                        <div class="box-sudut sudut-biru">
                                            <table class="inner-table">
                                                <tr>
                                                    <td>
                                                        {{-- NAMA ATLET BIRU (GABUNG DENGAN &) --}}
                                                        <span class="nama text-biru" style="display:block;">
                                                            {{ $detailBiru->map(function ($db) {
                                                                    return $db->peserta->nama ?? ($db->peserta->nama_peserta ?? 'Tanpa Nama');
                                                                })->implode(' & ') }}
                                                        </span>

                                                        <span
                                                            class="kontingen">({{ $biru->peserta->kontingen ?? '-' }})</span>

                                                        {{-- Stats Nilai --}}
                                                        @if ($isDisqBiru)
                                                            <span class="badge-disq">DISQ</span>
                                                        @else
                                                            <div class="stats">
                                                                M: {{ number_format($biru->nilai_median, 2) }} |
                                                                D: {{ $biru->nilai_deviasi }} |
                                                                H: {{ number_format($biru->hukuman_ketua, 1) }} |
                                                                W:
                                                                {{ floor(($biru->waktu_tampil ?? 0) / 60) }}:{{ str_pad(($biru->waktu_tampil ?? 0) % 60, 2, '0', STR_PAD_LEFT) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right; width: 45px; vertical-align: middle;">
                                                        @if (!$isDisqBiru)
                                                            <span
                                                                class="total-nilai text-biru">{{ number_format($biru->total_nilai, 3) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endif
                                </td>

                                {{-- SUDUT MERAH --}}
                                <td class="td-kanan">
                                    @if ($merah)
                                        <div class="box-sudut sudut-merah">
                                            <table class="inner-table">
                                                <tr>
                                                    <td>
                                                        {{-- NAMA ATLET MERAH (GABUNG DENGAN &) --}}
                                                        <span class="nama text-merah" style="display:block;">
                                                            {{ $detailMerah->map(function ($dm) {
                                                                    return $dm->peserta->nama ?? ($dm->peserta->nama_peserta ?? 'Tanpa Nama');
                                                                })->implode(' & ') }}
                                                        </span>

                                                        <span
                                                            class="kontingen">({{ $merah->peserta->kontingen ?? '-' }})</span>

                                                        {{-- Stats Nilai & Waktu --}}
                                                        @if ($isDisqMerah)
                                                            <span class="badge-disq">DISQ</span>
                                                        @else
                                                            <div class="stats">
                                                                M: {{ number_format($merah->nilai_median, 2) }} |
                                                                D: {{ $merah->nilai_deviasi }} |
                                                                H: {{ number_format($merah->hukuman_ketua, 1) }} |
                                                                W:
                                                                {{ floor(($merah->waktu_tampil ?? 0) / 60) }}:{{ str_pad(($merah->waktu_tampil ?? 0) % 60, 2, '0', STR_PAD_LEFT) }}
                                                            </div>
                                                        @endif
                                                    </td>

                                                    {{-- Skor Akhir di Sisi Kanan Kotak --}}
                                                    <td style="text-align: right; width: 45px; vertical-align: middle;">
                                                        @if (!$isDisqMerah)
                                                            <span
                                                                class="total-nilai text-merah">{{ number_format($merah->total_nilai, 3) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="text-center">
                        @php
                            // Mencari peserta yang memiliki status is_pemenang = 1
                            $pemenang = $partai->detail_peserta->firstWhere('is_pemenang', 1);
                        @endphp

                        @if ($pemenang)
                            <span
                                class="badge-pemenang {{ strtolower($pemenang->sudut) == 'biru' ? 'bg-pemenang-biru' : 'bg-pemenang-merah' }}">
                                {{ strtoupper($pemenang->sudut) }}
                            </span>
                        @else
                            <span class="text-muted">BELUM DITETAPKAN</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
