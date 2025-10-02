@extends('layouts.app')

@section('title', 'Pilih Barang - Store GS')

@section('content')
<div class="min-h-screen">
    <div class="bg-red-600 text-white py-6 px-4">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Selamat Datang di STORE GS</h1>
                <p class="text-sm mt-1">
                    <span class="font-semibold">{{ session('borrower_name') }}</span> - 
                    <span>{{ session('borrower_function') }}</span>
                </p>
                <p class="text-sm mt-2">Silahkan pilih barang yang akan diambil</p>
            </div>
            <a href="{{ route('user.reset') }}" 
               class="bg-white text-red-600 px-4 py-2 rounded hover:bg-gray-100">
               Ganti User
            </a>
        </div>
        <div class="container mx-auto mt-4">
            <input type="text" id="searchInput" placeholder="Cari kebutuhanmu di sini..." 
                   class="w-full md:w-96 px-4 py-2 rounded text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-300">
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div id="itemsGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($items as $item)
            <div class="bg-white rounded-lg shadow p-4 item-card flex flex-col">
                <div class="aspect-square bg-gray-100 rounded mb-3 flex items-center justify-center">
                    <span class="text-4xl">📦</span>
                </div>
                <h3 class="font-semibold text-lg mb-2 flex-grow">{{ $item->name }}</h3>
                <p class="text-sm text-gray-600 mb-3">Stok: <span class="font-bold">{{ $item->available_stock }}</span></p>
                <div class="flex items-center gap-2 mt-auto">
                    <button onclick="decreaseQty({{ $item->id }})" 
                            class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center hover:bg-gray-300 text-lg font-bold transition-colors">-</button>
                    <input type="number" id="qty-{{ $item->id }}" value="0" min="0" max="{{ $item->available_stock }}"
                           class="w-16 text-center border rounded py-2" readonly>
                    <button onclick="increaseQty({{ $item->id }}, {{ $item->available_stock }})" 
                            class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center hover:bg-gray-300 text-lg font-bold transition-colors">+</button>
                </div>
            </div>
            @endforeach
        </div>

        <button onclick="goToCart()" 
                class="fixed bottom-8 right-8 bg-red-600 text-white px-6 py-4 rounded-full shadow-lg hover:bg-red-700 flex items-center gap-2 transition-transform hover:scale-105">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="font-semibold">Lihat Keranjang (<span id="cartCount">0</span>)</span>
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
const currentUser = "{{ session('borrower_name', 'guest') }}_{{ session('borrower_function', 'guest') }}";
let cart = [];

function getCartKey() {
    return 'cart_' + currentUser;
}

function increaseQty(itemId, max) {
    const input = document.getElementById('qty-' + itemId);
    const currentQty = parseInt(input.value);
    
    if (currentQty < max) {
        input.value = currentQty + 1;
        updateCartFromInputs();
    } else {
        showToast('Stok tidak mencukupi!', 'error');
    }
}

function decreaseQty(itemId) {
    const input = document.getElementById('qty-' + itemId);
    const currentQty = parseInt(input.value);
    
    if (currentQty > 0) {
        input.value = currentQty - 1;
        updateCartFromInputs();
    }
}

function updateCartFromInputs() {
    cart = [];
    
    document.querySelectorAll('.item-card').forEach(card => {
        const input = card.querySelector('input[type="number"]');
        const itemId = parseInt(input.id.replace('qty-', ''));
        const qty = parseInt(input.value);
        const itemName = card.querySelector('h3').textContent;
        
        if (qty > 0) {
            cart.push({ id: itemId, name: itemName, quantity: qty });
        }
    });
    
    localStorage.setItem(getCartKey(), JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById('cartCount').textContent = count;
}

function goToCart() {
    if (cart.length === 0) {
        showToast('Keranjang masih kosong!', 'error');
        return;
    }
    window.location.href = '{{ route('user.cart') }}';
}

function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.toast-notification').forEach(t => t.remove());

    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const toast = document.createElement('div');
    toast.className = `toast-notification fixed top-5 right-5 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-down`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function loadCartToInputs() {
    const cartData = localStorage.getItem(getCartKey());
    if (cartData) {
        cart = JSON.parse(cartData);
        
        cart.forEach(item => {
            const input = document.getElementById('qty-' + item.id);
            if (input) {
                input.value = item.quantity;
            }
        });
        
        updateCartCount();
    }
}

// Load cart from localStorage on page load
loadCartToInputs();

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.item-card');
    
    items.forEach(item => {
        const itemName = item.querySelector('h3').textContent.toLowerCase();
        if (itemName.includes(searchTerm)) {
            item.style.display = 'flex'; // Use flex to maintain layout
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endsection