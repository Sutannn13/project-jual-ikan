<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $produks = [
            [
                'nama'        => 'Ikan Nila Segar Jumbo',
                'kategori'    => 'Ikan Nila',
                'harga_per_kg'=> 28000,
                'stok'        => 150,
                'deskripsi'   => 'Ikan nila segar ukuran jumbo langsung dari kolam. Cocok untuk digoreng, dibakar, dan menu restoran.',
            ],
            [
                'nama'        => 'Ikan Nila Sangkal Premium',
                'kategori'    => 'Ikan Nila',
                'harga_per_kg'=> 32000,
                'stok'        => 80,
                'deskripsi'   => 'Ikan nila sangkal pilihan dengan daging tebal dan rasa gurih. Ukuran seragam dan sudah disortir.',
            ],
            [
                'nama'        => 'Ikan Nila Phyton Super',
                'kategori'    => 'Ikan Nila',
                'harga_per_kg'=> 35000,
                'stok'        => 50,
                'deskripsi'   => 'Ikan nila jenis phyton dengan ukuran ekstra besar. Ideal untuk usaha rumah makan dan warung.',
            ],
            [
                'nama'        => 'Ikan Mas Segar',
                'kategori'    => 'Ikan Mas',
                'harga_per_kg'=> 38000,
                'stok'        => 100,
                'deskripsi'   => 'Ikan mas segar berkualitas tinggi. Cocok untuk dibakar, digoreng, atau dijadikan ikan bakar khas Sunda.',
            ],
            [
                'nama'        => 'Ikan Mas Tombro',
                'kategori'    => 'Ikan Mas',
                'harga_per_kg'=> 42000,
                'stok'        => 60,
                'deskripsi'   => 'Mas tombro pilihan dengan bobot rata-rata 500g-1kg per ekor. Daging lembut dan minim duri.',
            ],
            [
                'nama'        => 'Ikan Mas Koki',
                'kategori'    => 'Ikan Mas',
                'harga_per_kg'=> 45000,
                'stok'        => 30,
                'deskripsi'   => 'Ikan mas koki premium yang siap masak. Ukuran besar dengan warna kuning keemasan.',
            ],
            [
                'nama'        => 'Ikan Nila Dumbo',
                'kategori'    => 'Ikan Nila',
                'harga_per_kg'=> 25000,
                'stok'        => 200,
                'deskripsi'   => 'Ikan nila dumbo ekonomis dengan harga terjangkau. Cocok untuk kebutuhan rumah tangga sehari-hari.',
            ],
            [
                'nama'        => 'Ikan Mas Lokal',
                'kategori'    => 'Ikan Mas',
                'harga_per_kg'=> 35000,
                'stok'        => 75,
                'deskripsi'   => 'Ikan mas lokal dari kolam tradisional. Rasa alami dan segar, tanpa pakan kimia.',
            ],
        ];

        foreach ($produks as $produk) {
            Produk::create($produk);
        }
    }
}
