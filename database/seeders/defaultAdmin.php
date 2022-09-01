<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class defaultAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where("email", "admin@admin.com")->first();

        if (!$user) {
            $user = new User();
            $user->email  = "admin@admin.com";
            $user->first_name = "Admin";
            $user->last_name = "Smoke";
            $user->user_name = "admin_smoke";
            $user->password = bcrypt('123456789');
            $user->gender = 1;
            $user->role = 'admin';
            $user->save();
        }
    }
}
