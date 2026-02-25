<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Lavar los platos', 'points' => 2],
            ['name' => 'Lavar el auto', 'points' => 5],
            ['name' => 'Poner la mesa', 'points' => 1],
            ['name' => 'Ducharse', 'points' => 1],
            ['name' => 'Lavarse los dientes', 'points' => 1],
            ['name' => 'No ducharse a tiempo', 'points' => -2],
            ['name' => 'No lavarse los dientes', 'points' => -1],
            ['name' => 'Habitación desordenada', 'points' => -2],
            ['name' => 'Responder mal', 'points' => -3],
        ];

        foreach ($defaults as $action) {
            Action::firstOrCreate(
                [
                    'name' => $action['name'],
                    'user_id' => null,
                ],
                ['points' => $action['points']]
            );
        }
    }
}
