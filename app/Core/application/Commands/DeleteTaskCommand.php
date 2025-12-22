<?php

namespace App\Core\application\Commands;

use App\Core\domain\Exceptions\TaskNotFoundException;
use App\Core\domain\Repositories\TaskRepository;
use App\Core\domain\VO\TaskId;

/**
 * DeleteTaskCommand - handles task deletion by task ID
 */
final readonly class DeleteTaskCommand
{
    public function __construct(
        private TaskRepository $taskRepo,
    ) {}

    /**
     * @param TaskId $id
     * @throws TaskNotFoundException
     * @return void
     */
    public function execute(TaskId $id): void {
        $this->taskRepo->deleteByTaskId($id);
    }
}
