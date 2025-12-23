@extends('staff.layouts.app')

@section('content')
<div class="p-6">
    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Soal Ujian: <span class="text-yellow-600">{{ $exam->nama_ujian ?? $exam->name }}</span>
        </h2>

        <a href="{{ route('staff.questions.create', $exam->id) }}"
           class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 transition duration-200 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Soal
        </a>
    </div>

    {{-- Success Notification --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 border border-green-300 rounded-md px-4 py-2 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Soal Table --}}
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="py-3 px-4 border-b">No</th>
                    <th class="py-3 px-4 border-b">Soal</th>
                    <th class="py-3 px-4 border-b">Jawaban Benar</th>
                    <th class="py-3 px-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($questions as $q)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="py-3 px-4 border-b">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 border-b">{{ Str::limit($q->question_text, 60) }}</td>
                    <td class="py-3 px-4 border-b">{{ $q->jawaban_benar }}</td>
                    <td class="py-3 px-4 border-b text-center">
                        <div class="flex justify-center space-x-3">
                            <a href="{{ route('staff.questions.edit', [$exam->id, $q->id]) }}"
                               class="text-blue-600 font-medium hover:text-blue-800">Edit</a>

                            <form action="{{ route('staff.questions.destroy', [$exam->id, $q->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 font-medium hover:text-red-800"
                                    onclick="return confirm('Yakin ingin menghapus soal ini?')">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-4 text-center text-gray-500 italic">
                        Belum ada soal untuk ujian ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
