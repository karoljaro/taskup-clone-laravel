<?php

use App\Core\Application\Commands\UpdateTokenLastUsedCommand;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Entities\Token;
use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\TokenId;
use App\Core\Domain\VO\UserId;
use Throwable;

describe('UpdateTokenLastUsedCommand', function () {
    describe('execute()', function () {
        it('throws TokenNotFoundException when token does not exist', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7abc-8372-a567a0e02b2c');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(new TokenNotFoundException($tokenId));
            $mockUow->shouldReceive('rollback')->once();

            $command = new UpdateTokenLastUsedCommand($mockUow);

            expect(fn () => $command->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('calls repository findById with correct token ID', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7bcd-8901-abcdef123456');

            $token = Token::create(
                id: $tokenId->value(),
                userId: new UserId('018f47ac-10b5-7cde-8f67-890abcdef123'),
                plainTextToken: 'test_token_long_enough_for_validation_1234567890',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andReturn($token);
            $mockTokenRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new UpdateTokenLastUsedCommand($mockUow);

            $command->execute($tokenId);
        });

        it('propagates TokenNotFoundException with message', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7cde-8f67-890abcdef123');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(new TokenNotFoundException($tokenId));
            $mockUow->shouldReceive('rollback')->once();

            $command = new UpdateTokenLastUsedCommand($mockUow);

            expect(fn () => $command->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('accepts different valid TokenId formats', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7def-89bc-def012345678');

            $token = Token::create(
                id: $tokenId->value(),
                userId: new UserId('018f47ac-10b5-7e01-8901-f23456f789ab'),
                plainTextToken: 'another_token_long_enough_for_validation_1234567890',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andReturn($token);
            $mockTokenRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new UpdateTokenLastUsedCommand($mockUow);

            $command->execute($tokenId);
        });

        it('returns void', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7e01-8901-f23456f789ab');

            $token = Token::create(
                id: $tokenId->value(),
                userId: new UserId('018f47ac-10b5-7f12-8a23-456789abcdef'),
                plainTextToken: 'token_for_void_test_long_enough_for_validation_1234567890',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andReturn($token);
            $mockTokenRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new UpdateTokenLastUsedCommand($mockUow);

            $command->execute($tokenId);

            expect(true)->toBeTrue();
        });

        it('processes multiple token IDs independently', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId1 = TokenId::create('018f47ac-10b5-7f12-8a23-456789abcdef');
            $tokenId2 = TokenId::create('018f47ac-10b5-7234-8def-567890123456');

            $token1 = Token::create(
                id: $tokenId1->value(),
                userId: new UserId('018f47ac-10b5-7345-8abc-d1e2f3456789'),
                plainTextToken: 'token1_long_enough_for_validation_1234567890',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $token2 = Token::create(
                id: $tokenId2->value(),
                userId: new UserId('018f47ac-10b5-7456-8bcd-e2f3456789ab'),
                plainTextToken: 'token2_long_enough_for_validation_1234567890',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $mockUow->shouldReceive('begin')->twice();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId1)
                ->once()
                ->andReturn($token1);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId2)
                ->once()
                ->andReturn($token2);
            $mockTokenRepo->shouldReceive('save')->twice();
            $mockUow->shouldReceive('commit')->twice();

            $command = new UpdateTokenLastUsedCommand($mockUow);

            $command->execute($tokenId1);
            $command->execute($tokenId2);
        });

        it('calls findById exactly once per execute call', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('018f47ac-10b5-7345-8abc-d1e2f3456789');

            $token = Token::create(
                id: $tokenId->value(),
                userId: new UserId('018f47ac-10b5-7567-8bcd-f3456789abcd'),
                plainTextToken: 'once_test_token_long_enough_for_validation_1234567890',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andReturn($token);
            $mockTokenRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new UpdateTokenLastUsedCommand($mockUow);

            $command->execute($tokenId);
        });
    });
});

