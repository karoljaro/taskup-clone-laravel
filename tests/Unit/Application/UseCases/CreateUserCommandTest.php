<?php

use App\Core\Application\Commands\CreateUserCommand;
use App\Core\Application\DTOs\CreateUserInputDTO;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\User;
use App\Core\Domain\Repositories\UserRepository;

describe('CreateUserCommand', function () {
    describe('execute()', function () {
        it('creates and saves a new user with valid data', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $generatedId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $username = 'testuser';
            $email = 'test@example.com';
            $plainPassword = 'SecurePassword123!';

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn($generatedId);

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateUserCommand($mockUow, $mockIdGenerator);
            $input = new CreateUserInputDTO($username, $email, $plainPassword);

            $result = $command->execute($input);

            expect($result)->toBeInstanceOf(User::class)
                ->and($result->getUsername())->toBe($username)
                ->and($result->getEmail()->value())->toBe($email);
        });

        it('calls id generator exactly once', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateUserCommand($mockUow, $mockIdGenerator);
            $input = new CreateUserInputDTO('testuser', 'test@example.com', 'Password123!');

            $command->execute($input);
        });

        it('calls repository save exactly once', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateUserCommand($mockUow, $mockIdGenerator);
            $input = new CreateUserInputDTO('johndoe', 'john@example.com', 'MyPassword123!');

            $command->execute($input);
        });

        it('returns user with generated ID', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $generatedId = 'a1b2c3d4-e5f6-7890-abcd-ef1234567890';

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn($generatedId);

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateUserCommand($mockUow, $mockIdGenerator);
            $input = new CreateUserInputDTO('newuser', 'new@example.com', 'Password456!');

            $result = $command->execute($input);

            expect($result->getId()->value())->toBe($generatedId);
        });

        it('rolls back transaction on failure', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockUserRepo = mock(UserRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('users')->andReturn($mockUserRepo);
            $mockUserRepo->shouldReceive('save')
                ->once()
                ->andThrow(new Exception('Database error'));
            $mockUow->shouldReceive('rollback')->once();

            $command = new CreateUserCommand($mockUow, $mockIdGenerator);
            $input = new CreateUserInputDTO('testuser', 'test@example.com', 'Password123!');

            expect(fn() => $command->execute($input))->toThrow(Exception::class);
        });
    });
});

