<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\UpdateUserInputDTO;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\UserId;

/**
 * Updates an existing user's information.
 */
final readonly class UpdateUserCommand
{
    public function __construct(
        private UserRepository $userRepo,
    ) {}

    /**
     * Executes user update with optional fields.
     */
    public function execute(UserId $userId, UpdateUserInputDTO $input): void
    {
        $user = $this->userRepo->findById($userId);

        $user->update(
            username: $input->username,
            email: $input->email,
            plainPassword: $input->plainPassword,
        );
    }
}
