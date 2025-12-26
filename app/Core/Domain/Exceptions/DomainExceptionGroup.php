<?php

namespace App\Core\Domain\Exceptions;

enum DomainExceptionGroup: string
{
    case NOT_FOUND = 'not_found';
    case INVALID_INPUT = 'invalid_input';
    case CONFLICT = 'conflict';
}
