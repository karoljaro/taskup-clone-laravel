<?php

use App\Core\Application\Queries\GetUserByIdQuery;
use App\Core\Domain\Entities\User;
use App\Core\Domain\Exceptions\UserNotFoundException;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\UserId;

describe('GetUserByIdQuery', function () {
    describe('execute()', function () {
        it('returns user when user exists', function () {
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $user = User::create(
                $userId->value(),
                'testuser',
                'test@example.com',
                'Password123!'
            );

            $mockUserRepo->shouldReceive('findById')
                ->with($userId)
                ->once()
                ->andReturn($user);

            $query = new GetUserByIdQuery($mockUserRepo);
            $result = $query->execute($userId);

            expect($result)->toBeInstanceOf(User::class)
                ->and($result->getUsername())->toBe('testuser')
                ->and($result->getEmail()->value())->toBe('test@example.com');
        });

        it('throws UserNotFoundException when user does not exist', function () {
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUserRepo->shouldReceive('findById')
                ->with($userId)
                ->once()
                ->andThrow(new UserNotFoundException('User not found'));

            $query = new GetUserByIdQuery($mockUserRepo);

            expect(fn() => $query->execute($userId))
                ->toThrow(UserNotFoundException::class);
        });

        it('returns user with correct ID', function () {
            $mockUserRepo = mock(UserRepository::class);

            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $user = User::create(
                $userId->value(),
                'johndoe',
                'john@example.com',
                'SecurePassword123!'
            );

            $mockUserRepo->shouldReceive('findById')
                ->with($userId)
                ->once()
                ->andReturn($user);

            $query = new GetUserByIdQuery($mockUserRepo);
            $result = $query->execute($userId);

            expect($result->getId()->value())->toBe($userId->value());
        });
    });
});

