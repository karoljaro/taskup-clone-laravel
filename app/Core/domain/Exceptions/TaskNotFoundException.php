<?php

namespace App\Core\domain\Exceptions;

use App\Core\domain\VO\TaskId;
use Exception;

class TaskNotFoundException extends Exception
{
    public function __construct(TaskId $taskId)
    {
        parent::__construct("Task with ID {$taskId->value()} not found");
    }
}
