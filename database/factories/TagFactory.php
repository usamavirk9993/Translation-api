<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        $tagNames = [
            'mobile', 'desktop', 'web', 'admin', 'user', 'error', 'success', 'warning',
            'info', 'help', 'navigation', 'form', 'button', 'label', 'message', 'title',
            'header', 'footer', 'sidebar', 'modal', 'popup', 'notification', 'alert',
            'dashboard', 'settings', 'profile', 'authentication', 'authorization',
            'validation', 'api', 'frontend', 'backend', 'database', 'cache', 'queue',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($tagNames),
        ];
    }

    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'mobile',
        ]);
    }

    public function desktop(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'desktop',
        ]);
    }

    public function web(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'web',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'admin',
        ]);
    }

    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'user',
        ]);
    }
}
