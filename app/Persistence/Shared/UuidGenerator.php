<?php

namespace App\Persistence\Shared;

use App\Core\Application\Shared\IdGenerator;
use Ramsey\Uuid\Uuid;

/**
 * UUID-based implementation of IdGenerator
 */
final class UuidGenerator implements IdGenerator
{
    /**
     * Generates a random UUID v7
     *
     * @return string UUID as string
     */
    public function generate(): string
    {
        return Uuid::uuid7()->toString();
    }
}

