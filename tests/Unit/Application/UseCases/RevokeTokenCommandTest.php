<?php

use App\Core\Application\Commands\RevokeTokenCommand;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Entities\Token;
use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\TokenId;
use App\Core\Domain\VO\UserId;
use Throwable;

describe('RevokeTokenCommand', function () {
    describe('execute()', function () {
        it('throws TokenNotFoundException when token does not exist', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('b2c3d4e5-f6a7-8901-bcde-f12345678901');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(new TokenNotFoundException($tokenId));
            $mockUow->shouldReceive('rollback')->once();

            $command = new RevokeTokenCommand($mockUow);

            expect(fn () => $command->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('calls repository findById with correct token ID', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('a1b2c3d4-e5f6-7890-abcd-ef1234567890');

            $token = Token::create(
                id: $tokenId->value(),
                userId: new UserId('018f47ac-10b5-7abc-8372-a567a0e02b2c'),
                plainTextToken: 'test_token_that_is_long_enough_for_validation_1234567890',
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

            $command = new RevokeTokenCommand($mockUow);

            $command->execute($tokenId);
        });

        it('propagates TokenNotFoundException with message', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('c3d4e5f6-a7b8-9012-cdef-123456789012');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->once()
                ->andThrow(new TokenNotFoundException($tokenId));
            $mockUow->shouldReceive('rollback')->once();

            $command = new RevokeTokenCommand($mockUow);

            expect(fn () => $command->execute($tokenId))
                ->toThrow(TokenNotFoundException::class);
        });

        it('accepts different valid TokenId formats', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('d4e5f6a7-b8c9-0123-d4ef-234567890123');

            $token = Token::create(
                id: $tokenId->value(),
                userId: new UserId('018f47ac-10b5-7bcd-8abc-d479a567a0e0'),
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

            $command = new RevokeTokenCommand($mockUow);

            $command->execute($tokenId);
        });

        it('returns void on successful revocation', function () {
            $mockUow = mock(UnitOfWork::class);
            $mockTokenRepo = mock(TokenRepository::class);
            $tokenId = TokenId::create('e5f6a7b8-c9d0-1234-e5f6-345678901234');

            $token = Token::create(
                id: $tokenId->value(),
                userId: new UserId('018f47ac-10b5-7cde-8f67-890abcdef123'),
                plainTextToken: 'yet_another_token_long_enough_for_validation_1234567890',
                expiresAt: new DateTimeImmutable('+30 days')
            );

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tokens')->andReturn($mockTokenRepo);
            $mockTokenRepo->shouldReceive('findById')
                ->with($tokenId)
                ->andReturn($token);
            $mockTokenRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $command = new RevokeTokenCommand($mockUow);

            $command->execute($tokenId);

            expect(true)->toBeTrue();
        });
    });
});

