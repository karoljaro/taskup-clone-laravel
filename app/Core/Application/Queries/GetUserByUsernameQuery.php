<?php

namespace App\Core\Application\Queries;

use App\Core\Domain\Entities\User;
use App\Core\Domain\Exceptions\UserNotFoundException;
use App\Core\Domain\Repositories\UserRepository;

/**
 * Retrieves a user by username.
 */
final readonly class GetUserByUsernameQuery
{
    public function __construct(
        private UserRepository $userRepo
    ) {}

    /**
     * Executes user retrieval by username.
     *
     * @throws UserNotFoundException
     */
    public function execute(string $username): User
    {
        return $this->userRepo->findByUsername($username);
    }
}

