<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Gustavo Souza',
                'email' => 'tecnicowebmedicina@gmail.com',
                'password' => Hash::make('password'), // ajuste conforme necessário
            ],
            // adicione outros usuários aqui se necessário
        ];

        foreach ($users as $u) {
            // updateOrCreate evita erro de unique constraint ao tentar inserir email já existente
            User::updateOrCreate(
                ['email' => $u['email']],
                array_merge($u, [
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ])
            );
        }
    }
}
