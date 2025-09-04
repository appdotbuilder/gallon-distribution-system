<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\GallonRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GallonRequest>
 */
class GallonRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $requestedAt = fake()->dateTimeBetween('-3 months', 'now');
        
        return [
            'employee_id' => Employee::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'status' => fake()->randomElement(['pending', 'approved', 'ready', 'completed']),
            'requested_at' => $requestedAt,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_at' => null,
            'ready_at' => null,
            'completed_at' => null,
            'approved_by' => null,
            'prepared_by' => null,
        ]);
    }

    /**
     * Indicate that the request is approved.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            $approvedAt = fake()->dateTimeBetween($attributes['requested_at'], 'now');
            
            return [
                'status' => 'approved',
                'approved_at' => $approvedAt,
                'approved_by' => User::factory()->state(['role' => 'admin_administrator']),
                'ready_at' => null,
                'completed_at' => null,
                'prepared_by' => null,
            ];
        });
    }

    /**
     * Indicate that the request is ready for pickup.
     */
    public function ready(): static
    {
        return $this->state(function (array $attributes) {
            $approvedAt = fake()->dateTimeBetween($attributes['requested_at'], 'now');
            $readyAt = fake()->dateTimeBetween($approvedAt, 'now');
            
            return [
                'status' => 'ready',
                'approved_at' => $approvedAt,
                'approved_by' => User::factory()->state(['role' => 'admin_administrator']),
                'ready_at' => $readyAt,
                'prepared_by' => User::factory()->state(['role' => 'admin_gudang']),
                'completed_at' => null,
            ];
        });
    }

    /**
     * Indicate that the request is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $approvedAt = fake()->dateTimeBetween($attributes['requested_at'], 'now');
            $readyAt = fake()->dateTimeBetween($approvedAt, 'now');
            $completedAt = fake()->dateTimeBetween($readyAt, 'now');
            
            return [
                'status' => 'completed',
                'approved_at' => $approvedAt,
                'approved_by' => User::factory()->state(['role' => 'admin_administrator']),
                'ready_at' => $readyAt,
                'prepared_by' => User::factory()->state(['role' => 'admin_gudang']),
                'completed_at' => $completedAt,
            ];
        });
    }
}