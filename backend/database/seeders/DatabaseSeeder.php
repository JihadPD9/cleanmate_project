<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Utama
        User::create([
            'name'     => 'Administrator Piket',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // 2. Data Siswa (Dipisah per akun, email & password bisa beda-beda)
        $siswaData = [
            [
                'name'     => 'Ahmad Fadilah',
                'email'    => 'ahmad@gmail.com',
                'password' => 'ahmad123',
            ],
            [
                'name'     => 'Aliva Dian Nugraha',
                'email'    => 'aliva@gmail.com',
                'password' => 'aliva123',
            ],
            [
                'name'     => 'Anggia Rahmania',
                'email'    => 'anggia@gmail.com',
                'password' => 'anggia123',
            ],
            [
                'name'     => 'Ani Nur Rahayu',
                'email'    => 'ani@gmail.com',
                'password' => 'ani123',
            ],
            [
                'name'     => 'Dikri Nur Rohmat',
                'email'    => 'dikri@gmail.com',
                'password' => 'dikri123',
            ],
            [
                'name'     => 'Fakhri Ibnu Nabil',
                'email'    => 'fakhri@gmail.com',
                'password' => 'fakhri123',
            ],
            [
                'name'     => 'Fatahillah Akbar',
                'email'    => 'fatahillah@gmail.com',
                'password' => 'fatahillah123',
            ],
            [
                'name'     => 'Feri Ramdani',
                'email'    => 'feri@gmail.com',
                'password' => 'feri123',
            ],
            [
                'name'     => 'Ilman Abidullah',
                'email'    => 'ilman@gmail.com',
                'password' => 'ilman123',
            ],
            [
                'name'     => 'M.Jauf',
                'email'    => 'jauf@gmail.com',
                'password' => 'jauf123',
            ],
            [
                'name'     => 'Marsha Bara Suwarna',
                'email'    => 'marsha@gmail.com',
                'password' => 'marsha123',
            ],
            [
                'name'     => 'Muhamad Ilham',
                'email'    => 'ilham@gmail.com',
                'password' => 'ilham123',
            ],
            [
                'name'     => 'Muhamad Reza Aditia',
                'email'    => 'reza@gmail.com',
                'password' => 'reza123',
            ],
            [
                'name'     => 'Muhammad Jihad Putra Drajat',
                'email'    => 'jihad@gmail.com',
                'password' => 'jihad123',
            ],
            [
                'name'     => 'Muhammad Radiedtya Pratama',
                'email'    => 'radit@gmail.com',
                'password' => 'radit123',
            ],
            [
                'name'     => 'Rakha Alfarizqi Zahir',
                'email'    => 'rakha@gmail.com',
                'password' => 'rakha123',
            ],
            [
                'name'     => 'Rava Andrea',
                'email'    => 'rava@gmail.com',
                'password' => 'rava123',
            ],
            [
                'name'     => 'Rehan Ramadhan',
                'email'    => 'rehan@gmail.com',
                'password' => 'rehan123',
            ],
            [
                'name'     => 'Reihan Azka Vahlepy',
                'email'    => 'reihan@gmail.com',
                'password' => 'reihan123',
            ],
            [
                'name'     => 'Riska Aulia Sakinah',
                'email'    => 'riska@gmail.com',
                'password' => 'riska123',
            ],
            [
                'name'     => 'Rudy Gunawan',
                'email'    => 'rudy@gmail.com',
                'password' => 'rudy123',
            ],
            [
                'name'     => 'Salwa Khaerunnisa',
                'email'    => 'salwa@gmail.com',
                'password' => 'salwa123',
            ],
            [
                'name'     => 'Siti Nurfadilla Hasanah',
                'email'    => 'siti@gmail.com',
                'password' => 'siti123',
            ],
            [
                'name'     => 'Teguh Firmansyah',
                'email'    => 'teguh@gmail.com',
                'password' => 'teguh123',
            ],
            [
                'name'     => 'Zaskia Ramadhani Putri',
                'email'    => 'zaskia@gmail.com',
                'password' => 'zaskia123',
            ],
        ];

        // 3. Loop Insert Akun Siswa
        foreach ($siswaData as $siswa) {
            User::create([
                'name'     => $siswa['name'],
                'email'    => $siswa['email'],
                'password' => Hash::make($siswa['password']), // Otomatis mengenkripsi password masing-masing
                'role'     => 'siswa',
            ]);
        }
    }
}