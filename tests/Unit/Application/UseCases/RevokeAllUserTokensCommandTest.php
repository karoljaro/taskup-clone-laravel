<?php

use App\Core\Application\Commands\RevokeAllUserTokensCommand;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\UserId;

describe('RevokeAllUserTokensCommand', function () {
    describe('execute()', function () {
        it('calls repository getByUserId with correct user ID', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);
            $mockUow->shouldReceive('commit')->once();

            $command = new RevokeAllUserTokensCommand($mockUow);
            $command->execute($userId);
        });

        it('handles user with no tokens', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('a1b2c3d4-e5f6-7890-abcd-ef1234567890');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);
            $mockTokenRepo->shouldNotReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $command = new RevokeAllUserTokensCommand($mockUow);
            $command->execute($userId);
        });

        it('processes multiple user IDs independently', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $userId1 = new UserId('b2c3d4e5-f6a7-8901-bcde-f12345678901');
            $userId2 = new UserId('c3d4e5f6-a7b8-9012-cdef-123456789012');

            $mockUow->shouldReceive('begin')->twice();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId1)
                ->once()
                ->andReturn([]);
            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId2)
                ->once()
                ->andReturn([]);
            $mockUow->shouldReceive('commit')->twice();

            $command = new RevokeAllUserTokensCommand($mockUow);
            $command->execute($userId1);
            $command->execute($userId2);
        });

        it('returns void', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('d4e5f6a7-b8c9-0123-d4ef-234567890123');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('getByUserId')
                ->andReturn([]);
            $mockUow->shouldReceive('commit')->once();

            $command = new RevokeAllUserTokensCommand($mockUow);
            $result = $command->execute($userId);

            expect($result)->toBeNull();
        });

        it('calls getByUserId exactly once per execute call', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('e5f6a7b8-c9d0-1234-e5f6-345678901234');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);
            $mockUow->shouldReceive('commit')->once();

            $command = new RevokeAllUserTokensCommand($mockUow);
            $command->execute($userId);
        });

        it('accepts valid UserId instances', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $userId = new UserId('f6a7b8c9-d012-3456-f7a8-b9c0d1e2f3a4');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('getByUserId')
                ->with($userId)
                ->once()
                ->andReturn([]);
            $mockUow->shouldReceive('commit')->once();

            $command = new RevokeAllUserTokensCommand($mockUow);
            $command->execute($userId);
        });
    });
});

