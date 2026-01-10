<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\RegisterInputDTO;
use App\Core\Application\DTOs\RegisterOutputDTO;
use App\Core\Application\Ports\TokenGenerator;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\Token;
use App\Core\Domain\Entities\User;
use Throwable;

/**
 * RegisterCommand - handles user registration.
 * Creates a new user account and issues an initial access token.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class RegisterCommand
{
    public function __construct(
        private UnitOfWork $uow,
        private IdGenerator $idGenerator,
        private TokenGenerator $tokenGenerator
    ) {}

    /**
     * Executes user registration.
     * Creates user account and issues an access token in a single transaction.
     * Rolls back on any failure.
     *
     * @param RegisterInputDTO $input Registration data (username, email, plainPassword)
     * @return RegisterOutputDTO User, token, and plain text token for client
     * @throws Throwable If registration fails
     */
    public function execute(RegisterInputDTO $input): RegisterOutputDTO
    {
        try {
            $this->uow->begin();

            // 1. Create User entity (domain handles password hashing)
            $userId = $this->idGenerator->generate();
            $user = User::create(
                id: $userId,
                username: $input->username,
                email: $input->email,
                plainPassword: $input->plainPassword
            );

            // Save user to repository
            $this->uow->users()->save($user);

            // 2. Generate and create Token entity
            $expiresAt = now()->addDays(30)->toDateTimeImmutable();
            $plainTextToken = $this->tokenGenerator->generate($user->getId(), $expiresAt);

            $token = Token::create(
                id: $this->idGenerator->generate(),
                userId: $user->getId(),
                plainTextToken: $plainTextToken,
                expiresAt: $expiresAt
            );

            // Save token to repository
            $this->uow->tokens()->save($token);

            // 3. Commit transaction
            $this->uow->commit();

            // Return output DTO with plain text token (only transmitted once!)
            return new RegisterOutputDTO(
                user: $user,
                token: $token,
                plainTextToken: $plainTextToken
            );
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}

