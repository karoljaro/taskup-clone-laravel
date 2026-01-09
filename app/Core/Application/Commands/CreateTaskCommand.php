<?php

namespace App\Core\Application\Commands;

use App\Core\Application\DTOs\CreateTaskInputDTO;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\Task;
use Throwable;

/**
 * CreateTaskCommand - handles task creation.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class CreateTaskCommand
{
    public function __construct(
        private UnitOfWork $uow,
        private IdGenerator $idGenerator
    ) {}

    /**
     * Creates a new task.
     * Rolls back on any failure.
     *
     * @param CreateTaskInputDTO $input task data
     * @return Task created task
     * @throws Throwable If task creation fails
     */
    public function execute(CreateTaskInputDTO $input): Task {
        try {
            $this->uow->begin();

            $genTaskId = $this->idGenerator->generate();

            $task = Task::create(
                $genTaskId,
                $input->title,
                $input->description
            );

            $this->uow->tasks()->save($task);

            $this->uow->commit();

            return $task;
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}
