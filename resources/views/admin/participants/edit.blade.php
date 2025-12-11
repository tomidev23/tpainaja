@extends('admin.layouts.sidebar')

@section('title', 'Edit Peserta')

@section('content')
<div class="p-6">
    <div class="max-w-xl mx-auto bg-white shadow rounded-lg p-6">

        <h2 class="text-2xl font-semibold mb-6">Edit Peserta</h2>

        <form method="POST" action="{{ route('admin.participants.update', $participant->id) }}">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div class="mb-5">
                <label class="block mb-1 font-medium text-gray-700">Nama</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $participant->name) }}"
                    class="w-full border-gray-300 p-2 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                >
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <label class="block mb-1 font-medium text-gray-700">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email', $participant->email) }}"
                    class="w-full border-gray-300 p-2 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                >
            </div>

            <div class="flex items-center">
              <button type="button" class="btn-save bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
    Update
</button>


                <a 
                    href="{{ route('admin.participants.index') }}" 
                    class="ml-4 text-gray-600 hover:text-gray-800"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
