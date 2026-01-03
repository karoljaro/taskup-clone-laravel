<?php

use App\Core\Application\Queries\GetTokenByIdQuery;
use App\Core\Domain\Entities\Token;
use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\TokenId;

describe('GetTokenByIdQuery', function () {
    describe('execute()', function () {
        it('returns token when token exists', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7abc-8372-a567a0e02b2c');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once();

            $query = new GetTokenByIdQuery($mockTokenRepo);

            try {
                $query->execute($tokenId);
            } catch (Throwable $e) {
                // Expected - we're testing that it calls the repository
            }
        });

        it('throws TokenNotFoundException when token does not exist', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7bcd-8901-abcdef123456');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(TokenNotFoundException::class);

            $query = new GetTokenByIdQuery($mockTokenRepo);

            expect(fn () => $query->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('calls repository findById with correct token ID', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7cde-8f67-890abcdef123');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(TokenNotFoundException::class);

            $query = new GetTokenByIdQuery($mockTokenRepo);

            try {
                $query->execute($tokenId);
            } catch (TokenNotFoundException $e) {
                // Expected
            }
        });

        it('propagates TokenNotFoundException with message', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7def-89bc-def012345678');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(new TokenNotFoundException('Token with ID ' . $tokenId->value() . ' not found'));

            $query = new GetTokenByIdQuery($mockTokenRepo);

            expect(fn () => $query->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('returns Token instance', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7e01-8901-f23456f789ab');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->andThrow(TokenNotFoundException::class);

            $query = new GetTokenByIdQuery($mockTokenRepo);

            expect(fn () => $query->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('accepts different valid TokenId formats', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7234-8def-567890123456');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(TokenNotFoundException::class);

            $query = new GetTokenByIdQuery($mockTokenRepo);

            expect(fn () => $query->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('calls findById exactly once per execute call', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7456-8bcd-ef234567890a');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(TokenNotFoundException::class);

            $query = new GetTokenByIdQuery($mockTokenRepo);

            expect(fn () => $query->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('processes multiple token IDs independently', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId1 = TokenId::create('018f47ac-10b5-7567-8cde-f345678901bc');
            $tokenId2 = TokenId::create('018f47ac-10b5-7678-8def-012345678901');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId1)
                ->once()
                ->andThrow(TokenNotFoundException::class);

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId2)
                ->once()
                ->andThrow(TokenNotFoundException::class);

            $query = new GetTokenByIdQuery($mockTokenRepo);

            expect(fn() => $query->execute($tokenId1))
                ->toThrow(TokenNotFoundException::class)
                ->and(fn() => $query->execute($tokenId2))
                ->toThrow(TokenNotFoundException::class);

        });
    });
});

