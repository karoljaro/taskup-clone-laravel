<?php
/** @noinspection PhpPropertyHookInspection */

namespace App\core\domain\Entities;

use App\core\domain\Enums\TaskStatus;
use App\core\domain\VO\TaskId;
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

    public static function create(string $id, string $title, string $description): self
    {
        return new self(new TaskId($id), $title, $description);
    }

    public function update(
        ?string $title = null,
        ?string $description = null,
        ?TaskStatus $status = null
    ): void
    {
        $changeDetected = false;

        if ($title !== null && $this->title !== $title) {
            $this->title = $title;
            $changeDetected = true;
        }

        if ($description !== null && $this->description !== $description) {
            $this->description = $description;
            $changeDetected = true;
        }

        if ($status !== null && $this->status !== $status) {
            $this->status = $status;
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
