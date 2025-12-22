<?php

namespace App\Core\application\Commands;

use App\Core\application\DTOs\UpdateTaskInputDTO;
use App\Core\domain\Entities\Task;
use App\Core\domain\Exceptions\TaskNotFoundException;
use App\Core\domain\Repositories\TaskRepository;
use App\Core\domain\VO\TaskId;

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
