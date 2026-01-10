<?php

use App\Core\Application\Commands\DeleteUserCommand;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\UserId;

describe('DeleteUserCommand', function () {
    describe('execute()', function () {
        it('deletes user by ID', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('deleteById')
                ->withArgs(function (UserId $id) use ($userId) {
                    return $id->value() === $userId->value();
                })
                ->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new DeleteUserCommand($mockUow);

            $command->execute($userId);
        });

        it('calls repository deleteById exactly once', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('a1b2c3d4-e5f6-7890-abcd-ef1234567890');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('deleteById')
                ->withArgs(function (UserId $id) use ($userId) {
                    return $id->value() === $userId->value();
                })
                ->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new DeleteUserCommand($mockUow);

            $command->execute($userId);
        });

        it('passes correct user ID to repository', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('deleteById')
                ->withArgs(function (UserId $id) use ($userId) {
                    return $id->value() === $userId->value();
                })
                ->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new DeleteUserCommand($mockUow);

            $command->execute($userId);
        });

        it('rolls back transaction on failure', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('deleteById')
                ->withArgs(function (UserId $id) use ($userId) {
                    return $id->value() === $userId->value();
                })
                ->once()
                ->andThrow(new Exception('Database error'));
            $mockUow->shouldReceive('rollback')->once();

            $command = new DeleteUserCommand($mockUow);

            expect(fn() => $command->execute($userId))->toThrow(Exception::class);
        });
    });
});

