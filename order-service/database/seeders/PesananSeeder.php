<?php

     namespace Database\Seeders;

     use App\Models\Pesanan;
     use Illuminate\Database\Seeder;

     class PesananSeeder extends Seeder
     {
         public function run(): void
         {
             Pesanan::create([
                 'pelanggan_id' => 1,
                 'menu' => 'Nasi Goreng',
                 'jumlah' => 2,
                 'total_harga' => 50000,
                 'status' => 'menunggu'
             ]);
             Pesanan::create([
                 'pelanggan_id' => 2,
                 'menu' => 'Ayam Bakar',
                 'jumlah' => 1,
                 'total_harga' => 30000,
                 'status' => 'selesai'
             ]);
         }
     }
