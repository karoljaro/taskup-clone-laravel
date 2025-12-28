<?php

namespace App\Http\Exceptions;

use App\Core\Domain\Exceptions\DomainError;
use App\Core\Domain\Exceptions\DomainExceptionGroup;
use JsonException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

final class ExceptionToHttpMapper
{
    private const array HTTP_MAP = [
        DomainExceptionGroup::NOT_FOUND->value => SymfonyResponse::HTTP_NOT_FOUND,
        DomainExceptionGroup::INVALID_INPUT->value => SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY,
        DomainExceptionGroup::CONFLICT->value => SymfonyResponse::HTTP_CONFLICT
    ];

    /**
     * Map DomainError to HTTP Response
     *
     * @param Throwable $event
     * @return SymfonyResponse|null
     * @throws JsonException
     */
    public static function map(Throwable $event): ?SymfonyResponse
    {
        if (!$event instanceof DomainError) {
            return null;
        }

        $group = $event->group()->value;
        $status = self::HTTP_MAP[$group] ?? SymfonyResponse::HTTP_BAD_REQUEST;

        return new SymfonyResponse(
            content: json_encode([
                'error' => $group,
                'message' => $event->getMessage()
            ], JSON_THROW_ON_ERROR),
            status: $status,
            headers: ['Content-Type' => 'application/json']
        );
    }
}

