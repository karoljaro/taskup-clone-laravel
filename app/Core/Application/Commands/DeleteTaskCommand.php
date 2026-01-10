<?php

namespace App\Core\Application\Commands;

use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\VO\TaskId;
use Throwable;

/**
 * DeleteTaskCommand - handles task deletion by task ID.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class DeleteTaskCommand
{
    public function __construct(
        private UnitOfWork $uow,
    ) {}

    /**
     * Deletes a task by ID.
     * Rolls back on any failure.
     *
     * @param TaskId $id The ID of task to delete
     * @return void
     * @throws TaskNotFoundException If task not found
     * @throws Throwable If deletion fails
     */
    public function execute(TaskId $id): void {
        try {
            $this->uow->begin();

            $this->uow->tasks()->deleteByTaskId($id);

            $this->uow->commit();
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}
