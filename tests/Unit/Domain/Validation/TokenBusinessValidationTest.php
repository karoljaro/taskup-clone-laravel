<?php

use App\Core\Domain\Exceptions\InvalidPlainTextTokenException;
use App\Core\Domain\Exceptions\InvalidTokenTimestampException;
use App\Core\Domain\Validation\TokenBusinessValidation;

describe('TokenBusinessValidation', function () {
    describe('validatePlainTextToken', function () {
        it('passes with valid token', function () {
            TokenBusinessValidation::validatePlainTextToken('valid_token_string_with_numbers_123_and_hyphens-ok');

            expect(true)->toBeTrue();
        });

        it('fails with empty token', function () {
            TokenBusinessValidation::validatePlainTextToken('');
        })->throws(InvalidPlainTextTokenException::class);

        it('fails with whitespace only token', function () {
            TokenBusinessValidation::validatePlainTextToken('   ');
        })->throws(InvalidPlainTextTokenException::class);

        it('fails with token too short', function () {
            $shortToken = str_repeat('a', 31); // MIN_TOKEN_LENGTH is 32

            TokenBusinessValidation::validatePlainTextToken($shortToken);
        })->throws(InvalidPlainTextTokenException::class);

        it('fails with token too long', function () {
            $longToken = str_repeat('a', 501); // MAX_TOKEN_LENGTH is 500

            TokenBusinessValidation::validatePlainTextToken($longToken);
        })->throws(InvalidPlainTextTokenException::class);

        it('fails with token containing special characters', function () {
            TokenBusinessValidation::validatePlainTextToken('token@with#special$chars!');
        })->throws(InvalidPlainTextTokenException::class);

        it('fails with token containing spaces', function () {
            TokenBusinessValidation::validatePlainTextToken('token with spaces');
        })->throws(InvalidPlainTextTokenException::class);

        it('passes with token containing hyphens and underscores', function () {
            TokenBusinessValidation::validatePlainTextToken('valid_token-with-hyphens_and_underscores_123');

            expect(true)->toBeTrue();
        });

        it('passes with minimum valid token length', function () {
            $minToken = str_repeat('a', 32);

            TokenBusinessValidation::validatePlainTextToken($minToken);

            expect(true)->toBeTrue();
        });

        it('passes with maximum valid token length', function () {
            $maxToken = str_repeat('a', 500);

            TokenBusinessValidation::validatePlainTextToken($maxToken);

            expect(true)->toBeTrue();
        });

        it('trims whitespace before validation', function () {
            $tokenWithWhitespace = '  valid_token_string_with_numbers_123_and_hyphens-ok  ';

            TokenBusinessValidation::validatePlainTextToken($tokenWithWhitespace);

            expect(true)->toBeTrue();
        });
    });

    describe('validateExpiresAt', function () {
        it('passes with null expiration', function () {
            TokenBusinessValidation::validateExpiresAt(null);

            expect(true)->toBeTrue();
        });

        it('passes with future expiration timestamp', function () {
            $futureTime = (new DateTimeImmutable())->modify('+1 hour');

            TokenBusinessValidation::validateExpiresAt($futureTime);

            expect(true)->toBeTrue();
        });

        it('fails with past expiration timestamp', function () {
            $pastTime = (new DateTimeImmutable())->modify('-1 hour');

            TokenBusinessValidation::validateExpiresAt($pastTime);
        })->throws(InvalidTokenTimestampException::class);

        it('fails with slightly past expiration', function () {
            $pastTime = (new DateTimeImmutable())->modify('-1 second');

            TokenBusinessValidation::validateExpiresAt($pastTime);
        })->throws(InvalidTokenTimestampException::class);

        it('passes with far future expiration', function () {
            $farFuture = (new DateTimeImmutable())->modify('+1 year');

            TokenBusinessValidation::validateExpiresAt($farFuture);

            expect(true)->toBeTrue();
        });
    });
});
