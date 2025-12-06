@extends('admin.layouts.sidebar')

@section('title', 'Staff')

@section('content')
<div class="px-6 py-4">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Staff</h2>

        <a href="{{ route('admin.staff.create') }}"
           class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow flex items-center gap-2 transition">
            <i class="fas fa-plus"></i> Tambah Staff
        </a>
    </div>

    <!-- List Staff -->
    <div class="space-y-4">

        @forelse($staff as $s)
        <div class="w-full bg-white shadow rounded-xl flex items-center justify-between px-6 py-4">

            <!-- Kiri: Nama + Email -->
            <div class="flex flex-col">
                <span class="text-lg font-semibold text-gray-800">{{ $s->name }}</span>
                <span class="text-sm text-gray-500">{{ $s->email }}</span>
            </div>

            <!-- Tengah: Password Bintang + Icon Mata -->
            <div class="flex items-center gap-2">
                <span class="tracking-widest text-gray-600">***********</span>
                <i class="fas fa-eye-slash text-gray-600 text-sm"></i>
            </div>

            <!-- Kanan: Tombol Edit + Delete -->
            <div class="flex items-center gap-3">

                <!-- Edit -->
                <a href="{{ route('admin.staff.edit', $s->id) }}"
                   class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition">
                    <i class="fas fa-pen"></i>
                </a>

                <!-- Delete -->
                <form action="{{ route('admin.staff.destroy', $s->id) }}" method="POST" class="inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition btn-delete">
                        <i class="fas fa-times"></i>
                    </button>
                </form>

            </div>
        </div>
        @empty
            <p class="text-gray-500 italic text-center py-10">
                Belum ada staff terdaftar.
            </p>
        @endforelse

    </div>
</div>

<!-- SweetAlert Delete -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".btn-delete");

    deleteButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            let form = this.closest("form");

            Swal.fire({
                title: "Anda yakin?",
                text: "Staff akan dihapus dari sistem!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

@endsection
