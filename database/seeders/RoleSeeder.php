<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Superadmin', 'Admin', 'User'];

        foreach ($roles as $slug) {
            Role::updateOrCreate(['slug' => $slug]);
        }
    }
}
