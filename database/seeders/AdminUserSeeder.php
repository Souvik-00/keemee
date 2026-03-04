<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = 'Admin@12345';

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@keemee.local'],
            [
                'name' => 'Leadership Admin',
                'username' => 'admin',
                'password' => $adminPassword,
                'subscriber_id' => null,
                'status' => 'active',
            ]
        );

        $leadershipRole = Role::query()->where('slug', 'leadership')->firstOrFail();
        $admin->roles()->syncWithoutDetaching([$leadershipRole->id]);

        if ($this->command) {
            $this->command->newLine();
            $this->command->info('Default Leadership admin credentials:');
            $this->command->line('  Login (email): admin@keemee.local');
            $this->command->line('  Login (username): admin');
            $this->command->line('  Password: Admin@12345');
            $this->command->newLine();
        }
    }
}
