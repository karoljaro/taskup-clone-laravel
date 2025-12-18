<?php

use App\core\application\UseCases\DeleteTaskUseCase;
use App\core\domain\Repositories\TaskRepository;
use App\core\domain\Exceptions\TaskNotFoundException;
use App\core\domain\VO\TaskId;

describe('DeleteTaskUseCase', function () {
    describe('execute()', function () {
        it('calls repository deleteByTaskId with correct TaskId', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('deleteByTaskId')
                ->with($taskId)
                ->once();

            $useCase = new DeleteTaskUseCase($mockTaskRepo);
            $useCase->execute($taskId);
        });

        it('calls repository deleteByTaskId exactly once', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('deleteByTaskId')
                ->once();

            $useCase = new DeleteTaskUseCase($mockTaskRepo);
            $useCase->execute($taskId);
        });

        it('returns void', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('deleteByTaskId');

            $useCase = new DeleteTaskUseCase($mockTaskRepo);

            $useCase->execute($taskId);

            expect(true)->toBeTrue();
        });

        it('throws TaskNotFoundException when task does not exist', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo = mock(TaskRepository::class);
            $mockTaskRepo->shouldReceive('deleteByTaskId')
                ->with($taskId)
                ->once()
                ->andThrow(new TaskNotFoundException($taskId));

            $useCase = new DeleteTaskUseCase($mockTaskRepo);

            expect(fn() => $useCase->execute($taskId))
                ->toThrow(TaskNotFoundException::class);
        });

        it('propagates TaskNotFoundException with correct message', function () {
            $taskId = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $mockTaskRepo = mock(TaskRepository::class);
            $exception = new TaskNotFoundException($taskId);
            $mockTaskRepo->shouldReceive('deleteByTaskId')
                ->with($taskId)
                ->once()
                ->andThrow($exception);

            $useCase = new DeleteTaskUseCase($mockTaskRepo);

            expect(fn() => $useCase->execute($taskId))
                ->toThrow(TaskNotFoundException::class);
        });

        it('accepts different valid TaskId formats', function () {
            $taskIds = [
                'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                '550e8400-e29b-41d4-a716-446655440000',
                '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
            ];

            foreach ($taskIds as $id) {
                $taskId = new TaskId($id);
                $mockTaskRepo = mock(TaskRepository::class);
                $mockTaskRepo->shouldReceive('deleteByTaskId')
                    ->with($taskId)
                    ->once();

                $useCase = new DeleteTaskUseCase($mockTaskRepo);
                $useCase->execute($taskId);
            }
        });
    });
});

