<?php

namespace App\Persistence\Repositories;

use App\Core\Domain\Entities\User;
use App\Core\Domain\Exceptions\UserNotFoundException;
use App\Core\Domain\Repositories\UserRepository;
use App\Core\Domain\VO\Email;
use App\Core\Domain\VO\UserId;
use App\Persistence\Eloquent\UserEloquentModel;
use App\Persistence\Mappers\UserMapper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class EloquentUserRepository implements UserRepository
{
    public function __construct(
        private UserEloquentModel $model,
    ) {}

    public function save(User $user): void
    {
        $userData = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail()->value(),
            'password' => $user->getPassword()->value(),
        ];

        $existingUser = $this->model::find($user->getId()->value());
        if ($existingUser && $existingUser->email !== $user->getEmail()->value()) {
            $userData['email_verified_at'] = null;
        }

        $this->model::updateOrCreate(
            ['id' => $user->getId()->value()],
            $userData
        );
    }

    public function findById(UserId $id): User
    {
        try {
            $eloquentUser = $this->model::findOrFail($id->value());
            return UserMapper::toDomain($eloquentUser);
        } catch (ModelNotFoundException) {
            throw new UserNotFoundException($id);
        }
    }

    public function findByEmail(Email $email): User
    {
        try {
            $eloquentUser = $this->model::where('email', $email->value())->firstOrFail();
            return UserMapper::toDomain($eloquentUser);
        } catch(ModelNotFoundException) {
            throw new UserNotFoundException($email->value());
        }
    }

    public function findByUsername(string $username): User
    {
        try {
            $eloquentUser = $this->model::where('username', $username)->firstOrFail();
            return UserMapper::toDomain($eloquentUser);
        } catch (ModelNotFoundException) {
            throw new UserNotFoundException($username);
        }
    }


    public function deleteById(UserId $id): void
    {
        try {
            $this->model::where('id', $id->value())->deleteOrFail();
        } catch (\Throwable) {
            throw new UserNotFoundException($id);
        }
    }
}
