<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\CreateTokenInputDTO;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\Token;
use App\Core\Domain\Exceptions\InvalidPlainTextTokenException;
use App\Core\Domain\Exceptions\InvalidTokenTimestampException;
use App\Core\Domain\Repositories\TokenRepository;

/**
 * Create a new token for a user.
 */
final readonly class CreateTokenCommand
{
    public function __construct(
        private TokenRepository $tokenRepo,
        private IdGenerator $idGenerator
    ) {}

    /**
     * Execute the command to create a new token.
     *
     * @param CreateTokenInputDTO $input The token creation data
     * @return Token The created token entity
     * @throws InvalidTokenTimestampException If expiration time is invalid
     * @throws InvalidPlainTextTokenException If plain text token is invalid
     */
    public function execute(CreateTokenInputDTO $input): Token {
        $genTokenId = $this->idGenerator->generate();

        $token = Token::create(
            id: $genTokenId,
            userId: $input->userId,
            plainTextToken: $input->plainTextToken,
            expiresAt: $input->expiresAt
        );

        $this->tokenRepo->save($token);

        return $token;
    }
}
