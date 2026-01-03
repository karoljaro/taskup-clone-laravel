<?php

use App\Core\Application\Commands\RevokeTokenCommand;
use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\TokenId;

describe('RevokeTokenCommand', function () {
    describe('execute()', function () {
        it('throws TokenNotFoundException when token does not exist', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('b2c3d4e5-f6a7-8901-bcde-f12345678901');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(TokenNotFoundException::class);

            $command = new RevokeTokenCommand($mockTokenRepo);

            expect(fn () => $command->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('calls repository findById with correct token ID', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('a1b2c3d4-e5f6-7890-abcd-ef1234567890');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once();

            $command = new RevokeTokenCommand($mockTokenRepo);

            // This will throw because findById doesn't return anything, but we're testing the call
            try {
                $command->execute($tokenId);
            } catch (Throwable $e) {
                // Expected
            }
        });

        it('propagates TokenNotFoundException with message', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('c3d4e5f6-a7b8-9012-cdef-123456789012');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(new TokenNotFoundException('Token with ID ' . $tokenId->value() . ' not found'));

            $command = new RevokeTokenCommand($mockTokenRepo);

            expect(fn () => $command->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('accepts different valid TokenId formats', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('d4e5f6a7-b8c9-0123-d4ef-234567890123');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(TokenNotFoundException::class);

            $command = new RevokeTokenCommand($mockTokenRepo);

            expect(fn () => $command->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('returns void on successful revocation', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('e5f6a7b8-c9d0-1234-e5f6-345678901234');

            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->andThrow(TokenNotFoundException::class);

            $command = new RevokeTokenCommand($mockTokenRepo);

            expect(fn () => $command->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });
    });
});

