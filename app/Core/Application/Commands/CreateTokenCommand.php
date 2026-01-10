<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\CreateTokenInputDTO;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\Token;
use App\Core\Domain\Exceptions\InvalidPlainTextTokenException;
use App\Core\Domain\Exceptions\InvalidTokenTimestampException;
use Throwable;

/**
 * Create a new token for a user.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class CreateTokenCommand
{
    public function __construct(
        private UnitOfWork $uow,
        private IdGenerator $idGenerator
    ) {}

    /**
     * Execute the command to create a new token.
     * Rolls back on any failure.
     *
     * @param CreateTokenInputDTO $input The token creation data
     * @return Token The created token entity
     * @throws InvalidTokenTimestampException If expiration time is invalid
     * @throws InvalidPlainTextTokenException If plain text token is invalid
     * @throws Throwable If token persistence fails
     */
    public function execute(CreateTokenInputDTO $input): Token {
        try {
            $this->uow->begin();

            $token = Token::create(
                id: $this->idGenerator->generate(),
                userId: $input->userId,
                plainTextToken: $input->plainTextToken,
                expiresAt: $input->expiresAt
            );

            $this->uow->tokens()->save($token);

            $this->uow->commit();

            return $token;
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}
