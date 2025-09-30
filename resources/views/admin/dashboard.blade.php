<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-blue-900 text-white">
        <div class="p-6">
            <h1 class="text-xl font-bold">DASHBOARD</h1>
        </div>
        <nav class="mt-6">
            <a href="{{ route('admin.dashboard') }}" 
                class="block px-6 py-3 bg-blue-800 hover:bg-blue-700">
                DASHBOARD
            </a>
            <a href="{{ route('admin.items') }}" 
                class="block px-6 py-3 hover:bg-blue-800">
                Stok Barang
            </a>
            <a href="{{ route('admin.borrowings') }}" 
                class="block px-6 py-3 hover:bg-blue-800">
                Transaksi
            </a>
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-6">
                @csrf
                <button type="submit" class="w-full text-left px-6 py-3 hover:bg-blue-800">
                    Log Out
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-gray-100 p-8">
        <h2 class="text-2xl font-bold mb-6">Total Transaksi:</h2>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-600 text-sm mb-2">Total Barang</h3>
                <p class="text-3xl font-bold">{{ $totalItems }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-600 text-sm mb-2">Stock akan habis</h3>
                <p class="text-3xl font-bold">{{ $availableStock }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-600 text-sm mb-2">Stock tidak tersedia</h3>
                <p class="text-3xl font-bold">{{ $unavailableStock }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-600 text-sm mb-2">Stock paling banyak</h3>
                <p class="text-3xl font-bold">{{ $mostBorrowed }}</p>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-lg">Barang Paling Banyak Diminati</h3>
                <button class="text-gray-600">▼</button>
            </div>
            <canvas id="chartCanvas" width="800" height="300"></canvas>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-lg mb-4">Transaksi Terakhir</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Nama</th>
                            <th class="text-left py-2">Fungsi</th>
                            <th class="text-left py-2">Barang</th>
                            <th class="text-left py-2">Jumlah</th>
                            <th class="text-left py-2">Status</th>
                            <th class="text-left py-2">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBorrowings as $borrowing)
                        <tr class="border-b">
                            <td class="py-3">{{ $borrowing->borrower_name }}</td>
                            <td class="py-3">{{ $borrowing->function }}</td>
                            <td class="py-3">{{ $borrowing->item->name }}</td>
                            <td class="py-3">{{ $borrowing->quantity }}</td>
                            <td class="py-3">
                                <span class="px-2 py-1 rounded text-sm {{ $borrowing->status === 'dipinjam' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">
                                    {{ $borrowing->status }}
                                </span>
                            </td>
                            <td class="py-3">{{ $borrowing->borrowed_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const canvas = document.getElementById('chartCanvas');
const ctx = canvas.getContext('2d');

const items = @json($items);
const data = items.map(item => ({
    name: item.name,
    total: item.total_stock,
    available: item.available_stock,
    unavailable: item.unavailable_stock
}));

const barWidth = 40;
const spacing = 20;
const startX = 50;
const maxHeight = 250;
const maxValue = Math.max(...data.map(d => Math.max(d.total, d.available, d.unavailable)));

data.forEach((item, index) => {
    const x = startX + (index * (barWidth * 3 + spacing));
    
    const totalHeight = (item.total / maxValue) * maxHeight;
    const availableHeight = (item.available / maxValue) * maxHeight;
    const unavailableHeight = (item.unavailable / maxValue) * maxHeight;
    
    ctx.fillStyle = '#6366f1';
    ctx.fillRect(x, 300 - totalHeight, barWidth, totalHeight);
    
    ctx.fillStyle = '#ec4899';
    ctx.fillRect(x + barWidth, 300 - availableHeight, barWidth, availableHeight);
    
    ctx.fillStyle = '#f97316';
    ctx.fillRect(x + barWidth * 2, 300 - unavailableHeight, barWidth, unavailableHeight);
});

ctx.fillStyle = '#000';
ctx.font = '12px Arial';
data.forEach((item, index) => {
    const x = startX + (index * (barWidth * 3 + spacing));
    ctx.fillText(item.name.substring(0, 10), x, 320);
});
</script>
@endsection