<?php

namespace App\Persistence\Mappers;

use App\Core\Domain\Entities\User;
use App\Core\Domain\VO\UserId;
use App\Persistence\Eloquent\UserEloquentModel;
use DateTimeImmutable;

class UserMapper
{
    public static function toDomain(UserEloquentModel $eloquentModel) {
        return User::reconstruct(
            id: new UserId($eloquentModel->id),
            username: $eloquentModel->username,
            email: $eloquentModel->email,
            hashedPassword: $eloquentModel->password,
            createdAt: new DateTimeImmutable($eloquentModel->created_at),
            updatedAt: new DateTimeImmutable($eloquentModel->updated_at),
            emailVerified: $eloquentModel->email_verified_at !== null,
            verifiedAt: $eloquentModel->email_verified_at
                ? new DateTimeImmutable($eloquentModel->email_verified_at)
                : null,
        );
    }
}
