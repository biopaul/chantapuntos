<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Child;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@puntoschanta.test'],
            [
                'name' => 'Padre Demo',
                'password' => Hash::make('password'),
            ]
        );

        $children = [
            ['name' => 'Isabella', 'icon' => 'heart', 'points' => 12],
            ['name' => 'Lucas', 'icon' => 'star', 'points' => 5],
        ];

        $actionsPositive = Action::whereNull('user_id')->where('points', '>', 0)->get();
        $actionsNegative = Action::whereNull('user_id')->where('points', '<', 0)->get();

        foreach ($children as $data) {
            $child = Child::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $data['name'],
                ],
                [
                    'icon' => $data['icon'],
                    'points' => 0,
                ]
            );

            if ($child->pointTransactions()->count() > 0) {
                continue;
            }

            $points = $data['points'];
            $acc = 0;
            while ($acc < $points && $actionsPositive->isNotEmpty()) {
                $action = $actionsPositive->random();
                $acc += $action->points;
                PointTransaction::create([
                    'child_id' => $child->id,
                    'action_id' => $action->id,
                    'points' => $action->points,
                    'type' => PointTransaction::TYPE_TASK,
                ]);
            }
            $child->update(['points' => $acc]);
        }

        $isabella = Child::where('user_id', $user->id)->where('name', 'Isabella')->first();
        if ($isabella && $isabella->pointTransactions()->where('type', PointTransaction::TYPE_REDEEM)->count() === 0) {
            PointTransaction::create([
                'child_id' => $isabella->id,
                'action_id' => null,
                'points' => -5,
                'type' => PointTransaction::TYPE_REDEEM,
                'description' => 'Llevar a Isabella al cumpleaños de Juanita',
            ]);
            $isabella->decrement('points', 5);
        }
    }
}
