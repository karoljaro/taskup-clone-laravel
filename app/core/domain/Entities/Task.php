<?php
/** @noinspection PhpPropertyHookInspection */

namespace App\core\domain\Entities;

use App\core\domain\Enums\TaskStatus;
use DateTimeImmutable;

final class Task
{
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private TaskStatus $status;
    public function __construct(
        private readonly string $id, // TODO: Create brand ID
        private string $title,
        private string $description
    ) {
        // TODO: Create validators and type validations for setters

        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;

        $this->status = TaskStatus::TODO;
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

//    ==========================[ GETTERS ]==========================

    public function getId(): string
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

//    ==========================[ SETTERS ]==========================

    public function setTitle(string $title): void
    {
        if ($this->title === $title) {
            return;
        }

        $this->title = $title;
        $this->touch();
    }

    public function setDescription(string $description): void
    {
        if ($this->description === $description) {
            return;
        }

        $this->description = $description;
        $this->touch();
    }

    public function setStatus(TaskStatus $status): void
    {
        if ($this->status === $status) {
            return;
        }

        $this->status = $status;
        $this->touch();
    }
}
