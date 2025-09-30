@extends('layouts.app')

@section('title', 'Lihat Pilihan Anda')

@section('content')
<div class="min-h-screen">
    <div class="bg-red-600 text-white py-6 px-4">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">Lihat Pilihan Anda</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div id="cartItems" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8"></div>

            <form action="{{ route('user.borrow') }}" method="POST" class="bg-white rounded-lg shadow p-6">
                @csrf
                <input type="hidden" name="items" id="itemsInput">
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Nama Orang</label>
                    <input type="text" name="borrower_name" required
                           class="w-full px-4 py-2 border rounded focus:outline-none focus:border-red-500">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Fungsi</label>
                    <select name="function" required
                            class="w-full px-4 py-2 border rounded focus:outline-none focus:border-red-500">
                        <option value="">Pilih Fungsi</option>
                        <option value="Staff">Staff</option>
                        <option value="Manager">Manager</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Admin">Admin</option>
                        <option value="Teknisi">Teknisi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="window.location.href='{{ route('user.index') }}'"
                            class="flex-1 px-6 py-3 border border-gray-300 rounded hover:bg-gray-100">
                        + Tambah pilihan
                    </button>
                    <button type="submit" id="confirmBtn"
                            class="flex-1 px-6 py-3 bg-amber-700 text-white rounded hover:bg-amber-800">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let cart = [];

function loadCart() {
    if (localStorage.getItem('cart')) {
        cart = JSON.parse(localStorage.getItem('cart'));
        displayCart();
    } else {
        document.getElementById('cartItems').innerHTML = 
            '<p class="col-span-full text-center text-gray-500">Keranjang kosong</p>';
    }
}

function displayCart() {
    const container = document.getElementById('cartItems');
    
    if (cart.length === 0) {
        container.innerHTML = '<p class="col-span-full text-center text-gray-500">Keranjang kosong</p>';
        return;
    }

    container.innerHTML = cart.map(item => `
        <div class="bg-white rounded-lg shadow p-4" data-item-id="${item.id}">
            <div class="aspect-square bg-gray-200 rounded mb-3 flex items-center justify-center">
                <span class="text-4xl">📦</span>
            </div>
            <h3 class="font-semibold mb-2">${item.name}</h3>
            <p class="text-sm text-gray-600 mb-2">total: ${item.quantity}</p>
            <div class="flex items-center gap-2">
                <input type="checkbox" checked class="w-4 h-4 item-checkbox" data-item-id="${item.id}">
                <button onclick="removeItem(${item.id})" class="ml-auto text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');

    // Add event listeners to checkboxes
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateFormInput);
    });

    updateFormInput();
}

function removeItem(itemId) {
    cart = cart.filter(item => item.id !== itemId);
    localStorage.setItem('cart', JSON.stringify(cart));
    displayCart();
}

function updateFormInput() {
    // Get all checked items
    const checkedItems = [];
    document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
        const itemId = parseInt(checkbox.dataset.itemId);
        const item = cart.find(i => i.id === itemId);
        if (item) {
            checkedItems.push({
                item_id: item.id,
                quantity: item.quantity
            });
        }
    });
    
    // Create hidden inputs for form submission
    const form = document.querySelector('form');
    
    // Remove old hidden inputs
    form.querySelectorAll('input[name^="items["]').forEach(input => input.remove());
    
    // Add new hidden inputs
    checkedItems.forEach((item, index) => {
        const itemIdInput = document.createElement('input');
        itemIdInput.type = 'hidden';
        itemIdInput.name = `items[${index}][item_id]`;
        itemIdInput.value = item.item_id;
        form.appendChild(itemIdInput);
        
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = `items[${index}][quantity]`;
        quantityInput.value = item.quantity;
        form.appendChild(quantityInput);
    });
}

// Form submission handler
document.querySelector('form').addEventListener('submit', function(e) {
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    
    if (checkedItems.length === 0) {
        e.preventDefault();
        alert('Pilih minimal satu barang!');
        return false;
    }
    
    // Clear cart after successful submission
    setTimeout(() => {
        localStorage.removeItem('cart');
    }, 100);
});

loadCart();
</script>
@endsection