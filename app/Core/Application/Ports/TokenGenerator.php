<?php

namespace App\Core\Application\Ports;

use App\Core\Domain\VO\UserId;
use DateTimeImmutable;
use Exception;

/**
 * Token Generator port.
 * Responsible for generating plain text tokens for users with expiration dates.
 */
interface TokenGenerator
{
    /**
     * Generates a plain text token for the given user with an expiration date.
     *
     * @params UserId $userId The ID of the user for whom the token is generated.
     * @param DateTimeImmutable $expiresAt The expiration date and time of the token.
     * @returns string Plain text token.
     * @throws Exception If user no found ro generation fails
     */
    public function generate(
        UserId $userId,
        DateTimeImmutable $expiresAt
    ): string;
}
