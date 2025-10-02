<!-- resources/views/admin/items.blade.php -->
@extends('layouts.app')

@section('title', 'Stok Barang')

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
                class="block px-6 py-3 bg-blue-800">
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
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Stok Barang</h2>
            <button onclick="openAddModal()" 
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                + Tambah Barang
            </button>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3">Nama Barang</th>
                        <th class="text-left px-6 py-3">Total Stok</th>
                        <th class="text-left px-6 py-3">Stok Tersedia</th>
                        <th class="text-left px-6 py-3">Stok Tidak Tersedia</th>
                        <th class="text-left px-6 py-3">Paling Banyak Diambil</th>
                        <th class="text-left px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $item->name }}</td>
                        <td class="px-6 py-4">{{ $item->total_stock }}</td>
                        <td class="px-6 py-4">{{ $item->available_stock }}</td>
                        <td class="px-6 py-4">{{ $item->unavailable_stock }}</td>
                        <td class="px-6 py-4">{{ $item->most_borrowed }}</td>
                        <td class="px-6 py-4">
                            <button onclick="openStockModal({{ $item->id }}, '{{ $item->name }}')" 
                                class="bg-green-600 text-white px-4 py-1 rounded text-sm hover:bg-green-700">
                                Tambah Stok
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-xl font-bold mb-4">Tambah Barang Baru</h3>
        <form action="{{ route('admin.items.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Nama Barang</label>
                <input type="text" name="name" required
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Jumlah Stok</label>
                <input type="number" name="stock" min="0" required
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeAddModal()"
                    class="flex-1 px-4 py-2 border rounded hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Update Stock Modal -->
<div id="stockModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-xl font-bold mb-4">Tambah Stok</h3>
        <p class="mb-4">Barang: <span id="itemName" class="font-semibold"></span></p>
        <form id="stockForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Jumlah Stok Ditambahkan</label>
                <input type="number" name="stock" min="1" required
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeStockModal()"
                    class="flex-1 px-4 py-2 border rounded hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function openStockModal(itemId, itemName) {
    document.getElementById('itemName').textContent = itemName;
    document.getElementById('stockForm').action = `/admin/items/${itemId}/stock`;
    document.getElementById('stockModal').classList.remove('hidden');
}

function closeStockModal() {
    document.getElementById('stockModal').classList.add('hidden');
}
</script>
@endsection