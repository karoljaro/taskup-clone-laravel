<?php

use App\Core\Domain\Entities\Task;
use App\Core\Domain\Enums\TaskStatus;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\VO\TaskId;
use App\Persistence\Eloquent\TaskEloquentModel;
use App\Persistence\Repositories\EloquentTaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('EloquentTaskRepository Integration Tests', function () {
    beforeEach(function () {
        $this->repository = new EloquentTaskRepository(new TaskEloquentModel());
    });

    describe('save()', function () {
        it('persists new task to database', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create($taskId, 'Buy groceries', 'Milk and eggs');

            $this->repository->save($task);

            $saved = TaskEloquentModel::query()->find($taskId);
            expect($saved)->not()->toBeNull()
                ->and($saved->title)->toBe('Buy groceries')
                ->and($saved->description)->toBe('Milk and eggs')
                ->and($saved->status)->toBe(TaskStatus::TODO);
        });

        it('updates existing task in database', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create($taskId, 'Original title', 'Original description');
            $this->repository->save($task);

            $task->update(title: 'Updated title', description: 'Updated description');
            $this->repository->save($task);

            $updated = TaskEloquentModel::query()->find($taskId);
            expect($updated->title)->toBe('Updated title')
                ->and($updated->description)->toBe('Updated description');
        });

        it('persists task with null description', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create($taskId, 'Task without description', null);

            $this->repository->save($task);

            $saved = TaskEloquentModel::query()->find($taskId);
            expect($saved->description)->toBe('');
        });

        it('persists task status changes', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create($taskId, 'Test task', 'Description');
            $this->repository->save($task);

            $task->update(status: TaskStatus::IN_PROGRESS);
            $this->repository->save($task);

            $saved = TaskEloquentModel::query()->find($taskId);
            expect($saved->status)->toBe(TaskStatus::IN_PROGRESS);
        });

        it('preserves timestamps on save', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create($taskId, 'Test task', 'Description');

            $this->repository->save($task);

            $saved = TaskEloquentModel::query()->find($taskId);
            expect($saved->created_at)->not()->toBeNull()
                ->and($saved->updated_at)->not()->toBeNull();
        });
    });

    describe('getTaskById()', function () {
        it('retrieves task from database by ID', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create($taskId, 'Retrieve me', 'Test description');
            $this->repository->save($task);

            $retrieved = $this->repository->getTaskById(new TaskId($taskId));

            expect($retrieved)->toBeInstanceOf(Task::class)
                ->and($retrieved->getTitle())->toBe('Retrieve me')
                ->and($retrieved->getDescription())->toBe('Test description');
        });

        it('returns Task entity with correct data types', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create($taskId, 'Test', 'Description');
            $this->repository->save($task);

            $retrieved = $this->repository->getTaskById(new TaskId($taskId));

            expect($retrieved->getId())->toBeInstanceOf(TaskId::class)
                ->and($retrieved->getTitle())->toBeString()
                ->and($retrieved->getStatus())->toBeInstanceOf(TaskStatus::class)
                ->and($retrieved->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class)
                ->and($retrieved->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class);
        });

        it('throws TaskNotFoundException when task does not exist', function () {
            $nonExistentId = new TaskId('00000000-0000-0000-0000-000000000000');

            expect(fn() => $this->repository->getTaskById($nonExistentId))
                ->toThrow(TaskNotFoundException::class);
        });

        it('retrieves task with updated status', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create($taskId, 'Test', 'Description');
            $task->update(status: TaskStatus::IN_PROGRESS);
            $this->repository->save($task);

            $retrieved = $this->repository->getTaskById(new TaskId($taskId));

            expect($retrieved->getStatus())->toBe(TaskStatus::IN_PROGRESS);
        });
    });

    describe('getAllTasks()', function () {
        it('retrieves all tasks from database', function () {
            $task1 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'Task 1', 'Description 1');
            $task2 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d480', 'Task 2', 'Description 2');
            $task3 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d481', 'Task 3', 'Description 3');

            $this->repository->save($task1);
            $this->repository->save($task2);
            $this->repository->save($task3);

            $allTasks = $this->repository->getAllTasks();

            expect($allTasks)->toHaveCount(3)
                ->and($allTasks[0])->toBeInstanceOf(Task::class)
                ->and($allTasks[0]->getTitle())->toBe('Task 1')
                ->and($allTasks[1]->getTitle())->toBe('Task 2')
                ->and($allTasks[2]->getTitle())->toBe('Task 3');
        });

        it('returns empty array when no tasks exist', function () {
            $allTasks = $this->repository->getAllTasks();

            expect($allTasks)->toBeArray()
                ->and($allTasks)->toBeEmpty();
        });

        it('returns all tasks as domain entities', function () {
            $task1 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'Task 1', null);
            $task2 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d480', 'Task 2', 'Description 2');

            $this->repository->save($task1);
            $this->repository->save($task2);

            $allTasks = $this->repository->getAllTasks();

            expect($allTasks)->toHaveCount(2);
            foreach ($allTasks as $task) {
                expect($task)->toBeInstanceOf(Task::class);
            }
        });

        it('returns tasks with correct statuses', function () {
            $task1 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'Task 1', null);
            $task2 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d480', 'Task 2', null);
            $task2->update(status: TaskStatus::IN_PROGRESS);

            $this->repository->save($task1);
            $this->repository->save($task2);

            $allTasks = $this->repository->getAllTasks();

            expect($allTasks[0]->getStatus())->toBe(TaskStatus::TODO)
                ->and($allTasks[1]->getStatus())->toBe(TaskStatus::IN_PROGRESS);
        });
    });

    describe('deleteByTaskId()', function () {
        it('deletes task from database', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create($taskId, 'Delete me', 'Description');
            $this->repository->save($task);

            $this->repository->deleteByTaskId(new TaskId($taskId));

            $deleted = TaskEloquentModel::query()->find($taskId);
            expect($deleted)->toBeNull();
        });

        it('throws TaskNotFoundException when trying to delete non-existent task', function () {
            $nonExistentId = new TaskId('00000000-0000-0000-0000-000000000000');

            expect(fn() => $this->repository->deleteByTaskId($nonExistentId))
                ->toThrow(TaskNotFoundException::class);
        });

        it('only deletes specified task', function () {
            $task1 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'Task 1', null);
            $task2 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d480', 'Task 2', null);

            $this->repository->save($task1);
            $this->repository->save($task2);

            $this->repository->deleteByTaskId(new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479'));

            $remaining = $this->repository->getAllTasks();

            expect($remaining)->toHaveCount(1)
                ->and($remaining[0]->getTitle())->toBe('Task 2');
        });
    });

    describe('round trip', function () {
        it('task data survives save and retrieve cycle', function () {
            $originalTaskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $originalTask = Task::create(
                $originalTaskId,
                'Original task',
                'Original description'
            );
            $originalTask->update(status: TaskStatus::IN_PROGRESS);

            $this->repository->save($originalTask);
            $retrieved = $this->repository->getTaskById(new TaskId($originalTaskId));

            expect($retrieved->getId()->value())->toBe($originalTaskId)
                ->and($retrieved->getTitle())->toBe('Original task')
                ->and($retrieved->getDescription())->toBe('Original description')
                ->and($retrieved->getStatus())->toBe(TaskStatus::IN_PROGRESS);
        });
    });
});

