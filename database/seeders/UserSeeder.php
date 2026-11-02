<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }
        foreach ([['admin@oceanquiz.local', 'Quản trị viên', 'admin'], ['user@oceanquiz.local', 'Nhà thám hiểm', 'user']] as [$email, $name, $role]) {
            if (! User::where('email', $email)->exists()) {
                $user = new User(['name' => $name, 'email' => $email, 'password' => Hash::make('OceanQuiz@123')]);
                $user->role = $role;
                $user->save();
            }
        }
    }
}
