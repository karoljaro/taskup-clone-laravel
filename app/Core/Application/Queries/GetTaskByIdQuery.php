<?php

namespace App\Core\Application\Queries;

use App\Core\Domain\Entities\Task;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\Repositories\TaskRepository;
use App\Core\Domain\VO\TaskId;

/**
 * GetTaskByIdQuery - retrieves a single task by its ID
 */
final readonly class GetTaskByIdQuery
{
    public function __construct(
        private TaskRepository $taskRepo
    ) {}

    /**
     * Retrieves a task by ID
     *
     * @param TaskId $id the task ID to retrieve
     * @return Task the task entity
     * @throws TaskNotFoundException if task not found
     */
    public function execute(TaskId $id): Task {
        return $this->taskRepo->getTaskById($id);
    }
}
