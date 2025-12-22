<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\UpdateTaskInputDTO;
use App\Core\Domain\Entities\Task;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\Repositories\TaskRepository;
use App\Core\Domain\VO\TaskId;

/**
 * UpdateTaskCommand - handles task updating
 */
final readonly class UpdateTaskCommand
{
    public function __construct(
        private TaskRepository $taskRepo,
    ) {}

    /**
     * @param TaskId $taskId
     * @param UpdateTaskInputDTO $input
     * @return Task
     * @throws TaskNotFoundException
     */
    public function execute(TaskId $taskId, UpdateTaskInputDTO $input): Task {
        $task = $this->taskRepo->getTaskById($taskId);

        $task->update(
            title: $input->title ?? $task->getTitle(),
            description: $input->description ?? $task->getDescription(),
            status: $input->status ?? $task->getStatus()
        );

        $this->taskRepo->save($task);

        return $task;
    }
}
