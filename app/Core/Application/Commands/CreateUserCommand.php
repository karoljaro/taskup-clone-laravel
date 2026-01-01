<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\CreateUserInputDTO;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\User;
use App\Core\Domain\Repositories\UserRepository;

/**
 * Creates a new user in the system.
 */
final readonly class CreateUserCommand
{
    public function __construct(
        private UserRepository $userRepo,
        private IdGenerator $idGenerator
    ) {}

    /**
     * Executes user creation with generated ID.
     */
    public function execute(CreateUserInputDTO $input): User {
        $genUserId = $this->idGenerator->generate();

        $user = User::create(
            id: $genUserId,
            username: $input->username,
            email: $input->email,
            plainPassword: $input->plainPassword
        );

        $this->userRepo->save($user);

        return $user;
    }
}
