@extends('admin.layouts.sidebar')

@section('title', 'Report')

@section('content')
<div class="p-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($results as $result)
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-4 border border-gray-100">
                
                <!-- Logo Ujian -->
                @if ($result->logo)
                    <img 
                        src="{{ asset('storage/logos/' . $result->logo) }}" 
                        alt="{{ $result->nama_ujian }}"
                        class="w-full h-32 object-cover rounded-md mb-3"
                    >
                @else
                    <div class="w-full h-32 bg-gray-100 rounded-md flex items-center justify-center text-gray-400 mb-3">
                        <i class="fas fa-image text-2xl"></i>
                    </div>
                @endif

                <!-- Detail Ujian -->
                <h2 class="font-semibold text-gray-800 text-lg mb-1">
                    {{ $result->nama_ujian }}
                </h2>
                <p class="text-gray-500 text-sm">
                    {{ $result->question_count }} Soal
                </p>
                <p class="text-gray-400 text-xs">
                    {{ $result->duration }} menit
                </p>

                <!-- Tombol Lihat Detail (Icon Mata) -->
                <div class="mt-4 flex justify-end">
                    <a 
                        href="{{ route('admin.reports.show', $result->id) }}"
                        class="w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-full hover:bg-blue-600 transition"
                        title="Lihat Detail"
                    >
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        @empty
            <p class="text-gray-500 italic col-span-full text-center py-10">
                Belum ada ujian terdaftar.
            </p>
        @endforelse
    </div>
</div>
@endsection
