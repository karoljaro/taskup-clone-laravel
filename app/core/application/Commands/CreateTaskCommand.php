<?php

namespace App\core\application\Commands;

use App\core\application\DTOs\CreateTaskInputDTO;
use App\core\application\Shared\IdGenerator;
use App\core\domain\Entities\Task;
use App\core\domain\Repositories\TaskRepository;

/**
 * CreateTaskCommand - handles task creation
 */
final readonly class CreateTaskCommand
{
    public function __construct(
        private TaskRepository $taskRepo,
        private IdGenerator $idGenerator
    ) {}

    /**
     * Creates a new task
     *
     * @param CreateTaskInputDTO $input task data
     * @return Task created task
     */
    public function execute(CreateTaskInputDTO $input): Task {
        $genTaskId = $this->idGenerator->generate();

        $task = Task::create(
            $genTaskId,
            $input->title,
            $input->description
        );

        $this->taskRepo->save($task);

        return $task;
    }
}
