<?php

namespace App\core\domain\Validation;

use App\core\domain\Entities\Task;
use App\core\domain\Enums\TaskStatus;
use App\core\domain\Exceptions\InvalidStatusException;

class TaskInvariantValidation
{
    public static function validateCreateProps(string $id, string $title, ?string $description): void
    {
        SharedBusinessValidation::validateId($id);
        TaskBusinessValidation::validateTitle($title);

        if ($description !== null) {
            TaskBusinessValidation::validateDescription($description);
        }
    }

    public static function validateUpdateProps(
        string $title,
        string $description,
        TaskStatus $status
    ): void
    {
        TaskBusinessValidation::validateTitle($title);
        TaskBusinessValidation::validateDescription($description);
        TaskBusinessValidation::validateStatus($status);
    }

    public static function validateCreatedTask(Task $task) :void
    {
        SharedBusinessValidation::validateId($task->getId()->value());
        TaskBusinessValidation::validateTitle($task->getTitle());
        TaskBusinessValidation::validateDescription($task->getDescription());
        SharedBusinessValidation::validateTimeStamp($task->getCreatedAt());
        SharedBusinessValidation::validateTimeStamp($task->getUpdatedAt());
        SharedBusinessValidation::validateUpdatedAt($task->getCreatedAt(), $task->getUpdatedAt());

        if ($task->getStatus() !== TaskStatus::TODO) {
            throw new InvalidStatusException("Newly created task must have status TODO.");
        }
    }
}
