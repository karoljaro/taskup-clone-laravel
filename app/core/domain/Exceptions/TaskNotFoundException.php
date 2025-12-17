<?php

namespace App\core\domain\Exceptions;

use App\core\domain\VO\TaskId;
use Exception;
class TaskNotFoundException extends Exception
{
    public function __construct(TaskId $taskId)
    {
        parent::__construct("Task with ID {$taskId->value()} not found");
    }
}
