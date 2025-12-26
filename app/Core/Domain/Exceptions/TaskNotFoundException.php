<?php

namespace App\Core\Domain\Exceptions;

use App\Core\Domain\VO\TaskId;

final class TaskNotFoundException extends DomainError
{
    public function __construct(TaskId $taskId)
    {
        parent::__construct("Task with ID {$taskId->value()} not found");
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::NOT_FOUND;
    }
}
