<?php

namespace App\Core\Application\Queries;

use App\Core\Domain\Entities\User;
use App\Core\Domain\Exceptions\UserNotFoundException;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\UserId;

/**
 * Retrieves a user by their ID.
 */
final readonly class GetUserByIdQuery
{
    public function __construct(
        private UserRepository $userRepo
    ) {}

    /**
     * Executes user retrieval by ID.
     *
     * @throws UserNotFoundException
     */
    public function execute(UserId $userId): User
    {
        return $this->userRepo->findById($userId);
    }
}

