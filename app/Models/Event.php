<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\CascadesFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, CascadesFiles;

    protected $fillable = [
        'title',
        'preview_img',
        'content',
    ];
}
