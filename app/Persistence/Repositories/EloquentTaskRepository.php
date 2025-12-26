<?php

namespace App\Persistence\Repositories;

use App\Core\Domain\Entities\Task;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\Repositories\TaskRepository;
use App\Core\Domain\VO\TaskId;
use App\Persistence\Eloquent\TaskEloquentModel;
use App\Persistence\Mappers\TaskMapper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Eloquent implementation of TaskRepository
 */
readonly class EloquentTaskRepository implements TaskRepository
{
    public function __construct(
        private TaskEloquentModel $model
    )
    {}

    /**
     * Persists a new or updated Task
     *
     * @param Task $task
     * @return void
     */
    public function save(Task $task): void
    {
        $data = TaskMapper::toPersistence($task);
        $this->model::query()->updateOrCreate(
            ['id' => $data['id']],
            $data
        );
    }

    /**
     * Retrieves a task by ID
     *
     * @param TaskId $id
     * @return Task
     * @throws TaskNotFoundException
     */
    public function getTaskById(TaskId $id): Task
    {
        try {
            $eloquentModel = $this->model::query()->findOrFail($id->value());
            return TaskMapper::toDomain($eloquentModel);
        } catch (ModelNotFoundException) {
            throw new TaskNotFoundException($id);
        }
    }

    /**
     * Retrieves all tasks
     *
     * @return array<Task>
     */
    public function getAllTasks(): array
    {
        return $this->model::all()->map(fn (TaskEloquentModel $model) => TaskMapper::toDomain($model))
            ->toArray();
    }

    /**
     * Deletes a task by ID
     *
     * @param TaskId $id
     * @return void
     * @throws TaskNotFoundException
     */
    public function deleteByTaskId(TaskId $id): void
    {
        try {
            $eloquentModel = $this->model::query()->findOrFail($id->value());
            $eloquentModel->delete();
        } catch (ModelNotFoundException) {
            throw new TaskNotFoundException($id);
        }
    }
}
