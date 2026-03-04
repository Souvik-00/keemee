<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class SubscriberSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = [
            ['name' => 'Alpha Security Services', 'code' => 'ALPHA_SEC', 'status' => 'active'],
            ['name' => 'Bravo Guard Solutions', 'code' => 'BRAVO_GUARD', 'status' => 'active'],
        ];

        foreach ($subscribers as $subscriber) {
            Subscriber::query()->updateOrCreate(
                ['code' => $subscriber['code']],
                $subscriber
            );
        }
    }
}
