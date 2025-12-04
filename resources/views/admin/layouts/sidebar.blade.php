<!DOCTYPE html>
<html lang="id" x-data="{ activeMenu: '{{ request()->route()->getName() }}' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | TPAinaja</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="bg-[#F8FAFC] text-gray-800 flex h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-[#FFC920] flex flex-col justify-between py-6 text-gray-800">
        <div>
            <div class="flex items-center justify-center mb-10">
                <img src="{{ asset('images/logo-tpainaja.png') }}" alt="TPAinaja Logo" class="w-32 h-auto">
            </div>

            <!-- MENU -->
            <nav class="space-y-2 px-4 font-medium">

                <a href="{{ route('admin.dashboard') }}"
                   :class="{'bg-transparent text-white': activeMenu === 'admin.dashboard'}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg transition hover:bg-white/40">
                    <i class="fas fa-home w-5 text-center"></i> Dashboard
                </a>

                <a href="{{ route('admin.exam.index') }}"
                   :class="{'bg-transparent text-white': activeMenu.startsWith('admin.exam')}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg transition hover:bg-white/40">
                    <i class="fas fa-book w-5 text-center"></i> Ujian
                </a>

                <a href="{{ route('admin.participants.index') }}"
                   :class="{'bg-transparent text-white': activeMenu === 'admin.participants.index'}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg transition hover:bg-white/40">
                    <i class="fas fa-users w-5 text-center"></i> Peserta
                </a>

                <a href="{{ route('admin.staff.index') }}"
                   :class="{'bg-transparent text-white': activeMenu === 'admin.staff.index'}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg transition hover:bg-white/40">
                    <i class="fas fa-user-tie w-5 text-center"></i> Staff
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   :class="{'bg-transparent text-white': activeMenu === 'admin.reports.index'}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg transition hover:bg-white/40">
                    <i class="fas fa-file-alt w-5 text-center"></i> Reports
                </a>

            </nav>
        </div>

        <!-- LOGOUT BUTTON -->
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md w-40 mx-4 font-medium transition">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </form>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="flex items-center justify-between px-8 py-4 bg-white border-b shadow-sm">
            <h1 class="text-lg font-semibold text-gray-700">@yield('title')</h1>

            <div class="flex items-center space-x-4">
                <select class="border border-gray-300 rounded-md text-sm px-2 py-1">
                    <option>Last Week</option>
                    <option>This Month</option>
                </select>

                <!-- PROFILE -->
                <button type="button"
                    class="w-10 h-10 rounded-full overflow-hidden shadow-md flex items-center justify-center bg-indigo-500 hover:bg-indigo-600"
                    data-bs-toggle="modal" data-bs-target="#profileModal">

                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-white font-bold">
                            {{ strtoupper(substr(trim(Auth::user()->name ?? 'U'), 0, 1)) }}
                        </span>
                    @endif
                </button>
            </div>
        </header>

        <section class="flex-1 p-8 bg-[#F8FAFC]">
            @yield('content')
        </section>
    </main>

    @include('modal.modalprofile')

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ========== SAVE CONFIRMATION ========= -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const saveButtons = document.querySelectorAll(".btn-save");

        saveButtons.forEach(btn => {
            btn.addEventListener("click", function () {
                let form = this.closest("form");

                Swal.fire({
                    title: "Anda yakin?",
                    text: "Data akan disimpan!",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Simpan",
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

    <!-- ========== DELETE CONFIRMATION ========= -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const deleteButtons = document.querySelectorAll(".btn-delete");

        deleteButtons.forEach(btn => {
            btn.addEventListener("click", function () {
                let form = this.closest("form");

                Swal.fire({
                    title: "Hapus data?",
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus",
                    cancelButtonText: "Batal",
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
    </script>

    <!-- SUCCESS ALERT -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 1800,
            showConfirmButton: false,
        });
    </script>
    @endif

    <!-- ERROR ALERT -->
    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            showConfirmButton: true,
        });
    </script>
    @endif

</body>
</html>
