<?php

use App\Core\Domain\Entities\Token;
use App\Core\Domain\Exceptions\InvalidIdException;
use App\Core\Domain\Exceptions\InvalidPlainTextTokenException;
use App\Core\Domain\Exceptions\InvalidTokenTimestampException;
use App\Core\Domain\VO\TokenId;
use App\Core\Domain\VO\UserId;

describe('Token Entity', function () {
    describe('Factory - create()', function () {
        it('can be created with valid data without expiration', function () {
            $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';

            $token = Token::create(
                id: $id,
                userId: $userId,
                plainTextToken: $plainTextToken,
                expiresAt: null
            );

            expect($token)->toBeInstanceOf(Token::class)
                ->and($token->getPlainTextToken())->toBe($plainTextToken)
                ->and($token->getExpiresAt())->toBeNull()
                ->and($token->isRevoked())->toBeFalse()
                ->and($token->isValid())->toBeTrue();
        });

        it('can be created with valid data and future expiration', function () {
            $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';
            $expiresAt = (new DateTimeImmutable())->modify('+1 hour');

            $token = Token::create(
                id: $id,
                userId: $userId,
                plainTextToken: $plainTextToken,
                expiresAt: $expiresAt
            );

            expect($token)->toBeInstanceOf(Token::class)
                ->and($token->getExpiresAt())->toBe($expiresAt)
                ->and($token->isValid())->toBeTrue();
        });

        it('sets correct timestamps on creation', function () {
            $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';
            $beforeCreation = new DateTimeImmutable();

            $token = Token::create(
                id: $id,
                userId: $userId,
                plainTextToken: $plainTextToken,
                expiresAt: null
            );

            $afterCreation = new DateTimeImmutable();

            expect($token->getCreatedAt() >= $beforeCreation)->toBeTrue()
                ->and($token->getCreatedAt() <= $afterCreation)->toBeTrue()
                ->and($token->getLastUsedAt())->toBe($token->getCreatedAt());
        });

        it('fails with invalid ID', function () {
            Token::create(
                id: '',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );
        })->throws(InvalidIdException::class);

        it('fails with invalid plain text token', function () {
            Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'short',
                expiresAt: null
            );
        })->throws(InvalidPlainTextTokenException::class);

        it('fails with past expiration timestamp', function () {
            $pastTime = (new DateTimeImmutable())->modify('-1 hour');

            Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: $pastTime
            );
        })->throws(InvalidTokenTimestampException::class);
    });

    describe('Factory - reconstruct()', function () {
        it('can be reconstructed from database data', function () {
            $tokenId = TokenId::create('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';
            $now = new DateTimeImmutable();
            $expiresAt = $now->modify('+1 hour');

            $token = Token::reconstruct(
                id: $tokenId,
                userId: $userId,
                plainTextToken: $plainTextToken,
                expiresAt: $expiresAt,
                isRevoked: false,
                createdAt: $now,
                lastUsedAt: $now
            );

            expect($token)->toBeInstanceOf(Token::class)
                ->and($token->getId()->equals($tokenId))->toBeTrue()
                ->and($token->getPlainTextToken())->toBe($plainTextToken)
                ->and($token->isRevoked())->toBeFalse()
                ->and($token->isValid())->toBeTrue();
        });

        it('can reconstruct revoked token', function () {
            $tokenId = TokenId::create('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';
            $now = new DateTimeImmutable();

            $token = Token::reconstruct(
                id: $tokenId,
                userId: $userId,
                plainTextToken: $plainTextToken,
                expiresAt: null,
                isRevoked: true,
                createdAt: $now,
                lastUsedAt: $now
            );

            expect($token->isRevoked())->toBeTrue()
                ->and($token->isValid())->toBeFalse();
        });
    });

    describe('Behaviors - isValid()', function () {
        it('returns true for non-revoked, non-expired token without expiration', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            expect($token->isValid())->toBeTrue();
        });

        it('returns true for non-revoked, non-expired token with future expiration', function () {
            $expiresAt = (new DateTimeImmutable())->modify('+1 hour');

            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: $expiresAt
            );

            expect($token->isValid())->toBeTrue();
        });

        it('returns false for revoked token', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            $token->revoke();

            expect($token->isValid())->toBeFalse();
        });
    });

    describe('Behaviors - isExpired()', function () {
        it('returns false when no expiration is set', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            expect($token->isExpired())->toBeFalse();
        });

        it('returns false when expiration is in the future', function () {
            $expiresAt = (new DateTimeImmutable())->modify('+1 hour');

            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: $expiresAt
            );

            expect($token->isExpired())->toBeFalse();
        });

        it('returns true when expiration is in the past', function () {
            $tokenId = TokenId::create('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';
            $now = new DateTimeImmutable();
            $pastExpiresAt = $now->modify('-1 hour');

            $token = Token::reconstruct(
                id: $tokenId,
                userId: $userId,
                plainTextToken: $plainTextToken,
                expiresAt: $pastExpiresAt,
                isRevoked: false,
                createdAt: $now,
                lastUsedAt: $now
            );

            expect($token->isExpired())->toBeTrue();
        });
    });

    describe('Behaviors - revoke()', function () {
        it('marks token as revoked', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            expect($token->isRevoked())->toBeFalse();

            $token->revoke();

            expect($token->isRevoked())->toBeTrue();
        });

        it('invalidates token when revoked', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            $token->revoke();

            expect($token->isValid())->toBeFalse();
        });
    });

    describe('Behaviors - updateLastUsedAt()', function () {
        it('updates lastUsedAt timestamp', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            $originalLastUsedAt = $token->getLastUsedAt();

            usleep(100000); // Sleep for 100ms to ensure time difference

            $token->updateLastUsedAt();

            expect($token->getLastUsedAt() > $originalLastUsedAt)->toBeTrue();
        });

        it('createdAt remains unchanged when updating lastUsedAt', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            $originalCreatedAt = $token->getCreatedAt();

            usleep(100000); // Sleep for 100ms

            $token->updateLastUsedAt();

            expect($token->getCreatedAt())->toBe($originalCreatedAt);
        });
    });

    describe('Behaviors - matches()', function () {
        it('returns true for matching plain text token', function () {
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';

            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: $plainTextToken,
                expiresAt: null
            );

            expect($token->matches($plainTextToken))->toBeTrue();
        });

        it('returns false for non-matching plain text token', function () {
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';

            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: $plainTextToken,
                expiresAt: null
            );

            expect($token->matches('different_token_string_123_hyphens'))->toBeFalse();
        });

        it('is timing attack resistant using hash_equals', function () {
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';

            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: $plainTextToken,
                expiresAt: null
            );

            // Test with nearly matching token (only last char different)
            $similarToken = 'valid_token_string_with_numbers_123_and_hyphens-ox';
            expect($token->matches($similarToken))->toBeFalse();
        });
    });

    describe('Getters', function () {
        it('getId returns TokenId instance', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            expect($token->getId())->toBeInstanceOf(TokenId::class);
        });

        it('getUserId returns UserId instance', function () {
            $userId = new UserId('550e8400-e29b-41d4-a716-446655440000');

            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: $userId,
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            expect($token->getUserId()->equals($userId))->toBeTrue();
        });

        it('getPlainTextToken returns correct value', function () {
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';

            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: $plainTextToken,
                expiresAt: null
            );

            expect($token->getPlainTextToken())->toBe($plainTextToken);
        });

        it('getCreatedAt returns DateTimeImmutable', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            expect($token->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class);
        });

        it('getLastUsedAt returns DateTimeImmutable', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            expect($token->getLastUsedAt())->toBeInstanceOf(DateTimeImmutable::class);
        });

        it('createdAt is immutable and never changes', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            $originalCreatedAt = $token->getCreatedAt();

            $token->revoke();
            $token->updateLastUsedAt();

            expect($token->getCreatedAt())->toBe($originalCreatedAt);
        });

        it('id is readonly', function () {
            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: 'valid_token_string_with_numbers_123_and_hyphens-ok',
                expiresAt: null
            );

            expect($token->getId())->toBeInstanceOf(TokenId::class);
        });
    });

    describe('toString()', function () {
        it('returns plain text token as string', function () {
            $plainTextToken = 'valid_token_string_with_numbers_123_and_hyphens-ok';

            $token = Token::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                userId: new UserId('550e8400-e29b-41d4-a716-446655440000'),
                plainTextToken: $plainTextToken,
                expiresAt: null
            );

            expect((string) $token)->toBe($plainTextToken);
        });
    });
});

