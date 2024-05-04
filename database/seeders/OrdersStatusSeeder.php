<?php

namespace Database\Seeders;

use App\Models\OrderStatuses;
use Illuminate\Database\Seeder;

class OrdersStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $statuses = [
            'Очікується',
            'Обробляється',
            'Відправлено',
            'Доставлено',
            'Скасовано',
            'Виконано',
            'Повернено',
        ];

        foreach ($statuses as $status) {
            OrderStatuses::create([
                'status' => $status,
            ]);
        }
    }
}
