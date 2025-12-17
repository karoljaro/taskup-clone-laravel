<?php

namespace App\core\domain\Repositories;

use App\core\domain\Entities\Task;
use App\core\domain\VO\TaskId;
use App\core\domain\Exceptions\TaskNotFoundException;

interface TaskRepository
{
    /**
     * @param TaskId $id
     * @throws TaskNotFoundException If the task with the given ID does not exist
     * @return Task
     */
    public function getTaskById(TaskId $id): Task;
    /**
     * @return list<Task> Array can contain zero or more Task objects
     */
    public function getAllTasks(): array;
    /**
     * Persists a new or updated Task to the repository
     * @param Task $task
     * @return void
     */
    public function save(Task $task): void;
    /**
     * @param TaskId $id
     * @throws TaskNotFoundException If the task with the given ID does not exist
     * @return void
     */
    public function deleteByTaskId(TaskId $id): void;
}
