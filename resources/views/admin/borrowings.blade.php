<!-- resources/views/admin/borrowings.blade.php -->
@extends('layouts.app')

@section('title', 'Transaksi Peminjaman')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-blue-900 text-white">
        <div class="p-6">
            <h1 class="text-xl font-bold">DASHBOARD</h1>
        </div>
        <nav class="mt-6">
            <a href="{{ route('admin.dashboard') }}" 
                class="block px-6 py-3 hover:bg-blue-800">
                DASHBOARD
            </a>
            <a href="{{ route('admin.items') }}" 
                class="block px-6 py-3 hover:bg-blue-800">
                Stok Barang
            </a>
            <a href="{{ route('admin.borrowings') }}" 
                class="block px-6 py-3 bg-blue-800">
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
        <h2 class="text-2xl font-bold mb-6">Riwayat Transaksi</h2>

        <!-- Search & Filter -->
        <div class="mb-6 flex gap-4">
            <input type="text" id="searchInput" placeholder="Cari nama atau barang..."
                class="flex-1 px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
            <select id="statusFilter" class="px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
                <option value="">Semua Status</option>
                <option value="dipinjam">Dipinjam</option>
                <option value="dikembalikan">Dikembalikan</option>
            </select>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3">ID</th>
                        <th class="text-left px-6 py-3">Nama Peminjam</th>
                        <th class="text-left px-6 py-3">Fungsi</th>
                        <th class="text-left px-6 py-3">Barang</th>
                        <th class="text-left px-6 py-3">Jumlah</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Tanggal Pinjam</th>
                        <th class="text-left px-6 py-3">Tanggal Kembali</th>
                        <th class="text-left px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="borrowingTable">
                    @foreach($borrowings as $borrowing)
                    <tr class="border-b hover:bg-gray-50 borrowing-row" 
                        data-name="{{ $borrowing->borrower_name }}" 
                        data-item="{{ $borrowing->item->name }}"
                        data-status="{{ $borrowing->status }}">
                        <td class="px-6 py-4">{{ $borrowing->id }}</td>
                        <td class="px-6 py-4">{{ $borrowing->borrower_name }}</td>
                        <td class="px-6 py-4">{{ $borrowing->function }}</td>
                        <td class="px-6 py-4">{{ $borrowing->item->name }}</td>
                        <td class="px-6 py-4">{{ $borrowing->quantity }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded text-sm font-semibold
                                {{ $borrowing->status === 'dipinjam' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">
                                {{ ucfirst($borrowing->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $borrowing->borrowed_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            {{ $borrowing->returned_at ? $borrowing->returned_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($borrowing->status === 'dipinjam')
                            <form action="{{ route('admin.borrowings.return', $borrowing->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-1 rounded text-sm hover:bg-blue-700">
                                    Kembalikan
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400 text-sm">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const rows = document.querySelectorAll('.borrowing-row');

function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const statusValue = statusFilter.value;

    rows.forEach(row => {
        const name = row.getAttribute('data-name').toLowerCase();
        const item = row.getAttribute('data-item').toLowerCase();
        const status = row.getAttribute('data-status');

        const matchSearch = name.includes(searchTerm) || item.includes(searchTerm);
        const matchStatus = !statusValue || status === statusValue;

        if (matchSearch && matchStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

searchInput.addEventListener('input', filterTable);
statusFilter.addEventListener('change', filterTable);
</script>