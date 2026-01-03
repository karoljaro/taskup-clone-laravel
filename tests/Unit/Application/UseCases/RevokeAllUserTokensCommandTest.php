<?php

use App\Core\Application\Commands\RevokeAllUserTokensCommand;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\UserId;

describe('RevokeAllUserTokensCommand', function () {
    describe('execute()', function () {
        it('calls repository getByUserId with correct user ID', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);

            $command = new RevokeAllUserTokensCommand($mockTokenRepo);
            $command->execute($userId);
        });

        it('handles user with no tokens', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('a1b2c3d4-e5f6-7890-abcd-ef1234567890');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);

            $mockTokenRepo->shouldNotReceive('save');

            $command = new RevokeAllUserTokensCommand($mockTokenRepo);
            $command->execute($userId);
        });

        it('processes multiple user IDs independently', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId1 = new UserId('b2c3d4e5-f6a7-8901-bcde-f12345678901');
            $userId2 = new UserId('c3d4e5f6-a7b8-9012-cdef-123456789012');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId1)
                ->once()
                ->andReturn([]);

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId2)
                ->once()
                ->andReturn([]);

            $command = new RevokeAllUserTokensCommand($mockTokenRepo);
            $command->execute($userId1);
            $command->execute($userId2);
        });

        it('returns void', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('d4e5f6a7-b8c9-0123-d4ef-234567890123');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->andReturn([]);

            $command = new RevokeAllUserTokensCommand($mockTokenRepo);
            $result = $command->execute($userId);

            expect($result)->toBeNull();
        });

        it('calls getByUserId exactly once per execute call', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('e5f6a7b8-c9d0-1234-e5f6-345678901234');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);

            $command = new RevokeAllUserTokensCommand($mockTokenRepo);
            $command->execute($userId);
        });

        it('accepts valid UserId instances', function () {
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('f6a7b8c9-d012-3456-f7a8-b9c0d1e2f3a4');

            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);

            $command = new RevokeAllUserTokensCommand($mockTokenRepo);
            $command->execute($userId);
        });
    });
});

