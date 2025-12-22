<?php

namespace App\Core\Application\Queries;

use App\Core\Domain\Entities\Task;
use App\Core\Domain\Repositories\TaskRepository;

final readonly class GetAllTaskQuery
{
    public function __construct(
        private TaskRepository $taskRepo
    ) {}

    /**
     * @return list<Task>
     */
    public function execute(): array
    {
        return $this->taskRepo->getAllTasks();
    }
}
