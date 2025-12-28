<?php

use App\Core\Domain\Entities\Task;
use App\Core\Domain\Enums\TaskStatus;
use App\Http\Resources\TaskResource;

describe('TaskResource', function () {
    describe('from()', function () {
        it('creates TaskResource instance from Task entity', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test task',
                description: 'Test description'
            );

            $resource = TaskResource::from($task);

            expect($resource)->toBeInstanceOf(TaskResource::class);
        });
    });

    describe('jsonSerialize()', function () {
        it('serializes Task to array', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Buy groceries',
                description: 'Milk and eggs'
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized)->toBeArray();
        });

        it('includes all required fields in serialized output', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test task',
                description: 'Test description'
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized)->toHaveKeys([
                'id',
                'title',
                'description',
                'status',
                'created_at',
                'updated_at'
            ]);
        });

        it('returns correct ID as string', function () {
            $taskId = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $task = Task::create(
                id: $taskId,
                title: 'Test',
                description: null
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['id'])
                ->toBeString()
                ->toBe($taskId);
        });

        it('returns title as string', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'My awesome task',
                description: null
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['title'])
                ->toBeString()
                ->toBe('My awesome task');
        });

        it('returns description as string (empty string for null)', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['description'])
                ->toBeString()
                ->toBe('');
        });

        it('returns description as string when provided', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: 'Some description'
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['description'])
                ->toBeString()
                ->toBe('Some description');
        });

        it('converts status enum to string value', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['status'])
                ->toBeString()
                ->toBe(TaskStatus::TODO->value);
        });

        it('formats created_at timestamp as string with correct format', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['created_at'])
                ->toBeString()
                ->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/');
        });

        it('formats updated_at timestamp as string with correct format', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['updated_at'])
                ->toBeString()
                ->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/');
        });

        it('serializes updated task with changed status', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: 'Description'
            );
            $task->update(status: TaskStatus::IN_PROGRESS);

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['status'])->toBe(TaskStatus::IN_PROGRESS->value);
        });

        it('serializes updated task with changed title', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Original title',
                description: null
            );
            $task->update(title: 'Updated title');

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['title'])->toBe('Updated title');
        });

        it('serializes updated task with changed description', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: 'Original description'
            );
            $task->update(description: 'Updated description');

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['description'])->toBe('Updated description');
        });

        it('is JSON serializable', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $resource = TaskResource::from($task);

            expect($resource)->toBeInstanceOf(JsonSerializable::class);
        });

        it('can be converted to JSON string', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test task',
                description: 'Test description'
            );

            $resource = TaskResource::from($task);
            $json = json_encode($resource);

            expect($json)->toBeString();
            $decoded = json_decode($json, true);
            expect($decoded)->toHaveKeys([
                'id',
                'title',
                'description',
                'status',
                'created_at',
                'updated_at'
            ]);
        });
    });

    describe('data types consistency', function () {
        it('all fields have correct data types', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test task',
                description: 'Test description'
            );

            $resource = TaskResource::from($task);
            $serialized = $resource->jsonSerialize();

            expect($serialized['id'])->toBeString()
                ->and($serialized['title'])->toBeString()
                ->and($serialized['description'])->toBeString()
                ->and($serialized['status'])->toBeString()
                ->and($serialized['created_at'])->toBeString()
                ->and($serialized['updated_at'])->toBeString();
        });
    });
});

