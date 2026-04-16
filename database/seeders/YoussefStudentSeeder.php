<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class YoussefStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $avatars = Config::get('profile_avatars.items', []);
        $defaultAvatar = reset($avatars) ?: 'public/demo/user/student.jpg';

        $user = User::updateOrCreate(
            ['email' => 'youssef@student.com'],
            [
                'name' => 'Youssef',
                'username' => 'youssef',
                'role_id' => 3,
                'image' => $defaultAvatar,
                'headline' => 'Student',
                'phone' => '01000000000',
                'balance' => 0,
                'about' => 'Student account for testing profile and avatar flows.',
                'short_details' => 'Test student account',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'referral' => Str::random(10),
                'currency_id' => 112,
                'email_verify' => 1,
            ]
        );

        if (function_exists('applyDefaultRoleToUser')) {
            applyDefaultRoleToUser($user);
        }
    }
}
