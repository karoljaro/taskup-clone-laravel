<?php

use App\Core\Domain\Entities\User;
use App\Core\Domain\Exceptions\InvalidEmailException;
use App\Core\Domain\Exceptions\InvalidIdException;
use App\Core\Domain\Exceptions\InvalidPasswordException;
use App\Core\Domain\Exceptions\InvalidUsernameException;
use App\Core\Domain\VO\Email;
use App\Core\Domain\VO\HashedPassword;
use App\Core\Domain\VO\UserId;

describe('User Entity', function () {
    describe('Factory - create()', function () {
        it('can be created with valid data', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $username = 'john_doe';
            $email = 'john@example.com';
            $password = 'SecurePass123';

            $user = User::create($id, $username, $email, $password);

            expect($user)->toBeInstanceOf(User::class)
                ->and($user->getId()->value())->toBe($id)
                ->and($user->getUsername())->toBe($username)
                ->and($user->getEmail()->value())->toBe($email)
                ->and($user->isEmailVerified())->toBeFalse();
        });

        it('initializes timestamps on creation', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class)
                ->and($user->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class)
                ->and($user->getCreatedAt())->toEqual($user->getUpdatedAt());
        });

        it('fails with invalid ID', function () {
            User::create('invalid-id', 'john_doe', 'john@example.com', 'SecurePass123');
        })->throws(InvalidIdException::class);

        it('fails with empty username', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            User::create($id, '', 'john@example.com', 'SecurePass123');
        })->throws(InvalidUsernameException::class);

        it('fails with username too short', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            User::create($id, 'ab', 'john@example.com', 'SecurePass123');
        })->throws(InvalidUsernameException::class);

        it('passes with minimum length username (3 chars)', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'abc', 'john@example.com', 'SecurePass123');

            expect($user->getUsername())->toBe('abc');
        });

        it('passes with maximum length username (50 chars)', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $maxUsername = str_repeat('a', 50);
            $user = User::create($id, $maxUsername, 'john@example.com', 'SecurePass123');

            expect($user->getUsername())->toBe($maxUsername)
                ->and(strlen($user->getUsername()))->toBe(50);
        });

        it('fails with username too long', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $longUsername = str_repeat('a', 51);
            User::create($id, $longUsername, 'john@example.com', 'SecurePass123');
        })->throws(InvalidUsernameException::class);

        it('fails with username containing invalid characters', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            User::create($id, 'john@doe!', 'john@example.com', 'SecurePass123');
        })->throws(InvalidUsernameException::class);

        it('fails with invalid email format', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            User::create($id, 'john_doe', 'invalid-email', 'SecurePass123');
        })->throws(InvalidEmailException::class);

        it('fails with empty email', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            User::create($id, 'john_doe', '', 'SecurePass123');
        })->throws(InvalidEmailException::class);

        it('fails with email too long', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $longEmail = str_repeat('a', 250) . '@example.com';
            User::create($id, 'john_doe', $longEmail, 'SecurePass123');
        })->throws(InvalidEmailException::class);

        it('passes with valid email at max length boundary', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            // Use a valid but longer email
            $longEmail = 'very.long.username.with.many.dots@subdomain.example.co.uk';
            $user = User::create($id, 'john_doe', $longEmail, 'SecurePass123');

            expect($user->getEmail()->value())->toBe($longEmail);
        });

        it('fails with password too short', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            User::create($id, 'john_doe', 'john@example.com', 'short');
        })->throws(InvalidPasswordException::class);

        it('passes with minimum length password (8 chars)', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', '12345678');

            expect($user->verifyPassword('12345678'))->toBeTrue();
        });

        it('stores password as hash', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $plainPassword = 'SecurePass123';
            $user = User::create($id, 'john_doe', 'john@example.com', $plainPassword);

            expect($user->getPassword())->toBeInstanceOf(HashedPassword::class)
                ->and($user->verifyPassword($plainPassword))->toBeTrue()
                ->and($user->verifyPassword('WrongPassword'))->toBeFalse();
        });

        it('trims whitespace from email', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create(
                $id,
                'john_doe',
                '  john@example.com  ',
                'SecurePass123'
            );

            expect($user->getEmail()->value())->toBe('john@example.com');
        });
    });

    describe('update()', function () {
        it('can update username', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');
            $oldUpdatedAt = $user->getUpdatedAt();

            $user->update(username: 'jane_doe');

            expect($user->getUsername())->toBe('jane_doe')
                ->and($user->getUpdatedAt()->getTimestamp())->toBeGreaterThanOrEqual($oldUpdatedAt->getTimestamp());
        });

        it('can update email', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            $user->update(email: 'newemail@example.com');

            expect($user->getEmail()->value())->toBe('newemail@example.com')
                ->and($user->isEmailVerified())->toBeFalse();
        });

        it('resets email verification when email changes', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');
            $user->verifyEmail();

            expect($user->isEmailVerified())->toBeTrue();

            $user->update(email: 'newemail@example.com');

            expect($user->isEmailVerified())->toBeFalse()
                ->and($user->getEmailVerifiedAt())->toBeNull();
        });

        it('can update password', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');
            $oldPassword = 'SecurePass123';
            $newPassword = 'NewSecurePass456';

            $user->update(plainPassword: $newPassword);

            expect($user->verifyPassword($oldPassword))->toBeFalse()
                ->and($user->verifyPassword($newPassword))->toBeTrue();
        });

        it('can update multiple properties at once', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            $user->update(
                username: 'jane_doe',
                email: 'jane@example.com',
                plainPassword: 'NewSecurePass456'
            );

            expect($user->getUsername())->toBe('jane_doe')
                ->and($user->getEmail()->value())->toBe('jane@example.com')
                ->and($user->verifyPassword('NewSecurePass456'))->toBeTrue();
        });

        it('does not change updatedAt when only same values are updated', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');
            // Sleep to ensure time passes
            usleep(100000); // 0.1 second

            $oldUpdatedAt = $user->getUpdatedAt();
            $user->update(username: 'john_doe');

            expect($user->getUpdatedAt()->getTimestamp())->toBe($oldUpdatedAt->getTimestamp());
        });

        it('preserves old values when updating only some properties', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            $user->update(username: 'jane_doe');

            expect($user->getEmail()->value())->toBe('john@example.com')
                ->and($user->verifyPassword('SecurePass123'))->toBeTrue();
        });

        it('fails with invalid username', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            $user->update(username: 'ab');
        })->throws(InvalidUsernameException::class);

        it('fails with invalid email', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            $user->update(email: 'invalid-email');
        })->throws(InvalidEmailException::class);

        it('fails with empty username', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            $user->update(username: '');
        })->throws(InvalidUsernameException::class);

        it('allows update with null values (preserves existing)', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');
            $originalEmail = $user->getEmail()->value();

            $user->update(username: null, email: null, plainPassword: null);

            expect($user->getUsername())->toBe('john_doe')
                ->and($user->getEmail()->value())->toBe($originalEmail)
                ->and($user->verifyPassword('SecurePass123'))->toBeTrue();
        });
    });

    describe('verifyEmail()', function () {
        it('marks email as verified', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->isEmailVerified())->toBeFalse();

            $user->verifyEmail();

            expect($user->isEmailVerified())->toBeTrue();
        });

        it('sets verification timestamp', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->getEmailVerifiedAt())->toBeNull();

            $user->verifyEmail();

            expect($user->getEmailVerifiedAt())->toBeInstanceOf(DateTimeImmutable::class);
        });
    });

    describe('verifyPassword()', function () {
        it('returns true for correct password', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $plainPassword = 'SecurePass123';
            $user = User::create($id, 'john_doe', 'john@example.com', $plainPassword);

            expect($user->verifyPassword($plainPassword))->toBeTrue();
        });

        it('returns false for incorrect password', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->verifyPassword('WrongPassword'))->toBeFalse();
        });

        it('is case sensitive', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->verifyPassword('securepass123'))->toBeFalse();
        });
    });

    describe('Getters', function () {
        it('getId returns UserId instance', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->getId())->toBeInstanceOf(UserId::class)
                ->and($user->getId()->value())->toBe($id);
        });

        it('getUsername returns correct username', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->getUsername())->toBe('john_doe');
        });

        it('getEmail returns Email instance', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->getEmail())->toBeInstanceOf(Email::class)
                ->and($user->getEmail()->value())->toBe('john@example.com');
        });

        it('getPassword returns HashedPassword instance', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->getPassword())->toBeInstanceOf(HashedPassword::class);
        });

        it('getCreatedAt returns DateTimeImmutable', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->getCreatedAt())->toBeInstanceOf(DateTimeImmutable::class);
        });

        it('getUpdatedAt returns DateTimeImmutable', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');

            expect($user->getUpdatedAt())->toBeInstanceOf(DateTimeImmutable::class);
        });

        it('createdAt is immutable and never changes', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');
            $originalCreatedAt = $user->getCreatedAt();

            $user->update(username: 'jane_doe');

            expect($user->getCreatedAt())->toEqual($originalCreatedAt)
                ->and($user->getCreatedAt())->not->toEqual($user->getUpdatedAt());
        });

        it('getId is readonly', function () {
            $id = '550e8400-e29b-41d4-a716-446655440000';
            $user = User::create($id, 'john_doe', 'john@example.com', 'SecurePass123');
            $originalId = $user->getId();

            $user->update(username: 'jane_doe');

            expect($user->getId())->toEqual($originalId);
        });
    });

    describe('reconstruct()', function () {
        it('can reconstruct user from database values', function () {
            $id = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $username = 'john_doe';
            $email = 'john@example.com';
            $hashedPassword = password_hash('SecurePass123', PASSWORD_BCRYPT);
            $createdAt = new DateTimeImmutable('2025-01-01 10:00:00');
            $updatedAt = new DateTimeImmutable('2025-01-15 15:30:00');

            $user = User::reconstruct(
                $id,
                $username,
                $email,
                $hashedPassword,
                $createdAt,
                $updatedAt
            );

            expect($user->getId()->value())->toBe($id->value())
                ->and($user->getUsername())->toBe($username)
                ->and($user->getEmail()->value())->toBe($email)
                ->and($user->getCreatedAt())->toEqual($createdAt)
                ->and($user->getUpdatedAt())->toEqual($updatedAt)
                ->and($user->isEmailVerified())->toBeFalse();
        });

        it('can reconstruct user with verified email', function () {
            $id = new UserId('550e8400-e29b-41d4-a716-446655440000');
            $verifiedAt = new DateTimeImmutable('2025-01-10 12:00:00');

            $user = User::reconstruct(
                $id,
                'john_doe',
                'john@example.com',
                password_hash('SecurePass123', PASSWORD_BCRYPT),
                new DateTimeImmutable('2025-01-01 10:00:00'),
                new DateTimeImmutable('2025-01-15 15:30:00'),
                emailVerified: true,
                verifiedAt: $verifiedAt
            );

            expect($user->isEmailVerified())->toBeTrue()
                ->and($user->getEmailVerifiedAt())->toEqual($verifiedAt);
        });

        it('reconstructs password from hash', function () {
            $plainPassword = 'SecurePass123';
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
            $id = new UserId('550e8400-e29b-41d4-a716-446655440000');

            $user = User::reconstruct(
                $id,
                'john_doe',
                'john@example.com',
                $hashedPassword,
                new DateTimeImmutable(),
                new DateTimeImmutable()
            );

            expect($user->verifyPassword($plainPassword))->toBeTrue();
        });
    });
});

