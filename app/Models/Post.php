<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\CascadesFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes, CascadesFiles;

    protected $fillable = [
        'title',
        'text',
        'preview_img',
    ];

    public function cascadableFileAttributes(): array
    {
        return ['preview_img'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
