<?php

use App\Core\Domain\Exceptions\InvalidIdException;
use App\Core\Domain\VO\UserId;

describe('UserId Value Object', function () {
    it('can be created with valid UUID', function () {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userId = new UserId($uuid);

        expect($userId->value())->toBe($uuid);
    });

    it('is immutable', function () {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userId = new UserId($uuid);

        expect($userId->value())->toBe($uuid);
    });

    it('creation fails with empty string', function () {
        new UserId('');
    })->throws(InvalidIdException::class);

    it('creation fails with whitespace only', function () {
        new UserId('   ');
    })->throws(InvalidIdException::class);

    it('creation fails with invalid UUID format', function () {
        new UserId('not-a-valid-uuid');
    })->throws(InvalidIdException::class);

    it('creation fails with invalid UUID checksum', function () {
        new UserId('invalid-uuid-format-checksum');
    })->throws(InvalidIdException::class);

    it('creation succeeds with uppercase UUID', function () {
        $uuid = '550E8400-E29B-41D4-A716-446655440000';
        $userId = new UserId($uuid);

        expect($userId->value())->toBe($uuid);
    });

    it('creation succeeds with mixed case UUID', function () {
        $uuid = '550e8400-E29B-41D4-a716-446655440000';
        $userId = new UserId($uuid);


        expect($userId->value())->toBe($uuid);
    });

    it('instances with same value are logically equal', function () {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $userId1 = new UserId($uuid);
        $userId2 = new UserId($uuid);

        expect($userId1->equals($userId2))->toBeTrue();
    });

    it('instances with different values are not equal', function () {
        $userId1 = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $userId2 = new UserId('550e8400-e29b-41d4-a716-446655440001');

        expect($userId1->equals($userId2))->toBeFalse();
    });
});

