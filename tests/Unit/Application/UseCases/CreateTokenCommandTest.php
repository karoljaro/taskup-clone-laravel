<?php

use App\Core\Application\Commands\CreateTokenCommand;
use App\Core\Application\DTOs\CreateTokenInputDTO;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\Token;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\UserId;

describe('CreateTokenCommand', function () {
    describe('execute()', function () {
        it('creates and saves a new token with valid data', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $generatedId = '018f47ac-10b5-7abc-8372-a567a0e02b2c';
            $userId = '018f47ac-10b5-7bcd-8901-abcdef123456';
            $plainTextToken = 'hashed_token_value_that_is_at_least_32_chars_long_to_pass_validation_12345';
            $expiresAt = new DateTimeImmutable('+30 days');

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn($generatedId);

            $mockTokenRepo->shouldReceive('save')
                ->once();

            $command = new CreateTokenCommand($mockTokenRepo, $mockIdGenerator);
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
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn('018f47ac-10b5-7bcd-8abc-d479a567a0e0');

            $mockTokenRepo->shouldReceive('save');

            $command = new CreateTokenCommand($mockTokenRepo, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7cde-8f67-890abcdef123'),
                plainTextToken: 'token_value_that_is_at_least_32_characters_long_for_validation_1234567',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $command->execute($input);
        });

        it('calls repository save exactly once', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn('018f47ac-10b5-7def-89bc-def012345678');

            $mockTokenRepo->shouldReceive('save')
                ->once();

            $command = new CreateTokenCommand($mockTokenRepo, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7e01-8901-f23456f789ab'),
                plainTextToken: 'another_token_that_is_at_least_32_chars_long_for_validation_test_12345',
                expiresAt: new DateTimeImmutable('+7 days')
            );

            $command->execute($input);
        });

        it('returns token with generated ID', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $generatedId = '018f47ac-10b5-7f12-8a23-456789abcdef';

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn($generatedId);

            $mockTokenRepo->shouldReceive('save');

            $command = new CreateTokenCommand($mockTokenRepo, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7234-8def-567890123456'),
                plainTextToken: 'token_with_id_that_is_at_least_32_chars_long_for_validation_test_12345',
                expiresAt: new DateTimeImmutable('+14 days')
            );

            $result = $command->execute($input);

            expect($result->getId()->value())->toBe($generatedId);
        });

        it('token is not revoked when created', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7abc-8def-567890abcdef');

            $mockTokenRepo->shouldReceive('save');

            $command = new CreateTokenCommand($mockTokenRepo, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7456-8bcd-ef234567890a'),
                plainTextToken: 'active_token_that_is_at_least_32_characters_long_for_validation_test_1234',
                expiresAt: new DateTimeImmutable('+60 days')
            );

            $result = $command->execute($input);

            expect($result->isRevoked())->toBeFalse();
        });

        it('returns token with correct user ID', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $userId = '018f47ac-10b5-7567-8cde-f345678901bc';

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7678-8def-012345678901');

            $mockTokenRepo->shouldReceive('save');

            $command = new CreateTokenCommand($mockTokenRepo, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId($userId),
                plainTextToken: 'user_specific_token_that_is_at_least_32_chars_for_validation_test_12345',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $result = $command->execute($input);

            expect($result->getUserId()->value())->toBe($userId);
        });

        it('returns token with correct expiration date', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $expiresAt = new DateTimeImmutable('+90 days');

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7789-8e01-f234567890ab');

            $mockTokenRepo->shouldReceive('save');

            $command = new CreateTokenCommand($mockTokenRepo, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7890-8f12-3456789abcde'),
                plainTextToken: 'expiring_token_that_is_at_least_32_chars_long_for_validation_test_1234567',
                expiresAt: $expiresAt
            );

            $result = $command->execute($input);

            expect($result->getExpiresAt()->getTimestamp())->toBe($expiresAt->getTimestamp());
        });

        it('repository save receives the created token', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7901-8023-456789abcdef');

            $savedToken = null;
            $mockTokenRepo->shouldReceive('save')
                ->andReturnUsing(function ($token) use (&$savedToken) {
                    $savedToken = $token;
                });

            $command = new CreateTokenCommand($mockTokenRepo, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7a12-8134-56789abcdef0'),
                plainTextToken: 'saved_token_that_is_at_least_32_characters_long_for_validation_test_1234',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $result = $command->execute($input);

            expect($savedToken)->toBeInstanceOf(Token::class)
                ->and($savedToken->getId()->value())->toBe($result->getId()->value());
        });

        it('creates token with null description', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTokenRepo = mock(TokenRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('018f47ac-10b5-7b23-8245-6789abcdef01');

            $mockTokenRepo->shouldReceive('save');

            $command = new CreateTokenCommand($mockTokenRepo, $mockIdGenerator);
            $input = new CreateTokenInputDTO(
                userId: new UserId('018f47ac-10b5-7c34-8356-789abcdef012'),
                plainTextToken: 'token_with_null_description_that_is_at_least_32_chars_for_validation_1234',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $result = $command->execute($input);

            expect($result->getPlainTextToken())->toBe($input->plainTextToken);
        });
    });
});

