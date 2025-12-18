<?php

use App\core\application\Queries\GetAllTaskUseCase;
use App\core\domain\Repositories\TaskRepository;
use App\core\domain\Entities\Task;
use App\core\domain\Enums\TaskStatus;

describe('GetAllTaskUseCase', function () {
    describe('execute()', function () {
        it('returns all tasks from repository', function () {
            $mockTaskRepo = mock(TaskRepository::class);

            $task1 = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'First Task', 'Description 1');
            $task2 = Task::create('a87ac10b-58cc-4372-a567-0e02b2c3d480', 'Second Task', 'Description 2');
            $task2->update(status: TaskStatus::IN_PROGRESS);

            $tasks = [$task1, $task2];

            $mockTaskRepo->shouldReceive('getAllTasks')
                ->once()
                ->andReturn($tasks);

            $useCase = new GetAllTaskUseCase($mockTaskRepo);
            $result = $useCase->execute();

            expect($result)->toHaveCount(2)
                ->and($result[0]->getTitle())->toBe('First Task')
                ->and($result[1]->getTitle())->toBe('Second Task')
                ->and($result[0]->getStatus())->toBe(TaskStatus::TODO)
                ->and($result[1]->getStatus())->toBe(TaskStatus::IN_PROGRESS);
        });

        it('returns empty array when no tasks exist', function () {
            $mockTaskRepo = mock(TaskRepository::class);

            $mockTaskRepo->shouldReceive('getAllTasks')
                ->once()
                ->andReturn([]);

            $useCase = new GetAllTaskUseCase($mockTaskRepo);
            $result = $useCase->execute();

            expect($result)->toBeArray()
                ->and($result)->toBeEmpty();
        });

        it('returns list of tasks of correct type', function () {
            $mockTaskRepo = mock(TaskRepository::class);

            $task = Task::create('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'Test Task', null);

            $mockTaskRepo->shouldReceive('getAllTasks')
                ->once()
                ->andReturn([$task]);

            $useCase = new GetAllTaskUseCase($mockTaskRepo);
            $result = $useCase->execute();

            expect($result[0])->toBeInstanceOf(Task::class);
        });
    });
});

