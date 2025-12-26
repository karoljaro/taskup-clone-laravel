<?php

use App\Core\Domain\Entities\Task;
use App\Core\Domain\Enums\TaskStatus;
use App\Core\Domain\VO\TaskId;
use App\Persistence\Mappers\TaskMapper;

describe('TaskMapper', function () {
    describe('toPersistence()', function () {
        it('converts Domain Entity to persistence array', function () {
            $id = new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
            $task = Task::create(
                id: $id->value(),
                title: 'Buy groceries',
                description: 'Milk and eggs'
            );

            $data = TaskMapper::toPersistence($task);

            expect($data)->toBeArray()
                ->and($data['id'])->toBe('f47ac10b-58cc-4372-a567-0e02b2c3d479')
                ->and($data['title'])->toBe('Buy groceries')
                ->and($data['description'])->toBe('Milk and eggs')
                ->and($data['status'])->toBe(TaskStatus::TODO->value)
                ->and($data['created_at'])->toBeInstanceOf(DateTimeImmutable::class)
                ->and($data['updated_at'])->toBeInstanceOf(DateTimeImmutable::class);
        });

        it('includes all required fields in persistence array', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: 'Test description'
            );

            $data = TaskMapper::toPersistence($task);

            expect($data)->toHaveKeys(['id', 'title', 'description', 'status', 'created_at', 'updated_at']);
        });

        it('preserves status changes in persistence array', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $task->update(status: TaskStatus::IN_PROGRESS);

            $data = TaskMapper::toPersistence($task);

            expect($data['status'])->toBe(TaskStatus::IN_PROGRESS->value);
        });

        it('converts status enum to string value', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $task->update(status: TaskStatus::COMPLETED);

            $data = TaskMapper::toPersistence($task);

            expect($data['status'])->toBeString()
                ->and($data['status'])->toBe('completed');
        });

        it('empty description becomes empty string', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $data = TaskMapper::toPersistence($task);

            expect($data['description'])->toBe('');
        });

        it('preserves all data types', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test title',
                description: 'Test description'
            );

            $data = TaskMapper::toPersistence($task);

            expect($data['id'])->toBeString()
                ->and($data['title'])->toBeString()
                ->and($data['description'])->toBeString()
                ->and($data['status'])->toBeString()
                ->and($data['created_at'])->toBeInstanceOf(DateTimeImmutable::class)
                ->and($data['updated_at'])->toBeInstanceOf(DateTimeImmutable::class);
        });
    });

    describe('Domain Entity structure', function () {
        it('Task::create returns fully valid entity', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: 'Desc'
            );

            expect($task)->toBeInstanceOf(Task::class)
                ->and($task->getId())->toBeInstanceOf(TaskId::class)
                ->and($task->getTitle())->toBeString()
                ->and($task->getStatus())->toBeInstanceOf(TaskStatus::class)
                ->and($task->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class);
        });

        it('Task::reconstruct creates valid entity', function () {
            $createdAt = new DateTimeImmutable('2025-01-01 10:00:00');
            $updatedAt = new DateTimeImmutable('2025-01-02 15:30:00');

            $task = Task::reconstruct(
                id: new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d479'),
                title: 'Test',
                description: 'Desc',
                status: TaskStatus::IN_PROGRESS,
                createdAt: $createdAt,
                updatedAt: $updatedAt
            );

            expect($task)->toBeInstanceOf(Task::class)
                ->and($task->getCreatedAt())->toEqual($createdAt)
                ->and($task->getUpdatedAt())->toEqual($updatedAt)
                ->and($task->getStatus())->toBe(TaskStatus::IN_PROGRESS);
        });
    });
});

