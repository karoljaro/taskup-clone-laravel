<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\CreateTaskInputDTO;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\Task;
use App\Core\Domain\Repositories\TaskRepository;

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
