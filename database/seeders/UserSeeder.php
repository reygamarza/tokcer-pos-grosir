<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Owner', 'Finance', 'Gudang', 'Kasir', 'Pelanggan'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $users = [
            ['name' => 'Kang Aden (Owner)', 'username' => 'owner', 'email' => 'owner@tokcer.test', 'role' => 'Owner'],
            ['name' => 'Staf Keuangan', 'username' => 'finance', 'email' => 'finance@tokcer.test', 'role' => 'Finance'],
            ['name' => 'Staf Gudang', 'username' => 'gudang', 'email' => 'gudang@tokcer.test', 'role' => 'Gudang'],
            ['name' => 'Kasir Utama', 'username' => 'kasir', 'email' => 'kasir@tokcer.test', 'role' => 'Kasir'],
            ['name' => 'Warung Bu Siti', 'username' => 'pelanggan', 'email' => 'pelanggan@tokcer.test', 'role' => 'Pelanggan'],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['username' => $u['username']],
                [
                    'name' => $u['name'],
                    'email' => $u['email'],
                    'password' => Hash::make('password123'),
                    'status_akun' => 'aktif',
                ]
            );
            $user->syncRoles([$u['role']]);
        }
    }
}
