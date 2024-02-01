<?php

use App\Models\Concerns\CascadesFiles;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

arch('traits are all set up')
    ->expect([User::class, Post::class])->toUse(CascadesFiles::class)
    ->and(Post::class)->toUse(SoftDeletes::class);

arch('debug functions are not used')
    ->expect(['dd', 'dump'])
    ->not->toBeUsed();

arch('strict types mode is used')
    ->expect('App')
    ->toUseStrictTypes();

