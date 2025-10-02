<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Item;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Create Sample Items
        $items = [
            ['name' => 'Kertas HVS A4', 'stock' => 500],        
            ['name' => 'Pulpen Tinta Hitam', 'stock' => 150],
            ['name' => 'Tinta Printer Hitam', 'stock' => 20],
            ['name' => 'Sticky Notes', 'stock' => 100],
            ['name' => 'Spidol Papan Tulis', 'stock' => 40],
            ['name' => 'Isi Staples No. 10', 'stock' => 80],
            ['name' => 'Map Plastik', 'stock' => 70],
            ['name' => 'Amplop Coklat', 'stock' => 200],
            ['name' => 'Penghapus Papan Tulis', 'stock' => 30],    
            ['name' => 'Baterai AA', 'stock' => 60],
        ];

        foreach ($items as $item) {
            Item::create([
                'name' => $item['name'],
                'stock' => $item['stock'],
                'total_stock' => $item['stock'],
                'available_stock' => $item['stock'],
                'unavailable_stock' => 0,
                'most_borrowed' => 0
            ]);
        }
    }
}