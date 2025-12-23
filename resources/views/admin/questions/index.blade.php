@extends('admin.layouts.sidebar')

@section('content')
<div class="p-6">

    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Soal Ujian: <span class="text-yellow-600">{{ $exam->nama_ujian ?? $exam->name }}</span>
        </h2>

        <div class="flex gap-3">
            <a href="{{ route('admin.questions.create', $exam->id) }}"
            class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Soal
            </a>
            <!-- Import Excel Button -->
            <button
                onclick="openImportModal({{ $exam->id }}, '{{ $exam->nama_ujian ?? $exam->name }}')"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-green-100 text-green-600 hover:bg-green-200 transition"
                title="Import Soal Excel">
                <i class="fas fa-file-excel"></i>
            </button>
        </div>
    </div>

    {{-- Success Notification --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 border border-green-300 rounded-md px-4 py-2 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Notification --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-800 border border-red-300 rounded-md px-4 py-2 mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Soal Table --}}
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="py-3 px-4 border-b w-16">No</th>
                    <th class="py-3 px-4 border-b">Soal</th>
                    <th class="py-3 px-4 border-b w-32">Tipe</th>
                    <th class="py-3 px-4 border-b w-40 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($questions as $q)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 px-4 border-b">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 border-b">
                        <div class="font-medium text-gray-800 mb-1">{{ Str::limit($q->question_text, 80) }}</div>
                        @if($q->question_file)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                                Ada File
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 border-b text-sm text-gray-600">
                        @if($q->jenis_soal == 'pilihan_ganda') Pilihan Ganda
                        @elseif($q->jenis_soal == 'esai') Esai
                        @elseif($q->jenis_soal == 'benar_salah') Benar/Salah
                        @else Pilihan Ganda @endif
                    </td>
                    <td class="py-3 px-4 border-b text-center">
                        <div class="flex justify-center space-x-2">
                            {{-- 🔍 Tombol Lihat --}}
                            <button onclick="openPreview({{ $q->id }})" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>

                            {{-- Edit --}}
                            <a href="{{ route('admin.questions.edit', [$exam->id, $q->id]) }}" class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('admin.questions.destroy', [$exam->id, $q->id]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-500 italic">
                        Belum ada soal untuk ujian ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modals Section --}}
@foreach ($questions as $q)
<div id="modal-{{ $q->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl flex flex-col max-h-[90vh]">
        
        {{-- Modal Header --}}
        <div class="flex justify-between items-center p-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Detail Soal #{{ $loop->iteration }}</h3>
            <button onclick="closePreview({{ $q->id }})" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Modal Body (Scrollable) --}}
        <div class="p-6 overflow-y-auto">
            
            {{-- Teks Soal --}}
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Pertanyaan</h4>
                <div class="text-gray-800 text-lg leading-relaxed whitespace-pre-wrap">{{ $q->question_text }}</div>
            </div>

            {{-- File Media --}}
            @if ($q->question_file)
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Lampiran File</h4>
                    @php $ext = strtolower(pathinfo($q->question_file, PATHINFO_EXTENSION)); @endphp

                    @if (in_array($ext, ['jpg','jpeg','png','gif','webp']))
