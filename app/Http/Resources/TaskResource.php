<?php

namespace App\Http\Resources;

use App\Core\Domain\Entities\Task;
use JsonSerializable;

/**
 * TaskResource - Serializes Task entity to HTTP JSON response
 */
final readonly class TaskResource implements JsonSerializable
{
    public function __construct(
        private Task $task
    ) {}

    public static function from(Task $task): self
    {
        return new self($task);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->task->getId()->value(),
            'title' => $this->task->getTitle(),
            'description' => $this->task->getDescription(),
            'status' => $this->task->getStatus()->value,
            'created_at' => $this->task->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->task->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}

