<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class CascadableFileAttributesNotSet extends Exception
{
    private function __construct(Model $model) {
        parent::__construct('Cascade files are not set for model ' . $model::class);
    }

    public static function forModel(Model $model): static
    {
        return new static($model);
    }
}
