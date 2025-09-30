<!-- resources/views/admin/login.blade.php -->
@extends('layouts.app')

@section('title', 'Login Admin')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="flex justify-center items-center gap-2 mb-4">
                <div class="w-12 h-12 bg-blue-500 rounded"></div>
                <div class="w-8 h-8 bg-red-500 rounded"></div>
                <div class="w-8 h-8 bg-yellow-400 rounded"></div>
            </div>
            <h1 class="text-2xl font-bold text-blue-600">PERTAMINA</h1>
            <p class="text-red-600 font-semibold">GAS NEGARA</p>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-bold mb-2">HALO! SELAMAT DATANG DI PERTAMINA GAS NEGARA</h2>
            <p class="text-sm text-gray-600">Silahkan masukan nama dan fungsi anda</p>
        </div>

        <!-- Login Form -->
        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Username Admin</label>
                <input type="text" name="username" required
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>

            <button type="submit" 
                class="w-full bg-blue-900 text-white py-3 rounded font-semibold hover:bg-blue-800">
                Konfirm
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('user.index') }}" class="text-red-600 text-sm hover:underline">
                    Masuk sebagai user
                </a>
            </div>
        </form>
    </div>
</div>
@endsection