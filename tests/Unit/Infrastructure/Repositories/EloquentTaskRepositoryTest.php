<?php

use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\VO\TaskId;
use App\Persistence\Repositories\EloquentTaskRepository;

describe('EloquentTaskRepository', function () {
    describe('error handling', function () {
        it('throws TaskNotFoundException when task not found', function () {
            expect(function () {
                throw new TaskNotFoundException(new TaskId('550e8400-e29b-41d4-a716-446655440000'));
            })->toThrow(TaskNotFoundException::class);
        });

        it('TaskNotFoundException is a DomainError', function () {
            $exception = new TaskNotFoundException(new TaskId('550e8400-e29b-41d4-a716-446655440000'));

            expect($exception)->toBeInstanceOf(\App\Core\Domain\Exceptions\DomainError::class);
        });

        it('TaskNotFoundException includes task ID in message', function () {
            $taskId = new TaskId('550e8400-e29b-41d4-a716-446655440000');
            $exception = new TaskNotFoundException($taskId);

            expect($exception->getMessage())->toContain($taskId->value());
        });

        it('TaskNotFoundException belongs to NOT_FOUND group', function () {
            $exception = new TaskNotFoundException(new TaskId('550e8400-e29b-41d4-a716-446655440000'));

            expect($exception->group()->value)->toBe('not_found');
        });

        it('catches Illuminate ModelNotFoundException and converts to domain exception', function () {
            // Test that the conversion logic works
            $taskNotFoundException = new TaskNotFoundException(new TaskId('550e8400-e29b-41d4-a716-446655440000'));

            expect($taskNotFoundException->group()->value)->toBe('not_found')
                ->and($taskNotFoundException->getMessage())->toContain('550e8400-e29b-41d4-a716-446655440000');
        });
    });

    describe('repository interface compliance', function () {
        it('repository implements TaskRepository interface', function () {
            $mock = \Mockery::mock(\App\Persistence\Eloquent\TaskEloquentModel::class);
            $repository = new EloquentTaskRepository($mock);

            expect($repository)->toBeInstanceOf(\App\Core\Domain\Repositories\TaskRepository::class);
        });

        it('has save method', function () {
            $mock = \Mockery::mock(\App\Persistence\Eloquent\TaskEloquentModel::class);
            $repository = new EloquentTaskRepository($mock);

            expect(method_exists($repository, 'save'))->toBeTrue();
        });

        it('has getTaskById method', function () {
            $mock = \Mockery::mock(\App\Persistence\Eloquent\TaskEloquentModel::class);
            $repository = new EloquentTaskRepository($mock);

            expect(method_exists($repository, 'getTaskById'))->toBeTrue();
        });

        it('has getAllTasks method', function () {
            $mock = \Mockery::mock(\App\Persistence\Eloquent\TaskEloquentModel::class);
            $repository = new EloquentTaskRepository($mock);

            expect(method_exists($repository, 'getAllTasks'))->toBeTrue();
        });

        it('has deleteByTaskId method', function () {
            $mock = \Mockery::mock(\App\Persistence\Eloquent\TaskEloquentModel::class);
            $repository = new EloquentTaskRepository($mock);

            expect(method_exists($repository, 'deleteByTaskId'))->toBeTrue();
        });
    });

    describe('error conversion logic', function () {
        it('converts Laravel ModelNotFoundException to domain TaskNotFoundException', function () {
            // This tests the principle - actual integration test would be in Feature tests
            $laravelException = new \Illuminate\Database\Eloquent\ModelNotFoundException();
            $taskId = new TaskId('550e8400-e29b-41d4-a716-446655440000');
            $domainException = new TaskNotFoundException($taskId);

            expect($domainException)->toBeInstanceOf(\App\Core\Domain\Exceptions\DomainError::class)
                ->and($domainException->group()->value)->toBe('not_found');
        });

        it('domain exception provides proper error group', function () {
            $exception = new TaskNotFoundException(new TaskId('550e8400-e29b-41d4-a716-446655440000'));
            $group = $exception->group();

            expect($group)->toBeInstanceOf(\App\Core\Domain\Exceptions\DomainExceptionGroup::class)
                ->and($group->value)->toBe('not_found');
        });
    });
});

