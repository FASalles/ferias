<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Criar permissões
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'view posts']);

        // Associar permissões às roles
        $adminRole->givePermissionTo('manage users');
        $userRole->givePermissionTo('view posts');

        // Atribuir role ao primeiro usuário
        $user = \App\Models\User::find(1);
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
