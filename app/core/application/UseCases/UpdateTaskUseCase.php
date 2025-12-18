<?php

namespace App\core\application\UseCases;

use App\core\application\DTOs\UpdateTaskInputDTO;
use App\core\domain\Entities\Task;
use App\core\domain\Exceptions\TaskNotFoundException;
use App\core\domain\Repositories\TaskRepository;
use App\core\domain\VO\TaskId;

/**
 * UpdateTaskUseCase - handles task updating
 */
final readonly class UpdateTaskUseCase
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
