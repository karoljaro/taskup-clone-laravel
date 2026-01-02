<?php

use App\Core\Application\Queries\GetUserByEmailQuery;
use App\Core\Domain\Entities\User;
use App\Core\Domain\Exceptions\UserNotFoundException;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\Email;
use App\Core\Domain\VO\UserId;

describe('GetUserByEmailQuery', function () {
    describe('execute()', function () {
        it('returns user when email exists', function () {
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $user = User::create(
                $userId->value(),
                'testuser',
                'test@example.com',
                'Password123!'
            );

            $email = Email::create('test@example.com');

            $mockUserRepo->shouldReceive('findByEmail')
                ->with($email)
                ->once()
                ->andReturn($user);

            $query = new GetUserByEmailQuery($mockUserRepo);
            $result = $query->execute($email);

            expect($result)->toBeInstanceOf(User::class)
                ->and($result->getUsername())->toBe('testuser')
                ->and($result->getEmail()->value())->toBe('test@example.com');
        });

        it('throws UserNotFoundException when email does not exist', function () {
            $mockUserRepo = mock(UserRepository::class);

            $email = Email::create('nonexistent@example.com');

            $mockUserRepo->shouldReceive('findByEmail')
                ->with($email)
                ->once()
                ->andThrow(new UserNotFoundException('User not found'));

            $query = new GetUserByEmailQuery($mockUserRepo);

            expect(fn() => $query->execute($email))
                ->toThrow(UserNotFoundException::class);
        });

        it('returns user with correct email', function () {
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $user = User::create(
                $userId->value(),
                'johndoe',
                'john@example.com',
                'SecurePassword123!'
            );

            $email = Email::create('john@example.com');

            $mockUserRepo->shouldReceive('findByEmail')
                ->with($email)
                ->once()
                ->andReturn($user);

            $query = new GetUserByEmailQuery($mockUserRepo);
            $result = $query->execute($email);

            expect($result->getEmail()->value())->toBe('john@example.com');
        });
    });
});

