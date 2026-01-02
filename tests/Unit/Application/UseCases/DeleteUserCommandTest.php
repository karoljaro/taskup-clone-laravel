<?php

use App\Core\Application\Commands\DeleteUserCommand;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\UserId;

describe('DeleteUserCommand', function () {
    describe('execute()', function () {
        it('deletes user by ID', function () {
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUserRepo->shouldReceive('deleteById')
                ->withArgs(function (UserId $id) use ($userId) {
                    return $id->value() === $userId->value();
                })
                ->once();

            $command = new DeleteUserCommand($mockUserRepo);

            $command->execute($userId);
        });

        it('calls repository deleteById exactly once', function () {
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('a1b2c3d4-e5f6-7890-abcd-ef1234567890');

            $mockUserRepo->shouldReceive('deleteById')
                ->withArgs(function (UserId $id) use ($userId) {
                    return $id->value() === $userId->value();
                })
                ->once();

            $command = new DeleteUserCommand($mockUserRepo);

            $command->execute($userId);
        });

        it('passes correct user ID to repository', function () {
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUserRepo->shouldReceive('deleteById')
                ->withArgs(function (UserId $id) use ($userId) {
                    return $id->value() === $userId->value();
                })
                ->once();

            $command = new DeleteUserCommand($mockUserRepo);

            $command->execute($userId);
        });
    });
});

