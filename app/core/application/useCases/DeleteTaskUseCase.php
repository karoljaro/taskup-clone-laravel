<?php

namespace App\core\application\useCases;

use App\core\domain\Repositories\TaskRepository;
use App\core\domain\VO\TaskId;
use App\core\domain\Exceptions\TaskNotFoundException;

/**
 * DeleteTaskUseCase - handles task deletion by task ID
 */
readonly class DeleteTaskUseCase
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
