<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\CreateUserInputDTO;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\User;
use Throwable;

/**
 * Creates a new user in the system.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class CreateUserCommand
{
    public function __construct(
        private UnitOfWork $uow,
        private IdGenerator $idGenerator
    ) {}

    /**
     * Executes user creation with generated ID.
     * Rolls back on any failure.
     *
     * @param CreateUserInputDTO $input User creation data
     * @return User The created user entity
     * @throws Throwable If user creation fails
     */
    public function execute(CreateUserInputDTO $input): User {
        try {
            $this->uow->begin();

            $genUserId = $this->idGenerator->generate();

            $user = User::create(
                id: $genUserId,
                username: $input->username,
                email: $input->email,
                plainPassword: $input->plainPassword
            );

            $this->uow->users()->save($user);

            $this->uow->commit();

            return $user;
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}
