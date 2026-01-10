<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\UpdateTaskInputDTO;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Entities\Task;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\VO\TaskId;
use Throwable;

/**
 * UpdateTaskCommand - handles task updating.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class UpdateTaskCommand
{
    public function __construct(
        private UnitOfWork $uow,
    ) {}

    /**
     * Updates an existing task.
     * Rolls back on any failure.
     *
     * @param TaskId $taskId The ID of task to update
     * @param UpdateTaskInputDTO $input Updated task data
     * @return Task The updated task
     * @throws TaskNotFoundException If task not found
     * @throws Throwable If update fails
     */
    public function execute(TaskId $taskId, UpdateTaskInputDTO $input): Task {
        try {
            $this->uow->begin();

            $task = $this->uow->tasks()->getTaskById($taskId);

            $task->update(
                title: $input->title ?? $task->getTitle(),
                description: $input->description ?? $task->getDescription(),
                status: $input->status ?? $task->getStatus()
            );

            $this->uow->tasks()->save($task);

            $this->uow->commit();

            return $task;
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}
