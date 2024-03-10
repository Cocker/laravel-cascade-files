<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Exceptions\CascadableFileAttributesNotSet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

trait CascadesFiles
{
    protected array $orphanedFilePaths = [];

    public function cascadableFileAttributes(): array
    {
        return [];
    }

    public function getOrphanedFilePaths(): array
    {
        return $this->orphanedFilePaths;
    }

    public function ensureCascadableFileAttributesAreSet(): void
    {
        if (empty($this->cascadableFileAttributes())) {
            throw CascadableFileAttributesNotSet::forModel($this);
        }
    }

    protected static function bootCascadesFiles(): void
    {
        $updatingClosure = static::storeFilesOnUpdating(...);
        $deletingClosure = static::storeFilesOnDeleting(...);
        $cascadeFilesClosure = static::cascadeFiles(...);

        static::updating($updatingClosure);
        static::updated($cascadeFilesClosure);

        if (collect(class_uses_recursive(static::class))->contains(SoftDeletes::class)) {
            static::forceDeleting($deletingClosure);
            static::forceDeleted($cascadeFilesClosure);
        } else {
            static::deleting($deletingClosure);
            static::deleted($cascadeFilesClosure);
        }
    }

    protected static function storeFilesOnUpdating(Model $model): void
    {
        $model->ensureCascadableFileAttributesAreSet();

        $model->orphanedFilePaths = collect($model->getDirty())
            ->only($model->cascadableFileAttributes())
            ->map(fn (mixed $value, string $key) => $model->getOriginal($key))
            ->filter()
            ->values()
            ->toArray();
    }

    protected static function storeFilesOnDeleting(Model $model): void
    {
        $model->ensureCascadableFileAttributesAreSet();

        $model->orphanedFilePaths = collect($model->attributesToArray())
            ->only($model->cascadableFileAttributes())
            ->filter()
            ->values()
            ->toArray();
    }

    protected static function cascadeFiles(Model $model): void
    {
        $filePaths = $model->getOrphanedFilePaths();

        if (empty($filePaths)) {
            return;
        }

        Storage::delete($filePaths);
    }
}
