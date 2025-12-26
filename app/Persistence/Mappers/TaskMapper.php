<?php

namespace App\Persistence\Mappers;

use App\Core\Domain\Entities\Task;
use App\Core\Domain\VO\TaskId;
use App\Persistence\Eloquent\TaskEloquentModel;
use DateTimeImmutable;

/**
 * Maps between Eloquent persistence layer and domain Entity
 */
class TaskMapper
{
    /**
     * Convert Eloquent model to domain Entity
     *
     * @param TaskEloquentModel $eloquentModel
     * @return Task
     */
    public static function toDomain(TaskEloquentModel $eloquentModel): Task
    {
        return Task::reconstruct(
            id: new TaskId($eloquentModel->id),
            title: $eloquentModel->title,
            description: $eloquentModel->description,
            status: $eloquentModel->status,
            createdAt: DateTimeImmutable::createFromMutable($eloquentModel->created_at),
            updatedAt: DateTimeImmutable::createFromMutable($eloquentModel->updated_at),
        );
    }

    /**
     * Convert domain Entity to persistence array
     *
     * @param Task $task
     * @return array
     */
    public static function toPersistence(Task $task): array
    {
        return [
            'id' => $task->getId()->value(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus()->value,
            'created_at' => $task->getCreatedAt(),
            'updated_at' => $task->getUpdatedAt(),
        ];
    }
}
