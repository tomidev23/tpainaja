@extends('admin.layouts.sidebar')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Tambah Soal Baru</h2>
            <p class="text-sm text-gray-500">Ujian: {{ $exam->nama_ujian ?? $exam->name }}</p>
        </div>

        <form action="{{ route('admin.questions.store', $exam->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="p-6">
            
            @csrf

            {{-- Tampilkan Error Validasi Global --}}
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Jenis Soal --}}
            <div class="mb-6">
                <label class="block font-semibold text-gray-700 mb-2">Jenis Soal</label>
                <select id="question_type" name="question_type" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition">
                    <option value="multiple_choice" {{ old('question_type') == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="essay" {{ old('question_type') == 'essay' ? 'selected' : '' }}>Essay</option>
                    <option value="true_false" {{ old('question_type') == 'true_false' ? 'selected' : '' }}>Benar / Salah</option>
                </select>
            </div>

            {{-- Pertanyaan --}}
            <div class="mb-6">
                <label class="block font-semibold text-gray-700 mb-2">Pertanyaan (Text)</label>
                <textarea name="question_text" rows="4" 
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition" 
                          required>{{ old('question_text') }}</textarea>
                @error('question_text')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Upload File Soal --}}
            <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block font-semibold text-gray-700 mb-2">Upload File Soal (Opsional)</label>
                <input type="file" name="question_file" 
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, PDF, Audio (Max 2MB)</p>
                @error('question_file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- === PILIHAN GANDA === --}}
            <div id="multiple_choice_box">
                <label class="block font-semibold text-gray-700 mb-3">Opsi Jawaban</label>
                <div class="grid grid-cols-1 gap-4 mb-4">
                    @foreach(['a', 'b', 'c', 'd'] as $opt)
                    <div class="flex items-center gap-3">
                        <div class="grow">
                            <input type="text" name="option_{{ $opt }}" 
                                   value="{{ old('option_'.$opt) }}"
                                   placeholder="Opsi {{ strtoupper($opt) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition">
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="jawaban_benar" value="{{ strtoupper($opt) }}" 
                                   {{ old('jawaban_benar') == strtoupper($opt) ? 'checked' : '' }}
                                   class="w-5 h-5 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                            <span class="ml-2 font-bold text-gray-600">{{ strtoupper($opt) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500">* Pilih salah satu radio button di kanan sebagai kunci jawaban.</p>
            </div>

            {{-- === ESSAY === --}}
            <div id="essay_box" class="hidden">
                <label class="block font-semibold text-gray-700 mb-2">Kunci Jawaban Essay (Kata Kunci)</label>
                <textarea name="essay_answer" rows="3" 
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition">{{ old('essay_answer') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Masukkan kata kunci jawaban yang diharapkan.</p>
            </div>

            {{-- === TRUE FALSE === --}}
            <div id="true_false_box" class="hidden">
                <label class="block font-semibold text-gray-700 mb-3">Kunci Jawaban</label>
                <div class="flex gap-6">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 w-full">
                        <input type="radio" name="jawaban_benar" value="true" 
                               {{ old('jawaban_benar') == 'true' ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 border-gray-300 focus:ring-green-500">
                        <span class="ml-2 font-medium text-gray-700">Benar (True)</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 w-full">
                        <input type="radio" name="jawaban_benar" value="false" 
                               {{ old('jawaban_benar') == 'false' ? 'checked' : '' }}
                               class="w-5 h-5 text-red-600 border-gray-300 focus:ring-red-500">
                        <span class="ml-2 font-medium text-gray-700">Salah (False)</span>
                    </label>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.questions.index', $exam->id) }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 transition">
                    Simpan Soal
                </button>
            </div>
        </form>
    </div>

    {{-- Script Dinamis --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('question_type');
            const mcBox = document.getElementById('multiple_choice_box');
            const essayBox = document.getElementById('essay_box');
            const tfBox = document.getElementById('true_false_box');

            function updateForm() {
                let type = select.value;

                mcBox.classList.add('hidden');
                essayBox.classList.add('hidden');
                tfBox.classList.add('hidden');

                // Disable inputs in hidden sections to prevent validation errors
                disableInputs(mcBox, true);
                disableInputs(essayBox, true);
                disableInputs(tfBox, true);

                if (type === 'multiple_choice') {
                    mcBox.classList.remove('hidden');
                    disableInputs(mcBox, false);
                }
                else if (type === 'essay') {
                    essayBox.classList.remove('hidden');
                    disableInputs(essayBox, false);
                }
                else if (type === 'true_false') {
                    tfBox.classList.remove('hidden');
                    disableInputs(tfBox, false);
                }
            }

            function disableInputs(container, disabled) {
                const inputs = container.querySelectorAll('input, textarea, select');
                inputs.forEach(input => input.disabled = disabled);
            }

            select.addEventListener('change', updateForm);
            
            // Run on load to handle old input
            updateForm();
        });
    </script>

</div>
@endsection
