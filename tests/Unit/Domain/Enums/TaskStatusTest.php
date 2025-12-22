<?php

use App\Core\Domain\Enums\TaskStatus;

describe('TaskStatus Enum', function () {
    it('has required cases', function () {
        $cases = TaskStatus::cases();
        $caseValues = array_map(fn($case) => $case->value, $cases);

        expect($caseValues)->toContain(TaskStatus::TODO->value)
            ->and($caseValues)->toContain(TaskStatus::IN_PROGRESS->value)
            ->and($caseValues)->toContain(TaskStatus::COMPLETED->value);
    });

    it('TODO has correct value', function () {
        expect(TaskStatus::TODO->value)->toBe('todo');
    });

    it('IN_PROGRESS has correct value', function () {
        expect(TaskStatus::IN_PROGRESS->value)->toBe('in_progress');
    });

    it('COMPETED has correct value', function () {
        expect(TaskStatus::COMPLETED->value)->toBe('completed');
    });

    it('can be created from string value', function () {
        $status = TaskStatus::from('todo');

        expect($status)->toBe(TaskStatus::TODO);
    });

    it('creation from invalid value throws error', function () {
        expect(fn () => TaskStatus::from('invalid_status'))
            ->toThrow(ValueError::class);
    });
});

