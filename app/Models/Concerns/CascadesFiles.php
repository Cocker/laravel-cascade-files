<?php

declare(strict_types=1);

namespace App\Models\Concerns;

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
        if (! count($cascadableAttributes = $model->cascadableFileAttributes())) {
            return;
        }

        $model->orphanedFilePaths = collect($model->attributesToArray())
            ->filter(function (mixed $value, string $key) use ($model, $cascadableAttributes) {
                return in_array($key, $cascadableAttributes, true) && $model->isDirty($key);
            })
            ->map(fn (mixed $value, string $key) => $model->getOriginal($key))
            ->filter()
            ->values()
            ->toArray();
    }

    protected static function storeFilesOnDeleting(Model $model): void
    {
        if (! count($cascadableAttributes = $model->cascadableFileAttributes())) {
            return;
        }

        $model->orphanedFilePaths = collect($model->attributesToArray())
            ->filter(function (mixed $value, string $key) use ($cascadableAttributes) {
                return in_array($key, $cascadableAttributes, true) && ! is_null($value);
            })
            ->values()
            ->toArray();
    }

    protected static function cascadeFiles(Model $model): void
    {
        if (! count($filePaths = $model->getOrphanedFilePaths())) {
            return;
        }

        Storage::delete($filePaths);
    }
}
