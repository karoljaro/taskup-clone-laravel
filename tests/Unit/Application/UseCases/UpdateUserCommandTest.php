<?php

use App\Core\Application\Commands\UpdateUserCommand;
use App\Core\Application\DTOs\UpdateUserInputDTO;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Entities\User;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\UserId;

describe('UpdateUserCommand', function () {
    describe('execute()', function () {
        it('updates user username', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $user = User::create(
                $userId->value(),
                'oldusername',
                'test@example.com',
                'Password123!'
            );

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('findById')
                ->with($userId)
                ->once()
                ->andReturn($user);
            $mockUserRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new UpdateUserCommand($mockUow);
            $input = new UpdateUserInputDTO(username: 'newusername');

            $command->execute($userId, $input);

            expect($user->getUsername())->toBe('newusername');
        });

        it('updates user email', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $user = User::create(
                $userId->value(),
                'testuser',
                'old@example.com',
                'Password123!'
            );

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('findById')
                ->with($userId)
                ->once()
                ->andReturn($user);
            $mockUserRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new UpdateUserCommand($mockUow);
            $input = new UpdateUserInputDTO(email: 'new@example.com');

            $command->execute($userId, $input);

            expect($user->getEmail()->value())->toBe('new@example.com');
        });

        it('updates user password', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $user = User::create(
                $userId->value(),
                'testuser',
                'test@example.com',
                'OldPassword123!'
            );

            $oldPasswordValue = $user->getPassword()->value();

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('findById')
                ->with($userId)
                ->once()
                ->andReturn($user);
            $mockUserRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new UpdateUserCommand($mockUow);
            $input = new UpdateUserInputDTO(plainPassword: 'NewPassword456!');

            $command->execute($userId, $input);

            expect($user->getPassword()->value())->not()->toBe($oldPasswordValue);
        });

        it('updates multiple user properties at once', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $user = User::create(
                $userId->value(),
                'oldusername',
                'old@example.com',
                'OldPassword123!'
            );

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('findById')
                ->with($userId)
                ->once()
                ->andReturn($user);
            $mockUserRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new UpdateUserCommand($mockUow);
            $input = new UpdateUserInputDTO(
                username: 'newusername',
                email: 'new@example.com',
                plainPassword: 'NewPassword456!'
            );

            $command->execute($userId, $input);

            expect($user->getUsername())->toBe('newusername')
                ->and($user->getEmail()->value())->toBe('new@example.com');
        });

        it('does not change properties when null values provided', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $user = User::create(
                $userId->value(),
                'testuser',
                'test@example.com',
                'Password123!'
            );

            $originalUsername = $user->getUsername();
            $originalEmail = $user->getEmail()->value();

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('findById')
                ->with($userId)
                ->once()
                ->andReturn($user);
            $mockUserRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new UpdateUserCommand($mockUow);
            $input = new UpdateUserInputDTO();

            $command->execute($userId, $input);

            expect($user->getUsername())->toBe($originalUsername)
                ->and($user->getEmail()->value())->toBe($originalEmail);
        });

        it('rolls back transaction on failure', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('findById')
                ->with($userId)
                ->once()
                ->andThrow(new Exception('User not found'));
            $mockUow->shouldReceive('rollback')->once();

            $command = new UpdateUserCommand($mockUow);
            $input = new UpdateUserInputDTO(username: 'newusername');

            expect(fn() => $command->execute($userId, $input))->toThrow(Exception::class);
        });
    });
});

