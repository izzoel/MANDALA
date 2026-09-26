<?php

namespace Database\Seeders;

use App\Models\Laboratorium;
use Illuminate\Database\Seeder;

class LaboratoriumSeeder extends Seeder
{
    private function randomPastelColor(): string
    {
        return sprintf(
            '#%02X%02X%02X',
            rand(200, 255),
            rand(200, 255),
            rand(200, 255)
        );
    }

    public function run(): void
    {
        $laboratoriums = [
            [
                'nama' => 'Batra & Kosmetika',
                'fakultas' => 'Farmasi',
                'gedung' => 'Gedung B',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Kimia Farmasi',
                'fakultas' => 'Farmasi',
                'gedung' => 'Lab Terpadu',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Teknologi Sediaan Farmasi',
                'fakultas' => 'Farmasi',
                'gedung' => 'Lab Terpadu',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Farmakologi',
                'fakultas' => 'Farmasi',
                'gedung' => 'Gedung C',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Sediaan Steril',
                'fakultas' => 'Farmasi',
                'gedung' => 'Gedung B',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Mikrobiologi',
                'fakultas' => 'Ilmu Kesehatan dan Sains Teknologi',
                'gedung' => 'Lab Terpadu',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Mikrobiologi',
                'fakultas' => 'Ilmu Kesehatan dan Sains Teknologi',
                'gedung' => 'Gedung C',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Kimia',
                'fakultas' => 'Ilmu Kesehatan dan Sains Teknologi',
                'gedung' => 'Gedung B',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Patologi Klinis',
                'fakultas' => 'Ilmu Kesehatan dan Sains Teknologi',
                'gedung' => 'Gedung C',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Flebotomi & Sitohistologi',
                'fakultas' => 'Ilmu Kesehatan dan Sains Teknologi',
                'gedung' => 'Gedung B',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'CBT',
                'fakultas' => 'Ilmu Kesehatan dan Sains Teknologi',
                'gedung' => 'Gedung C',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'OSCE',
                'fakultas' => 'Farmasi',
                'gedung' => 'Gedung B',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Gizi Kuliner & Ilmu Bahan Makanan',
                'fakultas' => 'Ilmu Kesehatan dan Sains Teknologi',
                'gedung' => 'Gedung C',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Penilaian Status Gizi',
                'fakultas' => 'Ilmu Kesehatan dan Sains Teknologi',
                'gedung' => 'Gedung B',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Mini Teaching Hospital',
                'fakultas' => 'Ilmu Kesehatan dan Sains Teknologi',
                'gedung' => 'Gedung C',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Peradilan Semu',
                'fakultas' => 'Ilmu Sosial dan Humaniora',
                'gedung' => 'Gedung B',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Microteacing',
                'fakultas' => 'Ilmu Sosial dan Humaniora',
                'gedung' => 'Gedung C',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Bahan Alam',
                'fakultas' => 'Farmasi',
                'gedung' => 'Lab Terpadu',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Kimia Farmasi',
                'fakultas' => 'Farmasi',
                'gedung' => 'Gedung C',
                'deskripsi' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus modi est neque porro expedita, quisquam officia! Molestiae cum eligendi doloribus ad provident eos quibusdam animi unde, at, autem quam deleniti.',
                'gambar' => 'laboratorium/profil/img.jpg',
                'id_laboran' => 1,
                'warna' => $this->randomPastelColor(),
            ],
        ];

        foreach ($laboratoriums as $laboratorium) {
            Laboratorium::updateOrCreate(
                ['nama' => $laboratorium['nama']],
                [
                    'fakultas' => $laboratorium['fakultas'],
                    'gedung' => $laboratorium['gedung'],
                    'deskripsi' => $laboratorium['deskripsi'],
                    'gambar' => $laboratorium['gambar'],
                    'id_laboran' => $laboratorium['id_laboran'],
                    'warna' => $laboratorium['warna'],
                ],
            );
        }
    }
}
