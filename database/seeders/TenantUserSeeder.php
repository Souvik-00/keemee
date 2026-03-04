<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Role;
use App\Models\Site;
use App\Models\SiteAllowanceConfig;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantUserSeeder extends Seeder
{
    public function run(): void
    {
        $subscriber = Subscriber::query()->where('code', 'ALPHA_SEC')->first();

        if (! $subscriber) {
            return;
        }

        $roleMap = Role::query()
            ->whereIn('slug', ['manager', 'security_guard_manager', 'security_guard', 'customer'])
            ->get()
            ->keyBy('slug');

        $manager = User::query()->updateOrCreate(
            ['email' => 'manager@alpha.local'],
            [
                'name' => 'Alpha Manager',
                'username' => 'alpha_manager',
                'password' => 'Manager@12345',
                'subscriber_id' => $subscriber->id,
                'status' => 'active',
            ]
        );

        $guardManager = User::query()->updateOrCreate(
            ['email' => 'guardmanager@alpha.local'],
            [
                'name' => 'Alpha Guard Manager',
                'username' => 'alpha_guard_manager',
                'password' => 'GuardMgr@12345',
                'subscriber_id' => $subscriber->id,
                'status' => 'active',
            ]
        );

        $guard = User::query()->updateOrCreate(
            ['email' => 'guard@alpha.local'],
            [
                'name' => 'Alpha Guard',
                'username' => 'alpha_guard',
                'password' => 'Guard@12345',
                'subscriber_id' => $subscriber->id,
                'status' => 'active',
            ]
        );

        $customerUser = User::query()->updateOrCreate(
            ['email' => 'customer@alpha.local'],
            [
                'name' => 'Alpha Customer',
                'username' => 'alpha_customer',
                'password' => 'Customer@12345',
                'subscriber_id' => $subscriber->id,
                'status' => 'active',
            ]
        );

        if (isset($roleMap['manager'])) {
            $manager->roles()->syncWithoutDetaching([$roleMap['manager']->id]);
        }

        if (isset($roleMap['security_guard_manager'])) {
            $guardManager->roles()->syncWithoutDetaching([$roleMap['security_guard_manager']->id]);
        }

        if (isset($roleMap['security_guard'])) {
            $guard->roles()->syncWithoutDetaching([$roleMap['security_guard']->id]);
        }

        if (isset($roleMap['customer'])) {
            $customerUser->roles()->syncWithoutDetaching([$roleMap['customer']->id]);
        }

        $customer = Customer::query()->updateOrCreate(
            ['subscriber_id' => $subscriber->id, 'code' => 'ALPHA_CUST_001'],
            [
                'user_id' => $customerUser->id,
                'name' => 'Alpha Industrial Client',
                'billing_address' => 'Alpha Tech Park, Bengaluru',
                'status' => 'active',
            ]
        );

        $site = Site::query()->updateOrCreate(
            ['subscriber_id' => $subscriber->id, 'site_code' => 'ALPHA_SITE_01'],
            [
                'customer_id' => $customer->id,
                'name' => 'Alpha Main Campus',
                'address' => 'Gate 1, Alpha Tech Park',
                'status' => 'active',
            ]
        );

        SiteAllowanceConfig::query()->updateOrCreate(
            [
                'subscriber_id' => $subscriber->id,
                'site_id' => $site->id,
                'customer_id' => $customer->id,
                'allowance_type' => 'food',
                'effective_from' => now()->startOfMonth()->toDateString(),
            ],
            [
                'amount' => 1200,
                'effective_to' => null,
                'is_active' => true,
            ]
        );

        if ($this->command) {
            $this->command->newLine();
            $this->command->info('Tenant portal users seeded (ALPHA_SEC):');
            $this->command->line('  Manager: manager@alpha.local / Manager@12345');
            $this->command->line('  Guard Manager: guardmanager@alpha.local / GuardMgr@12345');
            $this->command->line('  Guard: guard@alpha.local / Guard@12345');
            $this->command->line('  Customer: customer@alpha.local / Customer@12345');
            $this->command->newLine();
        }
    }
}
