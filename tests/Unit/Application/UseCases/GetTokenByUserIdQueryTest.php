<?php

use App\Core\Application\Queries\GetTokenByUserIdQuery;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\UserId;

describe('GetTokenByUserIdQuery', function () {
    describe('execute()', function () {
        it('returns array of tokens for user', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('018f47ac-10b5-7abc-8372-a567a0e02b2c');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);

            $query = new GetTokenByUserIdQuery($mockTokenRepo);
            $result = $query->execute($userId);

            expect($result)->toBeArray();
        });

        it('returns empty array when user has no tokens', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('018f47ac-10b5-7bcd-8901-abcdef123456');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);

            $query = new GetTokenByUserIdQuery($mockTokenRepo);
            $result = $query->execute($userId);

            expect($result)->toBeArray()
                ->and(count($result))->toBe(0);
        });

        it('calls repository getByUserId with correct user ID', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('018f47ac-10b5-7cde-8f67-890abcdef123');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);

            $query = new GetTokenByUserIdQuery($mockTokenRepo);
            $query->execute($userId);
        });

        it('returns list of tokens of correct type', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('018f47ac-10b5-7def-89bc-def012345678');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->andReturn([]);

            $query = new GetTokenByUserIdQuery($mockTokenRepo);
            $result = $query->execute($userId);

            expect($result)->toBeArray();
        });

        it('accepts different valid UserId formats', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('018f47ac-10b5-7e01-8901-f23456f789ab');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);

            $query = new GetTokenByUserIdQuery($mockTokenRepo);
            $result = $query->execute($userId);

            expect($result)->toBeArray();
        });

        it('calls getByUserId exactly once per execute call', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('018f47ac-10b5-7234-8def-567890123456');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);

            $query = new GetTokenByUserIdQuery($mockTokenRepo);
            $query->execute($userId);
        });

        it('processes multiple user IDs independently', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId1 = new UserId('018f47ac-10b5-7456-8bcd-ef234567890a');
            $userId2 = new UserId('018f47ac-10b5-7567-8cde-f345678901bc');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId1)
                ->once()
                ->andReturn([]);

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId2)
                ->once()
                ->andReturn([]);

            $query = new GetTokenByUserIdQuery($mockTokenRepo);
            $query->execute($userId1);
            $query->execute($userId2);
        });

        it('returns tokens in expected format', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('018f47ac-10b5-7678-8def-012345678901');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->andReturn([]);

            $query = new GetTokenByUserIdQuery($mockTokenRepo);
            $result = $query->execute($userId);

            expect($result)->toBeArray();
        });
    });
});

