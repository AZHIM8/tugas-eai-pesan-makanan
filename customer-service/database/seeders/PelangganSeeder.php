<?php

     namespace Database\Seeders;

     use App\Models\Pelanggan;
     use Illuminate\Database\Seeder;

     class PelangganSeeder extends Seeder
     {
         public function run(): void
         {
             Pelanggan::create([
                 'nama' => 'Budi Santoso',
                 'alamat' => 'Jl. Mawar No. 10',
                 'telepon' => '081234567890'
             ]);
             Pelanggan::create([
                 'nama' => 'Siti Aminah',
                 'alamat' => 'Jl. Melati No. 5',
                 'telepon' => '082345678901'
             ]);
         }
     }
