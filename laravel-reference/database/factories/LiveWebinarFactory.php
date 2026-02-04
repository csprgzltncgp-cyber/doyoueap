<?php

namespace Database\Factories;

use App\Models\LiveWebinar;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LiveWebinar>
 */
class LiveWebinarFactory extends Factory
{
    protected $model = LiveWebinar::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 week', '+1 week');

        return [
            'permission_id' => Permission::factory(),
            'user_id' => User::factory(),
            'topic' => $this->faker->sentence(4),
            'from' => $start,
            'to' => (clone $start)->modify('+1 hour'),
            'duration' => 60,
            'description' => $this->faker->paragraph(),
            'language_id' => 1,
            'zoom_meeting_id' => (string) $this->faker->numberBetween(100000000, 999999999),
            'zoom_meeting_uuid' => (string) Str::uuid(),
            'recording_status' => 'pending',
        ];
    }
}
