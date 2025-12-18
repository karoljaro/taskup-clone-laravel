<?php

use App\core\application\Commands\UpdateTaskCommand;
use App\core\application\DTOs\UpdateTaskInputDTO;
use App\core\domain\Repositories\TaskRepository;
use App\core\domain\Entities\Task;
use App\core\domain\Enums\TaskStatus;
use App\core\domain\Exceptions\TaskNotFoundException;
use App\core\domain\VO\TaskId;

describe('UpdateTaskCommand', function () {
    describe('execute()', function () {
        it('updates task title only', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $existingTask = Task::create(
                id: $taskId->value(),
                title: 'Old Title',
                description: 'Description'
            );

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andReturn($existingTask);
            $mockTaskRepo->shouldReceive('save')
                ->once();

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(title: 'New Title');

            $result = $useCase->execute($taskId, $input);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getTitle())->toBe('New Title')
                ->and($result->getDescription())->toBe('Description');
        });

        it('updates task description only', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $existingTask = Task::create(
                id: $taskId->value(),
                title: 'Title',
                description: 'Old Description'
            );

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andReturn($existingTask);
            $mockTaskRepo->shouldReceive('save')
                ->once();

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(description: 'New Description');

            $result = $useCase->execute($taskId, $input);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getTitle())->toBe('Title')
                ->and($result->getDescription())->toBe('New Description');
        });

        it('updates task status only', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $existingTask = Task::create(
                id: $taskId->value(),
                title: 'Title',
                description: 'Description'
            );

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andReturn($existingTask);
            $mockTaskRepo->shouldReceive('save')
                ->once();

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(status: TaskStatus::IN_PROGRESS);

            $result = $useCase->execute($taskId, $input);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getStatus())->toBe(TaskStatus::IN_PROGRESS);
        });

        it('updates multiple properties at once', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $existingTask = Task::create(
                id: $taskId->value(),
                title: 'Old Title',
                description: 'Old Description'
            );

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andReturn($existingTask);
            $mockTaskRepo->shouldReceive('save')
                ->once();

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(
                title: 'New Title',
                description: 'New Description',
                status: TaskStatus::COMPLETED
            );

            $result = $useCase->execute($taskId, $input);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getTitle())->toBe('New Title')
                ->and($result->getDescription())->toBe('New Description')
                ->and($result->getStatus())->toBe(TaskStatus::COMPLETED);
        });

        it('preserves old values when updating only some properties', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $existingTask = Task::create(
                id: $taskId->value(),
                title: 'Original Title',
                description: 'Original Description'
            );

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andReturn($existingTask);
            $mockTaskRepo->shouldReceive('save')
                ->once();

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(title: 'Updated Title');

            $result = $useCase->execute($taskId, $input);

            expect($result->getTitle())->toBe('Updated Title')
                ->and($result->getDescription())->toBe('Original Description')
                ->and($result->getStatus())->toBe(TaskStatus::TODO);
        });

        it('calls repository getTaskById with correct TaskId', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $existingTask = Task::create(
                id: $taskId->value(),
                title: 'Title',
                description: 'Description'
            );

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andReturn($existingTask);
            $mockTaskRepo->shouldReceive('save');

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(title: 'New Title');

            $useCase->execute($taskId, $input);
        });

        it('calls repository save exactly once', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $existingTask = Task::create(
                id: $taskId->value(),
                title: 'Title',
                description: 'Description'
            );

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->andReturn($existingTask);
            $mockTaskRepo->shouldReceive('save')
                ->once();

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(title: 'New Title');

            $useCase->execute($taskId, $input);
        });

        it('throws TaskNotFoundException when task does not exist', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andThrow(new TaskNotFoundException($taskId));

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(title: 'New Title');

            expect(fn() => $useCase->execute($taskId, $input))
                ->toThrow(TaskNotFoundException::class);
        });

        it('does not call save when task does not exist', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->with($taskId)
                ->once()
                ->andThrow(new TaskNotFoundException($taskId));
            $mockTaskRepo->shouldReceive('save')
                ->never();

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(title: 'New Title');

            try {
                $useCase->execute($taskId, $input);
            } catch (TaskNotFoundException) {
                // Expected
            }
        });

        it('returns updated task instance', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $existingTask = Task::create(
                id: $taskId->value(),
                title: 'Title',
                description: 'Description'
            );

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('getTaskById')
                ->andReturn($existingTask);
            $mockTaskRepo->shouldReceive('save');

            $useCase = new UpdateTaskCommand($mockTaskRepo);
            $input = new UpdateTaskInputDTO(title: 'Updated Title');

            $result = $useCase->execute($taskId, $input);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getId()->equals($taskId))->toBeTrue();
        });
    });
});

