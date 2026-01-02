<?php

namespace App\Core\Application\Commands;

use App\Core\Domain\Exceptions\UserNotFoundException;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\UserId;

/**
 * Handles user deletion.
 */
final readonly class DeleteUserCommand
{
    public function __construct(
        private UserRepository $userRepo
    ) {}

    /**
     * Deletes a user by ID.
     *
     * @throws UserNotFoundException
     */
    public function execute(UserId $userId): void
    {
        $this->userRepo->deleteById($userId);
    }
}

