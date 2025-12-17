<?php

use App\core\domain\Exceptions\InvalidIdException;
use App\core\domain\VO\TaskId;

describe('TaskId Value Object', function () {
    it('can be created with valid UUID', function () {
        $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

        $taskId = new TaskId($id);

        expect($taskId->value())->toBe($id);
    });

    it('value is immutable', function () {
        $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

        $taskId = new TaskId($id);
        $firstValue = $taskId->value();
        $secondValue = $taskId->value();

        expect($firstValue)->toBe($secondValue);
    });

    it('creation fails with empty string', function () {
        new TaskId('');
    })->throws(InvalidIdException::class);

    it('creation fails with whitespace only', function () {
        new TaskId('   ');
    })->throws(InvalidIdException::class);

    it('creation fails with invalid UUID format', function () {
        new TaskId('not-a-valid-uuid');
    })->throws(InvalidIdException::class);

    it('creation fails with invalid UUID checksum', function () {
        new TaskId('f47ac10b-58cc-4372-a567-0e02b2c3d47z');
    })->throws(InvalidIdException::class);

    it('creation succeeds with uppercase UUID', function () {
        $id = 'F47AC10B-58CC-4372-A567-0E02B2C3D479';

        $taskId = new TaskId($id);

        expect($taskId->value())->toBe($id);
    });

    it('creation succeeds with mixed case UUID', function () {
        $id = 'f47Ac10b-58Cc-4372-a567-0e02b2C3d479';

        $taskId = new TaskId($id);

        expect($taskId->value())->toBe($id);
    });

    it('instances with same value are logically equal', function () {
        $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

        $taskId1 = new TaskId($id);
        $taskId2 = new TaskId($id);

        expect($taskId1->value())->toBe($taskId2->value());
    });
});

