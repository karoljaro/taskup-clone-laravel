<?php

use App\Core\domain\Entities\Task;
use App\Core\domain\Enums\TaskStatus;
use App\Core\domain\Exceptions\InvalidDescriptionException;
use App\Core\domain\Exceptions\InvalidIdException;
use App\Core\domain\Exceptions\InvalidTitleException;
use App\Core\domain\VO\TaskId;

describe('Task Entity', function () {
    describe('Factory - create()', function () {
        it('can be created with valid data', function () {
            $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $title = 'Buy groceries';
            $description = 'Milk, eggs, bread';

            $task = Task::create(
                id: $id,
                title: $title,
                description: $description
            );

            expect($task)->toBeInstanceOf(Task::class)
                ->and($task->getTitle())->toBe($title)
                ->and($task->getDescription())->toBe($description)
                ->and($task->getStatus())->toBe(TaskStatus::TODO);
        });

        it('can be created with null description', function () {
            $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
            $title = 'Buy groceries';

            $task = Task::create(
                id: $id,
                title: $title,
                description: null
            );

            expect($task)->toBeInstanceOf(Task::class)
                ->and($task->getDescription())->toBe('');
        });

        it('newly created task has TODO status', function () {
            $task = Task::create(
                id: '550e8400-e29b-41d4-a716-446655440000',
                title: 'Learn PHP',
                description: null
            );

            expect($task->getStatus())->toBe(TaskStatus::TODO);
        });

        it('fails with invalid ID', function () {
            Task::create(
                id: '',
                title: 'Valid title',
                description: null
            );
        })->throws(InvalidIdException::class);

        it('fails with empty title', function () {
            Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: '',
                description: null
            );
        })->throws(InvalidTitleException::class);

        it('fails with title too short', function () {
            Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'ab',
                description: null
            );
        })->throws(InvalidTitleException::class);

        it('fails with title too long', function () {
            $longTitle = str_repeat('a', 256);

            Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: $longTitle,
                description: null
            );
        })->throws(InvalidTitleException::class);

        it('fails with title containing HTML', function () {
            Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: '<script>alert("xss")</script>',
                description: null
            );
        })->throws(InvalidTitleException::class);

        it('fails with description too long', function () {
            $longDescription = str_repeat('a', 2001);

            Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Valid title',
                description: $longDescription
            );
        })->throws(InvalidDescriptionException::class);

        it('sets correct timestamps', function () {
            $beforeCreation = new DateTimeImmutable();

            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test task',
                description: null
            );

            $afterCreation = new DateTimeImmutable();

            expect($task->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class)
                ->and($task->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class)
                ->and($task->getCreatedAt() >= $beforeCreation)->toBeTrue()
                ->and($task->getCreatedAt() <= $afterCreation)->toBeTrue()
                ->and($task->getCreatedAt())->toEqual($task->getUpdatedAt());
        });
    });

    describe('update()', function () {
        it('can update title', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Buy groceries',
                description: null
            );

            $originalUpdatedAt = $task->getUpdatedAt();
            sleep(1);

            $task->update(title: 'Buy vegetables');

            expect($task->getTitle())->toBe('Buy vegetables')
                ->and($task->getUpdatedAt() > $originalUpdatedAt)->toBeTrue();
        });

        it('can update description', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Buy groceries',
                description: 'Milk and eggs'
            );

            $task->update(description: 'Vegetables only');

            expect($task->getDescription())->toBe('Vegetables only');
        });

        it('can update status', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Buy groceries',
                description: null
            );

            $task->update(status: TaskStatus::IN_PROGRESS);

            expect($task->getStatus())->toBe(TaskStatus::IN_PROGRESS);
        });

        it('can update multiple properties at once', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Buy groceries',
                description: 'Milk'
            );

            $task->update(
                title: 'Buy vegetables',
                description: 'Carrots and potatoes',
                status: TaskStatus::COMPLETED
            );

            expect($task->getTitle())->toBe('Buy vegetables')
                ->and($task->getDescription())->toBe('Carrots and potatoes')
                ->and($task->getStatus())->toBe(TaskStatus::COMPLETED);
        });

        it('does not change updatedAt when no changes are made', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Buy groceries',
                description: null
            );

            $originalUpdatedAt = $task->getUpdatedAt();
            sleep(1);

            $task->update(
                title: $task->getTitle(),
                description: $task->getDescription(),
                status: $task->getStatus()
            );

            expect($task->getUpdatedAt())->toEqual($originalUpdatedAt);
        });

        it('fails with invalid title', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Buy groceries',
                description: null
            );

            $task->update(title: 'ab');
        })->throws(InvalidTitleException::class);

        it('fails with invalid description', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Buy groceries',
                description: null
            );

            $longDescription = str_repeat('a', 2001);
            $task->update(description: $longDescription);
        })->throws(InvalidDescriptionException::class);

        it('preserves old values when updating only some properties', function () {
            $originalTitle = 'Buy groceries';
            $originalDescription = 'Milk and eggs';

            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: $originalTitle,
                description: $originalDescription
            );

            $task->update(status: TaskStatus::IN_PROGRESS);

            expect($task->getTitle())->toBe($originalTitle)
                ->and($task->getDescription())->toBe($originalDescription)
                ->and($task->getStatus())->toBe(TaskStatus::IN_PROGRESS);
        });
    });

    describe('Getters', function () {
        it('getId returns TaskId instance', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            expect($task->getId())->toBeInstanceOf(TaskId::class);
        });

        it('getTitle returns correct title', function () {
            $title = 'Buy groceries';

            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: $title,
                description: null
            );

            expect($task->getTitle())->toBe($title);
        });

        it('getDescription returns correct description', function () {
            $description = 'Milk and eggs';

            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Buy groceries',
                description: $description
            );

            expect($task->getDescription())->toBe($description);
        });

        it('getStatus returns correct status', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            expect($task->getStatus())->toBe(TaskStatus::TODO);

            $task->update(status: TaskStatus::COMPLETED);

            expect($task->getStatus())->toBe(TaskStatus::COMPLETED);
        });

        it('getCreatedAt returns DateTimeImmutable', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            expect($task->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class);
        });

        it('getUpdatedAt returns DateTimeImmutable', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            expect($task->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class);
        });

        it('createdAt is immutable and never changes', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $originalCreatedAt = $task->getCreatedAt();
            sleep(1);

            $task->update(title: 'Updated title');

            expect($task->getCreatedAt())->toEqual($originalCreatedAt);
        });

        it('TaskId is readonly', function () {
            $task = Task::create(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Test',
                description: null
            );

            $id = $task->getId();

            expect($id)->toBeInstanceOf(TaskId::class);
        });
    });
});

