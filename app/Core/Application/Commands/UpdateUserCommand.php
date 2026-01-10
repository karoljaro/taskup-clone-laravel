<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\UpdateUserInputDTO;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\VO\UserId;
use Throwable;

/**
 * Updates an existing user's information.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class UpdateUserCommand
{
    public function __construct(
        private UnitOfWork $uow,
    ) {}

    /**
     * Executes user update with optional fields.
     * Rolls back on any failure.
     *
     * @param UserId $userId The ID of user to update
     * @param UpdateUserInputDTO $input Updated user data
     * @return void
     * @throws Throwable If user not found or update fails
     */
    public function execute(UserId $userId, UpdateUserInputDTO $input): void
    {
        try {
            $this->uow->begin();

            $user = $this->uow->users()->findById($userId);

            $user->update(
                username: $input->username,
                email: $input->email,
                plainPassword: $input->plainPassword,
            );

            $this->uow->users()->save($user);

            $this->uow->commit();
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}
