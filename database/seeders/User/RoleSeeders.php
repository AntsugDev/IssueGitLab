<?php

namespace Database\Seeders\User;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            "name" => "root",
            "label" =>"Root"
        ]);
        Role::create([
            "name" => "sso",
            "label" =>"SSO"
        ]);
    }
}
