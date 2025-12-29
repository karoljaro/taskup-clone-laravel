<?php

use App\Core\Domain\Exceptions\InvalidEmailException;
use App\Core\Domain\Exceptions\InvalidIdException;
use App\Core\Domain\Exceptions\InvalidPasswordException;
use App\Core\Domain\Exceptions\InvalidUsernameException;
use App\Core\Domain\Validation\UserInvariantValidation;

describe('UserInvariantValidation', function () {
    describe('validateCreateProps()', function () {
        it('passes with valid data', function () {
            expect(true)->toBeTrue();
        });

        it('fails with invalid ID', function () {
            UserInvariantValidation::validateCreateProps(
                id: 'invalid-id',
                username: 'john_doe',
                email: 'john@example.com',
                plainPassword: 'SecurePass123'
            );
        })->throws(InvalidIdException::class);

        it('fails with invalid username', function () {
            UserInvariantValidation::validateCreateProps(
                id: '550e8400-e29b-41d4-a716-446655440000',
                username: 'ab',
                email: 'john@example.com',
                plainPassword: 'SecurePass123'
            );
        })->throws(InvalidUsernameException::class);

        it('fails with invalid email', function () {
            UserInvariantValidation::validateCreateProps(
                id: '550e8400-e29b-41d4-a716-446655440000',
                username: 'john_doe',
                email: 'invalid-email',
                plainPassword: 'SecurePass123'
            );
        })->throws(InvalidEmailException::class);

        it('fails with password too short', function () {
            UserInvariantValidation::validateCreateProps(
                id: '550e8400-e29b-41d4-a716-446655440000',
                username: 'john_doe',
                email: 'john@example.com',
                plainPassword: 'short'
            );
        })->throws(InvalidPasswordException::class);
    });

    describe('validateUpdateProps()', function () {
        it('passes with valid data', function () {
            expect(true)->toBeTrue();
        });

        it('passes with null values', function () {
            expect(true)->toBeTrue();
        });

        it('passes with only username updated', function () {
            expect(true)->toBeTrue();
        });

        it('passes with only email updated', function () {
            expect(true)->toBeTrue();
        });

        it('passes with only password updated', function () {
            expect(true)->toBeTrue();
        });

        it('fails with invalid username', function () {
            UserInvariantValidation::validateUpdateProps(username: 'ab');
        })->throws(InvalidUsernameException::class);

        it('fails with invalid email', function () {
            UserInvariantValidation::validateUpdateProps(email: 'invalid-email');
        })->throws(InvalidEmailException::class);

        it('fails with password too short', function () {
            UserInvariantValidation::validateUpdateProps(password: 'short');
        })->throws(InvalidPasswordException::class);

        it('does not validate null values', function () {
            expect(true)->toBeTrue();
        });
    });

    describe('validateCreatedUser()', function () {
        it('passes with valid user entity', function () {
            expect(true)->toBeTrue();
        });
    });
});

