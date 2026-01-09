<?php

use App\Core\Application\Commands\CreateTaskCommand;
use App\Core\Application\DTOs\CreateTaskInputDTO;
use App\Core\Application\Ports\UnitOfWork;
use App\Core\Application\Shared\IdGenerator;
use App\Core\Domain\Entities\Task;
use App\Core\Domain\Enums\TaskStatus;
use App\Core\Domain\Repositories\TaskRepository;

describe('CreateTaskCommand', function () {
    describe('execute()', function () {
        it('creates and saves a new task with title and description', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $generatedId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $title = 'Test Task';
            $description = 'Test Description';

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn($generatedId);

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tasks')->andReturn($mockTaskRepo);
            $mockTaskRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $useCase = new CreateTaskCommand($mockUow, $mockIdGenerator);
            $input = new CreateTaskInputDTO($title, $description);

            $result = $useCase->execute($input);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getTitle())->toBe($title)
                ->and($result->getDescription())->toBe($description)
                ->and($result->getStatus())->toBe(TaskStatus::TODO);
        });

        it('creates and saves a new task with only title', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $generatedId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $title = 'Test Task Without Description';

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn($generatedId);

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tasks')->andReturn($mockTaskRepo);
            $mockTaskRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $useCase = new CreateTaskCommand($mockUow, $mockIdGenerator);
            $input = new CreateTaskInputDTO($title);

            $result = $useCase->execute($input);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getTitle())->toBe($title)
                ->and($result->getDescription())->toBe('')
                ->and($result->getStatus())->toBe(TaskStatus::TODO);
        });

        it('calls id generator exactly once', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tasks')->andReturn($mockTaskRepo);
            $mockTaskRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $useCase = new CreateTaskCommand($mockUow, $mockIdGenerator);
            $input = new CreateTaskInputDTO('Test Task');

            $useCase->execute($input);
        });

        it('calls repository save exactly once', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tasks')->andReturn($mockTaskRepo);
            $mockTaskRepo->shouldReceive('save')->once();
            $mockUow->shouldReceive('commit')->once();

            $useCase = new CreateTaskCommand($mockUow, $mockIdGenerator);
            $input = new CreateTaskInputDTO('Test Task');

            $useCase->execute($input);
        });

        it('returns the created task', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $generatedId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn($generatedId);

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tasks')->andReturn($mockTaskRepo);
            $mockTaskRepo->shouldReceive('save');
            $mockUow->shouldReceive('commit')->once();

            $useCase = new CreateTaskCommand($mockUow, $mockIdGenerator);
            $input = new CreateTaskInputDTO('New Task', 'Description');

            $result = $useCase->execute($input);

            expect($result)->toBeInstanceOf(Task::class);
        });

        it('rolls back transaction on failure', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockUow = mock(UnitOfWork::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockUow->shouldReceive('begin')->once();
            $mockUow->shouldReceive('tasks')->andReturn($mockTaskRepo);
            $mockTaskRepo->shouldReceive('save')
                ->once()
                ->andThrow(new Exception('Database error'));
            $mockUow->shouldReceive('rollback')->once();

            $useCase = new CreateTaskCommand($mockUow, $mockIdGenerator);
            $input = new CreateTaskInputDTO('Test Task');

            expect(fn() => $useCase->execute($input))->toThrow(Exception::class);
        });
    });
});

