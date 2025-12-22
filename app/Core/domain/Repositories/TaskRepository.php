<?php

namespace App\Core\domain\Repositories;

use App\Core\domain\Entities\Task;
use App\Core\domain\Exceptions\TaskNotFoundException;
use App\Core\domain\VO\TaskId;

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
