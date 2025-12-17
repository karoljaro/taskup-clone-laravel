<?php

use App\core\application\useCases\CreateTaskUseCase;
use App\core\application\DTOs\CreateTaskInputDTO;
use App\core\application\Shared\IdGenerator;
use App\core\domain\Repositories\TaskRepository;
use App\core\domain\Entities\Task;
use App\core\domain\Enums\TaskStatus;

describe('CreateTaskUseCase', function () {
    describe('execute()', function () {
        it('creates and saves a new task with title and description', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $generatedId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $title = 'Test Task';
            $description = 'Test Description';

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn($generatedId);

            $mockTaskRepo->shouldReceive('save')
                ->once();

            $useCase = new CreateTaskUseCase($mockTaskRepo, $mockIdGenerator);
            $input = new CreateTaskInputDTO($title, $description);

            $result = $useCase->execute($input);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getTitle())->toBe($title)
                ->and($result->getDescription())->toBe($description)
                ->and($result->getStatus())->toBe(TaskStatus::TODO);
        });

        it('creates and saves a new task with only title', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $generatedId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $title = 'Test Task Without Description';

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn($generatedId);

            $mockTaskRepo->shouldReceive('save')
                ->once();

            $useCase = new CreateTaskUseCase($mockTaskRepo, $mockIdGenerator);
            $input = new CreateTaskInputDTO($title);

            $result = $useCase->execute($input);

            expect($result)->toBeInstanceOf(Task::class)
                ->and($result->getTitle())->toBe($title)
                ->and($result->getDescription())->toBe('')
                ->and($result->getStatus())->toBe(TaskStatus::TODO);
        });

        it('calls id generator exactly once', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->once()
                ->andReturn('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo->shouldReceive('save');

            $useCase = new CreateTaskUseCase($mockTaskRepo, $mockIdGenerator);
            $input = new CreateTaskInputDTO('Test Task');

            $useCase->execute($input);
        });

        it('calls repository save exactly once', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo->shouldReceive('save')
                ->once();

            $useCase = new CreateTaskUseCase($mockTaskRepo, $mockIdGenerator);
            $input = new CreateTaskInputDTO('Test Task');

            $useCase->execute($input);
        });

        it('returns the created task', function () {
            $mockIdGenerator = mock(IdGenerator::class);
            $mockTaskRepo = mock(TaskRepository::class);

            $generatedId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

            $mockIdGenerator->shouldReceive('generate')
                ->andReturn($generatedId);

            $mockTaskRepo->shouldReceive('save');

            $useCase = new CreateTaskUseCase($mockTaskRepo, $mockIdGenerator);
            $input = new CreateTaskInputDTO('New Task', 'Description');

            $result = $useCase->execute($input);

            expect($result)->toBeInstanceOf(Task::class);
        });
    });
});

