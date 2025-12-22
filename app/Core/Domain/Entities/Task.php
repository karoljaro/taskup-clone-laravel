<?php
/** @noinspection PhpPropertyHookInspection */

namespace App\Core\Domain\Entities;

use App\Core\Domain\Enums\TaskStatus;
use App\Core\Domain\Validation\TaskInvariantValidation;
use App\Core\Domain\VO\TaskId;
use DateTimeImmutable;

final class Task
{
    private TaskStatus $status;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        private readonly TaskId $id,
        private string $title,
        private string $description
    )
    {
        $this->status = TaskStatus::TODO;

        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

//    ==========================[ FACTORY ] ==========================

    public static function create(string $id, string $title, ?string $description): self
    {
        TaskInvariantValidation::validateCreateProps(
            id: $id,
            title: $title,
            description: $description
        );
        $task =  new self(new TaskId($id), $title, $description ?? '');
        TaskInvariantValidation::validateCreatedTask($task);
        return $task;
    }

    public function update(
        ?string $title = null,
        ?string $description = null,
        ?TaskStatus $status = null
    ): void
    {
        $newTitle = $title ?? $this->title;
        $newDescription = $description ?? $this->description;
        $newStatus = $status ?? $this->status;

        TaskInvariantValidation::validateUpdateProps(
            title: $newTitle,
            description: $newDescription,
            status: $newStatus
        );

        $changeDetected = false;

        if ($newTitle !== $this->title) {
            $this->title = $newTitle;
            $changeDetected = true;
        }

        if ($newDescription !== $this->description) {
            $this->description = $newDescription;
            $changeDetected = true;
        }

        if ($newStatus !== $this->status) {
            $this->status = $newStatus;
            $changeDetected = true;
        }

        if ($changeDetected) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

//    ==========================[ GETTERS ]==========================

    public function getId(): TaskId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
