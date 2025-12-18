<?php

namespace App\core\application\Queries;

use App\core\domain\Entities\Task;
use App\core\domain\Repositories\TaskRepository;

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
