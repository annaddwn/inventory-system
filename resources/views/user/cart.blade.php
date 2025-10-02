@extends('layouts.app')

@section('title', 'Konfirmasi Pilihan Barang')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-red-600 text-white py-6 px-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Pilihan Barang Anda</h1>
                <p class="text-sm mt-1">
                    <span class="font-semibold">{{ session('borrower_name') }}</span> - 
                    <span>{{ session('borrower_function') }}</span>
                </p>
            </div>
            <a href="{{ route('user.index') }}" class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                Kembali Memilih
            </a>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-5xl mx-auto">
            <form action="{{ route('user.borrow') }}" method="POST" id="borrowForm">
                @csrf
                <h2 class="text-xl font-bold text-gray-800 mb-4">Barang di Keranjang</h2>
                <div id="cartItems" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8"></div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="button" onclick="window.location.href='{{ route('user.index') }}'" class="flex-1 w-full px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-100 transition duration-300">
                        + Tambah Barang Lagi
                    </button>
                    <button type="submit" class="flex-1 w-full px-6 py-3 bg-amber-700 text-white font-bold rounded-lg hover:bg-amber-800 transition duration-300">
                        Konfirmasi Pengambilan
                    </button>
                </div>
                
                <div id="hidden-items-container"></div>
            </form>
        </div>
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

function loadCart() {
    const cartData = localStorage.getItem(getCartKey());
    cart = cartData ? JSON.parse(cartData) : [];
    displayCart();
}

function saveCart() {
    localStorage.setItem(getCartKey(), JSON.stringify(cart));
}

function showEmptyCartMessage() {
    document.getElementById('cartItems').innerHTML = `
        <div class="col-span-full text-center py-12 bg-white rounded-lg shadow">
            <p class="text-gray-500 text-lg">Keranjang Anda masih kosong.</p>
            <a href="{{ route('user.index') }}" class="text-red-600 hover:underline mt-2 inline-block font-semibold">
                Mulai pilih barang
            </a>
        </div>`;
    // Disable submit button if cart is empty
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
}

function displayCart() {
    const container = document.getElementById('cartItems');
    
    if (cart.length === 0) {
        showEmptyCartMessage();
        return;
    }

    // Enable submit button
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.disabled = false;
    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');

    container.innerHTML = cart.map(item => `
        <div class="bg-white rounded-lg shadow p-4 flex flex-col" data-item-id="${item.id}">
            <div class="aspect-square bg-gray-100 rounded mb-3 flex items-center justify-center"><span class="text-4xl">📦</span></div>
            <h3 class="font-semibold text-gray-800 mb-2 flex-grow">${item.name}</h3>
            <p class="text-sm text-gray-600 mb-3">Jumlah: <span class="font-bold text-red-600">${item.quantity}</span></p>
            
            <div class="flex items-center justify-end mt-auto">
                <button type="button" onclick="removeItem(${item.id})" class="text-red-500 hover:text-red-700" title="Hapus item">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>
    `).join('');
    
    // Langsung update form input karena semua item di cart dianggap terpilih
    updateFormInput();
}

function removeItem(itemId) {
    if (confirm('Anda yakin ingin menghapus barang ini dari keranjang?')) {
        cart = cart.filter(item => item.id !== itemId);
        saveCart();
        displayCart(); // displayCart akan otomatis memanggil updateFormInput
    }
}

function updateFormInput() {
    const hiddenContainer = document.getElementById('hidden-items-container');
    hiddenContainer.innerHTML = ''; 

    // Loop melalui SEMUA item di dalam 'cart' array
    cart.forEach((item, index) => {
        const itemIdInput = document.createElement('input');
        itemIdInput.type = 'hidden';
        itemIdInput.name = `items[${index}][item_id]`;
        itemIdInput.value = item.id;
        hiddenContainer.appendChild(itemIdInput);
        
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = `items[${index}][quantity]`;
        quantityInput.value = item.quantity;
        hiddenContainer.appendChild(quantityInput);
    });
}

document.getElementById('borrowForm').addEventListener('submit', function(e) {
    // Cek berdasarkan panjang array 'cart', bukan jumlah checkbox yang tercentang
    if (cart.length === 0) {
        e.preventDefault();
        alert('Keranjang kosong! Silakan pilih barang terlebih dahulu.');
        return;
    }
    if (!confirm(`Anda akan mengonfirmasi pengambilan ${cart.length} jenis barang. Lanjutkan?`)) {
        e.preventDefault();
        return;
    }
    setTimeout(() => localStorage.removeItem(getCartKey()), 500);
});

loadCart();
</script>
@endsection