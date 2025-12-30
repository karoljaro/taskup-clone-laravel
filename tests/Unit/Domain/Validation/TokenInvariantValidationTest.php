<?php

use App\Core\Domain\Exceptions\InvalidIdException;
use App\Core\Domain\Exceptions\InvalidPlainTextTokenException;
use App\Core\Domain\Exceptions\InvalidTokenTimestampException;
use App\Core\Domain\Validation\TokenInvariantValidation;
use App\Core\Domain\VO\TokenId;
use App\Core\Domain\VO\UserId;
describe('TokenInvariantValidation', function () {
    describe('validateCreateProps', function () {
        it('passes with valid data and no expiration', function () {
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');

            TokenInvariantValidation::validateCreateProps(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: $userId,
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            expect(true)->toBeTrue();
        });

        it('passes with valid data and future expiration', function () {
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $futureTime = (new DateTimeImmutable())->modify('+1 hour');

            TokenInvariantValidation::validateCreateProps(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: $userId,
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: $futureTime
            );

            expect(true)->toBeTrue();
        });

        it('fails with invalid ID', function () {
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');

            TokenInvariantValidation::validateCreateProps(
                id: '',
                userId: $userId,
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );
        })->throws(InvalidIdException::class);

        it('fails with invalid plain text token', function () {
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');

            TokenInvariantValidation::validateCreateProps(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: $userId,
                plainTextToken: 'short',
                expiresAt: null
            );
        })->throws(InvalidPlainTextTokenException::class);

        it('fails with past expiration timestamp', function () {
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $pastTime = (new DateTimeImmutable())->modify('-1 hour');

            TokenInvariantValidation::validateCreateProps(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: $userId,
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: $pastTime
            );
        })->throws(InvalidTokenTimestampException::class);
    });

    describe('validateReconstructProps', function () {
        it('passes with valid reconstruction data', function () {
            $tokenId = TokenId::create('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $now = new DateTimeImmutable();
            $futureTime = $now->modify('+1 hour');

            TokenInvariantValidation::validateReconstructProps(
                id: $tokenId,
                userId: $userId,
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: $futureTime,
                isRevoked: false,
                createdAt: $now,
                lastUsedAt: $now
            );

            expect(true)->toBeTrue();
        });

        it('passes with revoked token', function () {
            $tokenId = TokenId::create('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $now = new DateTimeImmutable();

            TokenInvariantValidation::validateReconstructProps(
                id: $tokenId,
                userId: $userId,
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null,
                isRevoked: true,
                createdAt: $now,
                lastUsedAt: $now
            );

            expect(true)->toBeTrue();
        });

        it('fails with invalid plain text token', function () {
            $tokenId = TokenId::create('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $now = new DateTimeImmutable();

            TokenInvariantValidation::validateReconstructProps(
                id: $tokenId,
                userId: $userId,
                plainTextToken: 'invalid',
                expiresAt: null,
                isRevoked: false,
                createdAt: $now,
                lastUsedAt: $now
            );
        })->throws(InvalidPlainTextTokenException::class);

        it('fails when lastUsedAt is before createdAt', function () {
            $tokenId = TokenId::create('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $now = new DateTimeImmutable();
            $earlier = $now->modify('-1 hour');

            TokenInvariantValidation::validateReconstructProps(
                id: $tokenId,
                userId: $userId,
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null,
                isRevoked: false,
                createdAt: $now,
                lastUsedAt: $earlier
            );
        })->throws(Exception::class);
    });

    describe('validateReconstructedToken', function () {
        it('passes with valid token', function () {
            $token = \App\Core\Domain\Entities\Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            TokenInvariantValidation::validateReconstructedToken($token);

            expect(true)->toBeTrue();
        });
    });
});

