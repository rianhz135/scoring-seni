<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Hasil Pool</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header-title { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .header-subtitle { font-size: 12px; color: #666; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #777; padding: 6px 8px; vertical-align: middle; }
        th { background-color: #f3f4f6; font-weight: bold; }
        
        /* Baris judul partai */
        .row-partai { background-color: #dbeafe; font-weight: bold; font-size: 12px; color: #1e40af;}
        .rank-circle {
            display: inline-block; width: 18px; height: 18px; 
            line-height: 18px; text-align: center; 
            border-radius: 50%; border: 1px solid #777;
        }

        /* --- TAMBAHAN STYLE UNTUK DISKUALIFIKASI --- */
        .text-red { color: #dc2626; }
        .line-through { text-decoration: line-through; opacity: 0.7; }
        .bg-red-light { background-color: #fef2f2; }
    </style>
</head>
<body>

    <div class="text-center uppercase">
        <div class="header-title">REKAPITULASI HASIL KLASEMEN SISTEM POOL</div>
        <div class="header-subtitle">Dokumen Resmi Pencak Silat Kategori Seni (Sistem Pool)</div>
    </div>

    @forelse($data as $partai)
        <table>
            <thead>
                <tr>
                    <td colspan="7" class="row-partai uppercase">
                        PARTAI: {{ $partai['no_partai'] }} &nbsp;&nbsp;|&nbsp;&nbsp; 
                        GELANGGANG: {{ $partai['gelanggang'] }} &nbsp;&nbsp;|&nbsp;&nbsp; 
                        KATEGORI: {{ $partai['kategori'] }}
                    </td>
                </tr>
                <tr>
                    <th style="width: 5%;" class="text-center">Rank</th>
                    <th style="width: 35%;" class="text-left">Peserta & Kontingen</th>
                    <th style="width: 10%;" class="text-center">Waktu</th>
                    <th style="width: 10%;" class="text-center">Median</th>
                    <th style="width: 15%;" class="text-center">Deviasi</th>
                    <th style="width: 10%;" class="text-center">Hukuman</th>
                    <th style="width: 15%;" class="text-right">Total Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($partai['peserta'] as $p)
                {{-- Beri warna background sedikit merah muda jika diskualifikasi --}}
                <tr class="{{ $p['is_disq'] ? 'bg-red-light' : '' }}">
                    <td class="text-center">
                        @if($p['is_disq'])
                            <span class="font-bold text-red">{{ $p['rank'] }}</span>
                        @else
                            <span class="rank-circle font-bold">{{ $p['rank'] }}</span>
                        @endif
                    </td>
                    <td>
                        {{-- Coret nama peserta jika diskualifikasi --}}
                        <div class="font-bold uppercase {{ $p['is_disq'] ? 'line-through' : '' }}">{{ $p['nama'] }}</div>
                        <div style="font-size: 9px; color: #555;" class="uppercase {{ $p['is_disq'] ? 'line-through' : '' }}">{{ $p['kontingen'] }}</div>
                    </td>
                    <td class="text-center">{{ $p['waktu'] }}</td>
                    <td class="text-center font-bold">{{ $p['median'] }}</td>
                    <td class="text-center">{{ $p['deviasi'] }}</td>
                    <td class="text-center font-bold" style="color: #dc2626;">-{{ $p['hukuman'] }}</td>
                    
                    {{-- Total nilai akan menjadi DISQ warna merah --}}
                    <td class="text-right font-bold" style="font-size: 13px; color: {{ $p['is_disq'] ? '#dc2626' : '#2563eb' }};">
                        {{ $p['total'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <div class="text-center mt-20 text-gray-500">
            Belum ada hasil partai yang dapat diekspor.
        </div>
    @endforelse

</body>
</html>