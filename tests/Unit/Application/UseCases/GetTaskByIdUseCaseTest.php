<?php

use App\Core\Application\Queries\GetTaskByIdQuery;
use App\Core\Domain\Entities\Task;
use App\Core\Domain\Enums\TaskStatus;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\Repositories\TaskRepository;
use App\Core\Domain\VO\TaskId;

describe('GetTaskByIdQuery', function () {
    describe('execute()', function () {
        it('returns task when task exists', function () {
            $mockTaskRepo = mock(TaskRepository::class);

            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $task = Task::create($taskId->value(), 'Test Task', 'Test Description');

            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andReturn($task);

            $query = new GetTaskByIdQuery($mockTaskRepo);
            $result = $query->execute($taskId);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getTitle())->toBe('Test Task')
                ->and($result->getDescription())->toBe('Test Description')
                ->and($result->getStatus())->toBe(TaskStatus::TODO);
        });

        it('throws TaskNotFoundException when task does not exist', function () {
            $mockTaskRepo = mock(TaskRepository::class);

            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andThrow(new TaskNotFoundException($taskId));

            $query = new GetTaskByIdQuery($mockTaskRepo);

            expect(fn() => $query->execute($taskId))
                ->toThrow(TaskNotFoundException::class);
        });

        it('returns task with correct ID', function () {
            $mockTaskRepo = mock(TaskRepository::class);

            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $task = Task::create($taskId->value(), 'Task with ID', null);

            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andReturn($task);

            $query = new GetTaskByIdQuery($mockTaskRepo);
            $result = $query->execute($taskId);

            expect($result->getId()->value())->toBe($taskId->value());
        });

        it('returns task with updated status', function () {
            $mockTaskRepo = mock(TaskRepository::class);

            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $task = Task::create($taskId->value(), 'Updated Task', 'Description');
            $task->update(status: TaskStatus::IN_PROGRESS);

            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andReturn($task);

            $query = new GetTaskByIdQuery($mockTaskRepo);
            $result = $query->execute($taskId);

            expect($result->getStatus())->toBe(TaskStatus::IN_PROGRESS);
        });
    });
});