<img src="{{ Storage::url($q->question_file) }}" 
     onerror="this.src='/images/placeholder.png'; this.onerror=null;"
     class="max-h-64 rounded shadow-sm mx-auto object-contain">
                    @elseif ($ext === 'pdf')
                        <iframe src="{{ asset('storage/'.$q->question_file) }}" class="w-full h-64 rounded border"></iframe>
                    @elseif (in_array($ext, ['mp3','wav','ogg']))
                        <audio controls class="w-full"><source src="{{ asset('storage/'.$q->question_file) }}"></audio>
                    @else
                        <a href="{{ asset('storage/'.$q->question_file) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download File Lampiran
                        </a>
                    @endif
                </div>
            @endif

            {{-- Opsi Jawaban --}}
            <div class="mb-2">
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Jawaban</h4>
                
                @if($q->jenis_soal == 'pilihan_ganda' || !$q->jenis_soal)
                    <div class="space-y-3">
                        @foreach(['a', 'b', 'c', 'd'] as $opt)
                            @php 
                                $isCorrect = $q->jawaban_benar == strtoupper($opt); 
                                $optText = $q->{'option_'.$opt};
                            @endphp
                            <div class="flex items-start p-3 rounded-lg border {{ $isCorrect ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}">
                                <div class="shrink-0 w-8 h-8 flex items-center justify-center rounded-full {{ $isCorrect ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-500' }} font-bold text-sm mr-3">
                                    {{ strtoupper($opt) }}
                                </div>
                                <div class="grow pt-1">
                                    <span class="{{ $isCorrect ? 'text-green-900 font-medium' : 'text-gray-700' }}">{{ $optText }}</span>
                                </div>
                                @if($isCorrect)
                                    <div class="shrink-0 text-green-600 ml-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                @elseif($q->jenis_soal == 'benar_salah')
                    <div class="flex gap-4">
                        <div class="flex-1 p-4 rounded-lg border text-center {{ $q->jawaban_benar == 'true' ? 'bg-green-50 border-green-500 text-green-700 font-bold' : 'bg-gray-50 text-gray-400' }}">
                            BENAR (TRUE)
                            @if($q->jawaban_benar == 'true') <span class="block text-xs mt-1">✅ Jawaban Benar</span> @endif
                        </div>
                        <div class="flex-1 p-4 rounded-lg border text-center {{ $q->jawaban_benar == 'false' ? 'bg-red-50 border-red-500 text-red-700 font-bold' : 'bg-gray-50 text-gray-400' }}">
                            SALAH (FALSE)
                            @if($q->jawaban_benar == 'false') <span class="block text-xs mt-1">✅ Jawaban Benar</span> @endif
                        </div>
                    </div>

                @elseif($q->jenis_soal == 'esai')
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <span class="font-semibold text-yellow-800">Kata Kunci Jawaban:</span>
                        <p class="mt-1 text-gray-800">{{ $q->jawaban_benar }}</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- Modal Footer --}}
        <div class="p-5 border-t border-gray-100 bg-gray-50 rounded-b-xl flex justify-end">
            <button onclick="closePreview({{ $q->id }})" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">
                Tutup
            </button>
            <a href="{{ route('admin.questions.edit', [$exam->id, $q->id]) }}" class="ml-3 px-5 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-md">
                Edit Soal
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all scale-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Import Soal Excel</h3>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        
        <p class="text-sm text-gray-500 mb-4 bg-blue-50 p-3 rounded border border-blue-100">
            <span class="font-semibold text-blue-700">Format Excel:</span><br>
            Kolom A1: <b>Soal</b>, B1: <b>Pilihan A</b>, C1: <b>Pilihan B</b>, dst...<br>
            Kolom F1: <b>Jawaban Benar</b> (A/B/C/D)
        </p>

        <form method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx)</label>
                <input type="file" name="file" accept=".xlsx,.xls"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500" required>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 shadow-md transition">Import Sekarang</button>
            </div>
        </form>
    </div>
</div>

{{-- Script --}}
<script>
    function openPreview(id) {
        const modal = document.getElementById('modal-' + id);
        if(modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
    }
    function closePreview(id) {
        const modal = document.getElementById('modal-' + id);
        if(modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function openImportModal(examId, examName) {
        // document.getElementById('examTitle').innerText = 'Ujian: ' + examName; // Removed as we use static hint
        document.getElementById('importForm').action = `/admin/exams/${examId}/import-soal`;
        document.getElementById('importModal').classList.remove('hidden');
        document.getElementById('importModal').classList.add('flex');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
        document.getElementById('importModal').classList.remove('flex');
    }

    // Close modal on click outside
    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            event.target.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
</script>

@endsection
