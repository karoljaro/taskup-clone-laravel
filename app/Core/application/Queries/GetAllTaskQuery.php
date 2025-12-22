<?php

namespace App\Core\application\Queries;

use App\Core\domain\Entities\Task;
use App\Core\domain\Repositories\TaskRepository;

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
