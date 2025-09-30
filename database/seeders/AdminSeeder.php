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
            ['name' => 'tiramisu coffee milk', 'stock' => 50],
            ['name' => 'choco creamy latte', 'stock' => 50],
            ['name' => 'Pumpkin Spice Latte', 'stock' => 30],
            ['name' => 'Sticky Notes', 'stock' => 100],
            ['name' => 'Penggaris', 'stock' => 25],
            ['name' => 'Spidol Warna', 'stock' => 40],
            ['name' => 'Box File', 'stock' => 20],
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