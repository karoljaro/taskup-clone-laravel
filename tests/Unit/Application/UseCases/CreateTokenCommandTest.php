<?php

use App\Core\Application\Commands\CreateTokenCommand;
use App\Core\Application\DTOs\CreateTokenInputDTO;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\Token;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\UserId;

describe('CreateTokenCommand', function () {
    describe('execute()', function () {
        it('creates and saves a new token with valid data', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $generatedId = '018f47ac-10b5-7abc-8372-a567a0e02b2c';
            $userId = '018f47ac-10b5-7bcd-8901-abcdef123456';
            $plainTextToken = 'hashed_token_value_that_is_at_least_32_chars_long_to_pass_validation_12345';
            $expiresAt = new DateTimeImmutable('+30 days');

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn($generatedId);

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateTokenCommand($mockUow, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId($userId),
                plainTextToken: $plainTextToken,
                expiresAt: $expiresAt
            );

            $result = $command->execute($input);

            expect($result)->toBeInstanceOf(Token::class)
                ->and($result->getId()->value())->toBe($generatedId)
                ->and($result->getPlainTextToken())->toBe($plainTextToken);
        });

        it('calls id generator exactly once', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn('018f47ac-10b5-7bcd-8abc-d479a567a0e0');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateTokenCommand($mockUow, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7cde-8f67-890abcdef123'),
                plainTextToken: 'token_value_that_is_at_least_32_characters_long_for_validation_1234567',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $command->execute($input);
        });

        it('returns token with generated ID', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $generatedId = '018f47ac-10b5-7def-89bc-def012345678';
            $mockIdGenerator->shouldReceive('generate')
                ->andReturn($generatedId);

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateTokenCommand($mockUow, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7e01-8901-f23456f789ab'),
                plainTextToken: 'another_token_that_is_at_least_32_chars_long_for_validation_test_12345',
                expiresAt: new DateTimeImmutable('+7 days')
            );

            $result = $command->execute($input);

            expect($result->getId()->value())->toBe($generatedId);
        });

        it('token is not revoked when created', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7f12-8901-234567890abc');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateTokenCommand($mockUow, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7f23-8901-345678901bcd'),
                plainTextToken: 'token_that_is_long_enough_for_validation_requirements_here_1234567890',
                expiresAt: new DateTimeImmutable('+14 days')
            );

            $result = $command->execute($input);

            expect($result->isRevoked())->toBeFalse();
        });

        it('returns token with correct user ID', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('018f47ac-10b5-7f34-8901-456789012cde');

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7f45-8901-567890123def');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateTokenCommand($mockUow, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: $userId,
                plainTextToken: 'yet_another_token_long_enough_for_validation_purposes_12345678901234567',
                expiresAt: new DateTimeImmutable('+60 days')
            );

            $result = $command->execute($input);

            expect($result->getUserId()->value())->toBe($userId->value());
        });

        it('returns token with correct expiration date', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $expiresAt = new DateTimeImmutable('+90 days');

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7f56-8901-6789012345ef');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateTokenCommand($mockUow, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7f67-8901-78901234567f'),
                plainTextToken: 'token_with_specific_expiration_time_that_is_long_enough_1234567890123456',
                expiresAt: $expiresAt
            );

            $result = $command->execute($input);

            expect($result->getExpiresAt())->toEqual($expiresAt);
        });

        it('creates token with null expiration', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7f78-8901-890123456789');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $command = new CreateTokenCommand($mockUow, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7f89-8901-90123456789a'),
                plainTextToken: 'never_expiring_token_that_is_long_enough_for_validation_1234567890123456',
                expiresAt: null
            );

            $result = $command->execute($input);

            expect($result->getExpiresAt())->toBeNull();
        });

        it('sets correct timestamps on creation', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7f9a-8901-a0123456789b');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $beforeExecution = new DateTimeImmutable();
            $command = new CreateTokenCommand($mockUow, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7fab-8901-b012345678ac'),
                plainTextToken: 'token_with_proper_timestamps_that_is_long_enough_1234567890123456789012',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $result = $command->execute($input);
            $afterExecution = new DateTimeImmutable();

            expect($result->getCreatedAt())->toBeGreaterThanOrEqual($beforeExecution)
                ->and($result->getCreatedAt())->toBeLessThanOrEqual($afterExecution)
                ->and($result->getLastUsedAt())->toEqual($result->getCreatedAt());
        });

        it('rolls back transaction on failure', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7fbc-8901-c0123456789c');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('save')
                ->once()
                ->andThrow(new Exception('Database error'));
            $mockUow->shouldReceive('rollback')->once();

            $command = new CreateTokenCommand($mockUow, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7fcd-8901-d0123456789d'),
                plainTextToken: 'token_test_rollback_that_is_long_enough_1234567890123456789012345678',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            expect(fn() => $command->execute($input))->toThrow(Exception::class);
        });
    });
});

