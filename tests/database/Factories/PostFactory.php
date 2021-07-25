<?php

namespace Binarcode\LaravelMailator\Tests\database\Factories;

use Binarcode\LaravelMailator\Tests\Fixtures\Post;
use Binarcode\LaravelMailator\Tests\Fixtures\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'user_id' => User::factory(),
        ];
    }

    public static function one(array $attributes = []): Post
    {
        return app(static::class)->create($attributes);
    }
}
