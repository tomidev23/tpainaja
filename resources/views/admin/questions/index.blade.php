@extends('admin.layouts.sidebar')

@section('content')
<div class="p-6">

    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Soal Ujian: <span class="text-yellow-600">{{ $exam->nama_ujian ?? $exam->name }}</span>
        </h2>

        <a href="{{ route('admin.questions.create', $exam->id) }}"
           class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Soal
        </a>
           <!-- Import Excel -->
                    <button
                        onclick="openImportModal({{ $exam->id }}, '{{ $exam->nama_ujian }}')"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 text-green-600"
                        title="Import Soal Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
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
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 px-4 border-b">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 border-b">{{ Str::limit($q->question_text, 60) }}</td>
                    <td class="py-3 px-4 border-b">{{ $q->jawaban_benar }}</td>

                    <td class="py-3 px-4 border-b text-center">
                        <div class="flex justify-center space-x-3">

                            {{-- 🔍 Tombol Lihat --}}
                            <button
                                onclick="openPreview({{ $q->id }})"
                                class="text-yellow-600 font-medium hover:text-yellow-800">
                                Lihat
                            </button>

                            {{-- Edit --}}
                            <a href="{{ route('admin.questions.edit', [$exam->id, $q->id]) }}"
                               class="text-blue-600 font-medium hover:text-blue-800">
                                Edit
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('admin.questions.destroy', [$exam->id, $q->id]) }}" method="POST" class="inline">
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

                {{-- Modal Preview --}}
                <div id="modal-{{ $q->id }}" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white w-full max-w-xl p-6 rounded-lg shadow-xl relative">

                        {{-- Close button --}}
                        <button onclick="closePreview({{ $q->id }})"
                                class="absolute top-3 right-3 text-gray-600 hover:text-black text-xl">&times;</button>

                        <h3 class="text-xl font-bold mb-3">Preview Soal</h3>

                        <p class="text-gray-800 whitespace-pre-line mb-4">{{ $q->question_text }}</p>

                        @if ($q->question_file)
                            @php
                                $ext = strtolower(pathinfo($q->question_file, PATHINFO_EXTENSION));
                            @endphp

                            {{-- Gambar --}}
                            @if (in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <img src="{{ asset('storage/'.$q->question_file) }}" class="w-full rounded mb-3">

                            {{-- PDF --}}
                            @elseif ($ext === 'pdf')
                                <iframe src="{{ asset('storage/'.$q->question_file) }}" class="w-full h-64"></iframe>

                            {{-- Audio --}}
                            @elseif (in_array($ext, ['mp3','wav','ogg']))
                                <audio controls class="w-full mt-2">
                                    <source src="{{ asset('storage/'.$q->question_file) }}">
                                </audio>

                            {{-- Lainnya --}}
                            @else
                                <a href="{{ asset('storage/'.$q->question_file) }}"
                                   target="_blank"
                                   class="text-blue-600 underline">
                                    Download File
                                </a>
                            @endif

                        @endif
                    </div>
                </div>

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

<div id="importModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <h3 class="text-lg font-semibold mb-2">Import Soal Excel</h3>
        <p class="text-sm text-gray-500 mb-4" id="examTitle"></p>

        <form method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls"
                   class="w-full border rounded-md px-3 py-2 mb-4" required>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeImportModal()"
                        class="px-4 py-2 bg-gray-100 rounded-md">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script Modal --}}
<script>
    function openPreview(id) {
        document.getElementById('modal-' + id).classList.remove('hidden');
    }
    function closePreview(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
    }
</script>

<script>
function openImportModal(examId, examName) {
    document.getElementById('examTitle').innerText = 'Ujian: ' + examName;
    document.getElementById('importForm').action =
        `/admin/exams/${examId}/import-soal`;

    document.getElementById('importModal').classList.remove('hidden');
    document.getElementById('importModal').classList.add('flex');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('importModal').classList.remove('flex');
}
</script>

@endsection
