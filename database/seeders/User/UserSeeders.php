<?php

namespace Database\Seeders\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleId = Role::getRoot();
        User::create([
            'name' => "root",
            'email' => "root@root.it",
            'password' => "root@123",
            'role_id' =>$roleId
        ]);
    }
}
