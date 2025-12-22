<?php

namespace App\Core\Application\Commands;

use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\Repositories\TaskRepository;
use App\Core\Domain\VO\TaskId;

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
