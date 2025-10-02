<!-- resources/views/user/info.blade.php -->
@extends('layouts.app')

@section('title', 'Login User - Store GS')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('Logo_Pertamina.png') }}" alt="Logo Pertamina" class="mx-auto w-48">
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-bold mb-2">HALO! SELAMAT DATANG DI PERTAMINA GAS NEGARA</h2>
            <p class="text-sm text-gray-600">Silahkan masukan nama dan fungsi anda</p>
        </div>

        <!-- User Info Form -->
        <form action="{{ route('user.info.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Masukan Nama Anda</label>
                <input type="text" name="borrower_name" required
                    value="{{ old('borrower_name') }}"
                    placeholder="Nama lengkap"
                    class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:bg-white">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">Fungsi</label>
                <select name="function" required
                    class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:bg-white appearance-none">
                    <option value="">Pilih fungsi</option>
                    <option value="ICT" {{ old('function') == 'ICT' ? 'selected' : '' }}>ICT</option>
                    <option value="Finance" {{ old('function') == 'Finance' ? 'selected' : '' }}>Finance</option>
                    <option value="Treasury" {{ old('function') == 'Treasury' ? 'selected' : '' }}>Treasury</option>
                    <option value="Human Capital" {{ old('function') == 'Human Capital' ? 'selected' : '' }}>Human Capital</option>
                </select>
            </div>

            <button type="submit" 
                class="w-full bg-blue-900 text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                Konfirm
            </button>

            <div class="text-center mt-6">
                <a href="{{ route('admin.login') }}" class="text-red-600 text-sm hover:underline">
                    Masuk sebagai admin
                </a>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
    <div class="flex items-center gap-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
</div>
<script>
    setTimeout(() => {
        document.querySelector('.animate-fade-in').remove();
    }, 3000);
</script>
@endif

@endsection