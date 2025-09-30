<!-- resources/views/user/index.blade.php -->
@extends('layouts.app')

@section('title', 'Pilih Barang - Store GS')

@section('content')
<div class="min-h-screen">
    <!-- Header -->
    <div class="bg-red-600 text-white py-6 px-4">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">Selamat Datang di STORE GS</h1>
            <p class="text-sm">Silahkan pilih barang yang akan diambil</p>
            <div class="mt-4">
                <input type="text" id="searchInput" placeholder="pilih kebutuhan mu!" 
                    class="w-full md:w-96 px-4 py-2 rounded text-gray-800">
            </div>
        </div>
    </div>

    <!-- Items Grid -->
    <div class="container mx-auto px-4 py-8">
        <div id="itemsGrid" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($items as $item)
            <div class="bg-white rounded-lg shadow p-4 item-card">
                <div class="aspect-square bg-gray-200 rounded mb-3 flex items-center justify-center">
                    <span class="text-4xl">📦</span>
                </div>
                <h3 class="font-semibold text-lg mb-2">{{ $item->name }}</h3>
                <p class="text-sm text-gray-600 mb-3">stock: {{ $item->available_stock }}</p>
                <div class="flex items-center gap-2">
                    <button onclick="decreaseQty({{ $item->id }})" 
                        class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">-</button>
                    <input type="number" id="qty-{{ $item->id }}" value="1" min="1" max="{{ $item->available_stock }}"
                        class="w-12 text-center border rounded">
                    <button onclick="increaseQty({{ $item->id }}, {{ $item->available_stock }})" 
                        class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">+</button>
                    <button onclick="addToCart({{ $item->id }}, '{{ $item->name }}', {{ $item->available_stock }})"
                        class="ml-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Cart Button -->
        <button onclick="window.location.href='{{ route('user.cart') }}'" 
            class="fixed bottom-8 right-8 bg-red-600 text-white px-6 py-4 rounded-full shadow-lg hover:bg-red-700">
            <span class="font-semibold">Lihat Detail Barang (<span id="cartCount">0</span>)</span>
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
let cart = [];

function increaseQty(itemId, max) {
    const input = document.getElementById('qty-' + itemId);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQty(itemId) {
    const input = document.getElementById('qty-' + itemId);
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function addToCart(itemId, itemName, maxStock) {
    const qty = parseInt(document.getElementById('qty-' + itemId).value);
    
    if (qty > maxStock) {
        alert('Stok tidak mencukupi!');
        return;
    }

    const existingItem = cart.find(item => item.id === itemId);
    if (existingItem) {
        existingItem.quantity += qty;
    } else {
        cart.push({ id: itemId, name: itemName, quantity: qty });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    alert(`${itemName} ditambahkan ke keranjang!`);
}

function updateCartCount() {
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById('cartCount').textContent = count;
}

// Load cart from localStorage
if (localStorage.getItem('cart')) {
    cart = JSON.parse(localStorage.getItem('cart'));
    updateCartCount();
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.item-card');
    
    items.forEach(item => {
        const itemName = item.querySelector('h3').textContent.toLowerCase();
        if (itemName.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endsection