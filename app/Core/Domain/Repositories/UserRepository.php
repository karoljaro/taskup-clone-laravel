<?php

namespace App\Core\Domain\Repositories;

use App\Core\Domain\Entities\User;
use App\Core\Domain\VO\Email;
use App\Core\Domain\VO\UserId;

interface UserRepository
{
    public function save(User $user): void;
    public function findById(UserId $id): User;
    public function findByEmail(Email $email): User;
    public function findByUsername(string $username): User;
    public function deleteById(UserId $id): void;
}
