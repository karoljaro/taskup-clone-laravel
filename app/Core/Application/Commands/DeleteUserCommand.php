<?php

namespace App\Core\Application\Commands;

use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Exceptions\UserNotFoundException;
use App\Core\Domain\VO\UserId;
use Throwable;

/**
 * Handles user deletion.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class DeleteUserCommand
{
    public function __construct(
        private UnitOfWork $uow
    ) {}

    /**
     * Deletes a user by ID.
     * Rolls back on any failure.
     *
     * @param UserId $userId The ID of user to delete
     * @return void
     * @throws UserNotFoundException If user not found
     * @throws Throwable If deletion fails
     */
    public function execute(UserId $userId): void
    {
        try {
            $this->uow->begin();

            $this->uow->users()->deleteById($userId);

            $this->uow->commit();
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}

