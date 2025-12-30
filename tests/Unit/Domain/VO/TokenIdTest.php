<?php

use App\Core\Domain\Exceptions\InvalidIdException;
use App\Core\Domain\VO\TokenId;

describe('TokenId Value Object', function () {
    it('can be created with valid UUID', function () {
        $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

        $tokenId = TokenId::create($id);

        expect($tokenId->value())->toBe($id);
    });

    it('value is immutable', function () {
        $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

        $tokenId = TokenId::create($id);
        $firstValue = $tokenId->value();
        $secondValue = $tokenId->value();

        expect($firstValue)->toBe($secondValue);
    });

    it('creation fails with empty string', function () {
        TokenId::create('');
    })->throws(InvalidIdException::class);

    it('creation fails with whitespace only', function () {
        TokenId::create('   ');
    })->throws(InvalidIdException::class);

    it('creation fails with invalid UUID format', function () {
        TokenId::create('not-a-valid-token-id');
    })->throws(InvalidIdException::class);

    it('creation succeeds with uppercase UUID', function () {
        $id = 'F47AC10B-58CC-4372-A567-0E02B2C3D479';

        $tokenId = TokenId::create($id);

        expect($tokenId->value())->toBe($id);
    });

    it('creation succeeds with mixed case UUID', function () {
        $id = 'f47Ac10b-58Cc-4372-a567-0e02b2C3d479';

        $tokenId = TokenId::create($id);

        expect($tokenId->value())->toBe($id);
    });

    it('instances with same value are logically equal', function () {
        $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

        $tokenId1 = TokenId::create($id);
        $tokenId2 = TokenId::create($id);

        expect($tokenId1->equals($tokenId2))->toBeTrue();
    });

    it('instances with different values are not equal', function () {
        $tokenId1 = TokenId::create('f47ac10b-58cc-4372-a567-0e02b2c3d479');
        $tokenId2 = TokenId::create('550e8400-e29b-41d4-a716-446655440000');

        expect($tokenId1->equals($tokenId2))->toBeFalse();
    });

    it('can be converted to string', function () {
        $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

        $tokenId = TokenId::create($id);

        expect((string) $tokenId)->toBe($id);
    });
});

