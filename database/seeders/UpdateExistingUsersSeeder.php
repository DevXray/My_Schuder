<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UpdateExistingUsersSeeder extends Seeder
{
    public function run(): void
    {
        $dosenRole = Role::where('name', 'dosen')->first();
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();

        // Update existing dosens
        $dosens = Dosen::whereNull('user_id')->get();
        foreach ($dosens as $dosen) {
            // Create or find user
            $user = User::firstOrCreate(
                ['email' => $dosen->email],
                [
                    'name' => $dosen->nama,
                    'password' => $dosen->password ?? Hash::make('rahasia123'),
                    'role_id' => $dosenRole->id,
                ]
            );

            // Link to dosen
            $dosen->update(['user_id' => $user->id]);
        }

        // Update existing mahasiswas
        $mahasiswas = Mahasiswa::whereNull('user_id')->get();
        foreach ($mahasiswas as $mahasiswa) {
            // Create or find user
            $user = User::firstOrCreate(
                ['email' => $mahasiswa->email],
                [
                    'name' => $mahasiswa->nama,
                    'password' => $mahasiswa->password ?? Hash::make('mahasiswa123'),
                    'role_id' => $mahasiswaRole->id,
                ]
            );

            // Link to mahasiswa
            $mahasiswa->update(['user_id' => $user->id]);
        }
    }
}
