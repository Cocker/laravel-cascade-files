<?php

declare(strict_types=1);

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

describe('on model update', function () {
    test('nothing is cascaded if file attribute was not updated', function () {
        Storage::spy();

        $user = User::factory()->create();

        $oldCompanyLogo = $user->company_logo;

        $user->update(['email' => $newEmail = 'new@mail.com']);

        expect($user->email)->toBe($newEmail)
            ->and($user->company_logo)->toBe($oldCompanyLogo)
            ->and($user->avatar)->toBeNull();

        Storage::shouldHaveNotReceived('delete');
    });

    test('file is cascaded if set to null', function () {
        Storage::spy();

        $user = User::factory()
            ->withAvatar($avatar = fake()->filePath())
            ->create();

        $user->update(['avatar' => null]);

        expect($user->avatar)->toBeNull();

        Storage::shouldHaveReceived('delete')->once()->with([$avatar]);
    });

    test('multiple files can be cascaded after update', function () {
        Storage::spy();

        $user = User::factory()
            ->withAvatar($oldAvatar = 'test1')
            ->withCompanyLogo($oldCompanyLogo = 'test2')
            ->create();

        $user->update([
            'avatar' => $newAvatar = fake()->filePath(),
            'company_logo' => $newCompanyLogo = fake()->filePath(),
        ]);

        expect($user->company_logo)->toBe($newCompanyLogo)
            ->and($user->avatar)->toBe($newAvatar);

        Storage::shouldHaveReceived('delete')->once()->with([$oldCompanyLogo, $oldAvatar]);
    });
});

describe('on model delete', function () {
    test('file is cascaded if model does not support soft deletes', function () {
        Storage::spy();

        $user = User::factory()
            ->withCompanyLogo($companyLogo = fake()->filePath())
            ->create();

        $user->delete();

        $this->assertModelMissing($user);

        Storage::shouldHaveReceived('delete')->once()->with([$companyLogo]);
    });

    test('nothing is cascaded if model gets soft deleted', function () {
        Storage::spy();

        $post = Post::factory()->create();

        $post->delete();

        $this->assertSoftDeleted($post);

        Storage::shouldHaveNotReceived('delete');
    });

    test('file is cascaded if model gets force deleted', function () {
        Storage::spy();

        $post = Post::factory()
            ->withPreviewImg($previewImg = fake()->filePath())
            ->create();

        $post->forceDelete();

        $this->assertModelMissing($post);

        Storage::shouldHaveReceived('delete')->once()->with([$previewImg]);
    });
});
