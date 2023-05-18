<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Owner',
                'slug' => 'owner'
            ],
            [
                'name' => "Viewer",
                'slug' => 'viewer'
            ],
        ];

        Role::truncate();

        collect($data)->each(function($role) {
            Role::create($role);
        });
    }
}
