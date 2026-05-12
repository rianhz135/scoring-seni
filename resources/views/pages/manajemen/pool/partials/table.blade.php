<div class="space-y-2">

    {{-- HEADER --}}
    <div class="rounded-lg border border-gray-200 overflow-hidden">
        <table class="w-full table-fixed text-left">
            <colgroup>
                <col style="width: 100px;">
                <col style="width: 170px;">
                <col style="width: 60px;">
                <col>
                <col style="width: 120px;">
            </colgroup>

            <thead class="bg-gray-50 text-[11px] font-bold uppercase tracking-wide text-gray-700">
                <tr>
                    <th class="px-3 py-2">Partai</th>
                    <th class="px-3 py-2">Kategori</th>
                    <th class="px-2 py-2 text-center">No</th>
                    <th class="px-3 py-2">Peserta</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>

    @forelse($data as $partai)
        @php $jumlah = $partai->detail_peserta->count(); @endphp

        <div class="rounded-lg border border-gray-200 overflow-hidden bg-white">
            <table class="w-full table-fixed text-left">
                <colgroup>
                    <col style="width: 100px;">
                    <col style="width: 170px;">
                    <col style="width: 60px;">
                    <col>
                    <col style="width: 120px;">
                </colgroup>

                <tbody class="divide-y divide-gray-100">

                    @foreach($partai->detail_peserta as $detail)

                        <tr class="hover:bg-blue-50/40 transition">

                            @if($loop->first)

                                {{-- PARTAI --}}
                                <td rowspan="{{ $jumlah }}"
                                    class="px-3 py-2.5 align-middle bg-gray-50 border-r border-gray-200">

                                    <div class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold">
                                        Partai
                                    </div>

                                    <div class="text-lg font-black text-gray-800 leading-tight">
                                        {{ $partai->no_partai }}
                                    </div>

                                </td>

                                {{-- KATEGORI --}}
                                <td rowspan="{{ $jumlah }}"
                                    class="px-3 py-2.5 align-middle bg-gray-50 border-r border-gray-200">

                                    <div class="font-bold text-sm text-gray-800 leading-tight">
                                        {{ $partai->kategori->nama_kategori ?? '-' }}
                                    </div>

                                    <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold border border-gray-200 bg-white text-gray-600">
                                        {{ $partai->kelas->label_kelas ?? '-' }}
                                    </div>

                                </td>

                            @endif

                            {{-- NOMOR --}}
                            <td class="px-2 py-2 text-center align-middle">

                                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-black text-blue-700">
                                    {{ $detail->no_urut ?? '-' }}
                                </span>

                            </td>

                            {{-- PESERTA --}}
                            <td class="px-3 py-2 align-middle">

                                <div class="text-sm font-bold text-gray-900 uppercase leading-tight">
                                    {{ $detail->peserta->nama_peserta ?? '-' }}
                                </div>

                                <div class="text-[10px] text-blue-500 font-medium uppercase mt-0.5 leading-none">
                                    {{ $detail->peserta->kontingen ?? '-' }}
                                </div>

                            </td>

                            {{-- AKSI --}}
                            <td class="px-2 py-2 align-middle text-center">

                                <a href="#"
                                    class="inline-flex items-center justify-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[11px] font-bold px-3 py-1.5 rounded-md transition-all w-full max-w-[90px]">

                                    <svg class="w-3.5 h-3.5 shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.2"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>

                                    </svg>

                                    Mulai
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>
            </table>
        </div>

    @empty

        <div class="rounded-lg border border-gray-200 px-4 py-10 text-center text-sm italic text-gray-400">
            Belum ada antrean peserta untuk kategori ini.
        </div>

    @endforelse


    {{-- PAGINATION --}}
    @if(method_exists($data, 'links'))
        <div class="pt-2">

            {{ $data->onEachSide(1)->links() }}

        </div>
    @endif

</div>