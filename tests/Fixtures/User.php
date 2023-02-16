<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\Models\Concerns\HasMailatorSchedulers;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory;
    use HasMailatorSchedulers;
    use MustVerifyEmail;


    protected $guarded = [];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
