<?php

namespace App\Core\Application\Queries;

use App\Core\Domain\Entities\User;
use App\Core\Domain\Exceptions\UserNotFoundException;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\Email;

/**
 * Retrieves a user by email address.
 */
final readonly class GetUserByEmailQuery
{
    public function __construct(
        private UserRepository $userRepo
    ) {}

    /**
     * Executes user retrieval by email.
     *
     * @throws UserNotFoundException
     */
    public function execute(Email $email): User
    {
        return $this->userRepo->findByEmail($email);
    }
}

