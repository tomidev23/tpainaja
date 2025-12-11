@extends('admin.layouts.sidebar')

@section('title', 'Report Hasil Ujian')

@section('content')
<div class="p-6">

    <h2 class="text-2xl font-bold mb-6">Report Hasil Ujian</h2>

    <div class="bg-white shadow rounded-lg p-4">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-3 border">Peserta</th>
                    <th class="px-4 py-3 border">Email</th>
                    <th class="px-4 py-3 border">Ujian</th>
                    <th class="px-4 py-3 border">Nilai</th>
                    <th class="px-4 py-3 border">Benar</th>
                    <th class="px-4 py-3 border">Salah</th>
                    <th class="px-4 py-3 border">Kosong</th>
                </tr>
            </thead>

            <tbody>
                @forelse($results as $r)
                    <tr class="border hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $r->participant->user->name }}</td>
                        <td class="px-4 py-3">{{ $r->participant->user->email }}</td>
                        <td class="px-4 py-3">{{ $r->exam->nama_ujian }}</td>
                        <td class="px-4 py-3 font-bold text-blue-600">{{ $r->score }}</td>
                        <td class="px-4 py-3 text-green-600">{{ $r->correct_answers }}</td>
                        <td class="px-4 py-3 text-red-600">{{ $r->wrong_answers }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $r->empty_answers }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-4">
                            Belum ada hasil ujian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
