<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'zaroor',
            'email' => 'zaroor@gmail.com',
            'password' => bcrypt('123456'),
        ]);
    }
}
