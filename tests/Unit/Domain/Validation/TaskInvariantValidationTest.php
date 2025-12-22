<?php

use App\Core\domain\Enums\TaskStatus;
use App\Core\domain\Exceptions\InvalidDescriptionException;
use App\Core\domain\Exceptions\InvalidIdException;
use App\Core\domain\Exceptions\InvalidTitleException;
use App\Core\domain\Validation\TaskInvariantValidation;

describe('TaskInvariantValidation', function () {
    describe('validateCreateProps', function () {
        it('passes with valid data', function () {
            TaskInvariantValidation::validateCreateProps(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Valid title',
                description: 'Valid description'
            );

            expect(true)->toBeTrue();
        });

        it('passes with null description', function () {
            TaskInvariantValidation::validateCreateProps(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Valid title',
                description: null
            );

            expect(true)->toBeTrue();
        });

        it('fails with invalid ID', function () {
            TaskInvariantValidation::validateCreateProps(
                id: '',
                title: 'Valid title',
                description: null
            );
        })->throws(InvalidIdException::class);

        it('fails with invalid title', function () {
            TaskInvariantValidation::validateCreateProps(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'ab',
                description: null
            );
        })->throws(InvalidTitleException::class);

        it('fails with invalid description', function () {
            $longDescription = str_repeat('a', 2001);

            TaskInvariantValidation::validateCreateProps(
                id: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                title: 'Valid title',
                description: $longDescription
            );
        })->throws(InvalidDescriptionException::class);
    });

    describe('validateUpdateProps', function () {
        it('passes with valid data', function () {
            TaskInvariantValidation::validateUpdateProps(
                title: 'Updated title',
                description: 'Updated description',
                status: TaskStatus::IN_PROGRESS
            );

            expect(true)->toBeTrue();
        });

        it('fails with invalid title', function () {
            TaskInvariantValidation::validateUpdateProps(
                title: 'ab',
                description: 'Valid',
                status: TaskStatus::TODO
            );
        })->throws(InvalidTitleException::class);

        it('fails with invalid description', function () {
            $longDescription = str_repeat('a', 2001);

            TaskInvariantValidation::validateUpdateProps(
                title: 'Valid title',
                description: $longDescription,
                status: TaskStatus::TODO
            );
        })->throws(InvalidDescriptionException::class);

        it('passes with all TaskStatus values', function () {
            foreach (TaskStatus::cases() as $status) {
                TaskInvariantValidation::validateUpdateProps(
                    title: 'Valid title',
                    description: 'Valid description',
                    status: $status
                );
            }

            expect(true)->toBeTrue();
        });
    });
});

