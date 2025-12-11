@extends('admin.layouts.sidebar')

@section('title', 'Peserta')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] px-8 py-6" x-data="{ openAdd: false }">
    <!-- Header Page -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-3">           
            <!-- Tombol Tambah Peserta -->
            <a href="{{ route('admin.participants.create') }}"
                class="flex items-center gap-2 border border-[#635BFF] text-[#635BFF] font-medium px-4 py-2 rounded-lg hover:bg-[#635BFF] hover:text-white transition">                <i class="fas fa-plus-circle"></i>
                Tambah Peserta
        </a>
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="space-y-4">
        @forelse ($participants as $participant)
            <div
                class="flex justify-between items-center bg-white rounded-xl shadow-md px-6 py-4 border border-gray-100 hover:shadow-lg transition duration-200">
                
                <!-- Nama & Email -->
                <div class="flex flex-col">
                    <span class="text-lg font-semibold text-gray-800">{{ $participant->name }}</span>
                    <span class="text-sm text-gray-500">{{ $participant->email }}</span>
                </div>

                <!-- Password & Aksi -->
                <div class="flex items-center gap-6">
                    <div class="flex items-center text-gray-700">
                       {{--  <span class="tracking-widest font-semibold">********</span>
                        <i class="fas fa-eye-slash text-gray-400 ml-2"></i> --}}
                    </div>

                    <!-- Tombol Edit dan Hapus -->
<div class="flex items-center gap-3 z-10 relative">
    <!-- Edit -->
    <a href="{{ route('admin.participants.edit', $participant->id) }}"
        class="w-9 h-9 flex items-center justify-center rounded-full bg-[#FFF8E1] shadow-md hover:shadow-lg hover:scale-105 transition duration-200"
        title="Edit Peserta">
        <i class="fas fa-pen text-[#FBBF24] text-[14px]"></i>
    </a>

   <!-- HAPUS -->
<form action="{{ route('admin.participants.destroy', $participant->id) }}" method="POST"
      class="delete-form inline-block z-10 relative">
    @csrf
    @method('DELETE')
    <button type="button"
        class="btn-delete w-9 h-9 flex items-center justify-center rounded-full bg-[#FFE4E6] shadow-md hover:shadow-lg hover:scale-105 transition duration-200"
        title="Hapus Peserta">
        <i class="fas fa-times text-[#EF4444] text-[14px]"></i>
    </button>
</form>


</div>

                </div>
            </div>
        @empty
            <p class="text-gray-500 italic">Belum ada peserta terdaftar.</p>
        @endforelse
    </div>

</div>
@endsection
