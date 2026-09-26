<?php

namespace Database\Seeders;

use App\Models\Laboran;
use Illuminate\Database\Seeder;

class LaboranSeeder extends Seeder
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
        $laborans = [
            [
                'nama' => 'Haryati, S.Farm', 'jabatan' => 'Laboran','warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Karlinda Aminoor Rahmah, A.Md. AK', 'jabatan' => 'Laboran', 'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Maya Herliana Sasmitha, S.ST', 'jabatan' => 'Laboran', 'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Muhammad Fathur Razzaak, A.Md.Gz', 'jabatan' => 'Laboran', 'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Nurrahmi Arny, A.Md. Farm', 'jabatan' => 'Laboran','warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'apt. Putri Indah Sayakti, M. Pharm. Sci.', 'jabatan' => 'Kepala Laboratorium', 'warna' => '#d1ffbd'
            ],
            [
                'nama' => 'Rahma Maulida, S.Tr.Kes', 'jabatan' => 'Laboran', 'warna' => $this->randomPastelColor(),
            ],
            [
                'nama' => 'Tia Fajar Safarina, S.Farm', 'jabatan' => 'Laboran', 'warna' => $this->randomPastelColor(),
            ],
        ];

        foreach ($laborans as $laboran) {
            Laboran::updateOrCreate(
                ['nama' => $laboran['nama']
                ],
                [
                    'jabatan' => $laboran['jabatan'],
                    'warna' => $laboran['warna'],
                ],
            );
        }
    }
}
