<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\LoginInputDTO;
use App\Core\Application\DTOs\LoginOutputDTO;
use App\Core\Application\Ports\TokenGenerator;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\Token;
use App\Core\Domain\Exceptions\InvalidCredentialsException;
use App\Core\Domain\VO\Email;
use Throwable;

/**
 * LoginCommand - handles user authentication.
 * Authenticates user by email and password, then issues an access token.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class LoginCommand
{
    public function __construct(
        private UnitOfWork $uow,
        private IdGenerator $idGenerator,
        private TokenGenerator $tokenGenerator
    ) {}

    /**
     * Executes user login.
     * Verifies credentials and issues an access token in a single transaction.
     * Rolls back on any failure.
     *
     * @param LoginInputDTO $input Login data (email, plainPassword, rememberMe)
     * @return LoginOutputDTO User, token, and plain text token for client
     * @throws InvalidCredentialsException If email not found or password incorrect
     * @throws Throwable If login fails
     */
    public function execute(LoginInputDTO $input): LoginOutputDTO
    {
        try {
            $this->uow->begin();

            // 1. Retrieve user by email
            $email = Email::create($input->email);
            $user = $this->uow->users()->findByEmail($email);

            // 2. Verify password using HashedPassword VO
            if (!$user->getPassword()->verify($input->plainPassword)) {
                throw new InvalidCredentialsException('Invalid email or password');
            }

            // 3. Generate and create Token entity
            // Token expiration: 30 days if remember me, otherwise 24 hours
            $expiresAt = $input->rememberMe
                ? now()->addDays(30)->toDateTimeImmutable()
                : now()->addHours(24)->toDateTimeImmutable();

            $plainTextToken = $this->tokenGenerator->generate($user->getId(), $expiresAt);

            $token = Token::create(
                id: $this->idGenerator->generate(),
                userId: $user->getId(),
                plainTextToken: $plainTextToken,
                expiresAt: $expiresAt
            );

            // 4. Save token to repository
            $this->uow->tokens()->save($token);

            // 5. Commit transaction
            $this->uow->commit();

            // Return output DTO with plain text token (only transmitted once!)
            return new LoginOutputDTO(
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

