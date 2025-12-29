<?php

use App\Core\Domain\VO\HashedPassword;

describe('HashedPassword Value Object', function () {
    it('can be created from plain password', function () {
        $plainPassword = 'SecurePassword123';
        $hashedPassword = HashedPassword::fromPlain($plainPassword);

        expect($hashedPassword)->toBeInstanceOf(HashedPassword::class)
            ->and($hashedPassword->value())->not->toBe($plainPassword);
    });

    it('hashes password using bcrypt', function () {
        $plainPassword = 'SecurePassword123';
        $hashedPassword = HashedPassword::fromPlain($plainPassword);
        $hash = $hashedPassword->value();

        expect(password_verify($plainPassword, $hash))->toBeTrue();
    });

    it('can verify correct plain password', function () {
        $plainPassword = 'SecurePassword123';
        $hashedPassword = HashedPassword::fromPlain($plainPassword);

        expect($hashedPassword->verify($plainPassword))->toBeTrue();
    });

    it('returns false for incorrect plain password', function () {
        $plainPassword = 'SecurePassword123';
        $hashedPassword = HashedPassword::fromPlain($plainPassword);

        expect($hashedPassword->verify('WrongPassword'))->toBeFalse();
    });

    it('is case sensitive during verification', function () {
        $plainPassword = 'SecurePassword123';
        $hashedPassword = HashedPassword::fromPlain($plainPassword);

        expect($hashedPassword->verify('securepassword123'))->toBeFalse();
    });

    it('can be created from existing hash', function () {
        $plainPassword = 'SecurePassword123';
        $hash = password_hash($plainPassword, PASSWORD_BCRYPT);
        $hashedPassword = HashedPassword::fromHash($hash);

        expect($hashedPassword->verify($plainPassword))->toBeTrue()
            ->and($hashedPassword->value())->toBe($hash);
    });

    it('is immutable', function () {
        $plainPassword = 'SecurePassword123';
        $hashedPassword = HashedPassword::fromPlain($plainPassword);
        $originalHash = $hashedPassword->value();

        expect($hashedPassword->value())->toBe($originalHash);
    });

    it('can compare two hashes for equality', function () {
        $plainPassword = 'SecurePassword123';
        $hash = password_hash($plainPassword, PASSWORD_BCRYPT);

        $hashedPassword1 = HashedPassword::fromHash($hash);
        $hashedPassword2 = HashedPassword::fromHash($hash);

        expect($hashedPassword1->equals($hashedPassword2))->toBeTrue();
    });

    it('considers different hashes as not equal', function () {
        $hash1 = password_hash('SecurePassword123', PASSWORD_BCRYPT);
        $hash2 = password_hash('SecurePassword123', PASSWORD_BCRYPT);

        $hashedPassword1 = HashedPassword::fromHash($hash1);
        $hashedPassword2 = HashedPassword::fromHash($hash2);

        expect($hashedPassword1->equals($hashedPassword2))->toBeFalse();
    });

    it('generates different hashes for same plain password', function () {
        $plainPassword = 'SecurePassword123';
        $hashedPassword1 = HashedPassword::fromPlain($plainPassword);
        $hashedPassword2 = HashedPassword::fromPlain($plainPassword);

        expect($hashedPassword1->value())->not->toBe($hashedPassword2->value())
            ->and($hashedPassword1->verify($plainPassword))->toBeTrue()
            ->and($hashedPassword2->verify($plainPassword))->toBeTrue();
    });

    it('returns string value of hash', function () {
        $plainPassword = 'SecurePassword123';
        $hashedPassword = HashedPassword::fromPlain($plainPassword);

        expect($hashedPassword->value())->toBeString()
            ->and(strlen($hashedPassword->value()))->toBeGreaterThan(10);
    });

    it('can verify with very long password', function () {
        $longPassword = str_repeat('a', 100) . 'SecurePass123';
        $hashedPassword = HashedPassword::fromPlain($longPassword);

        expect($hashedPassword->verify($longPassword))->toBeTrue()
            ->and($hashedPassword->verify('WrongPassword'))->toBeFalse();
    });

    it('can verify with special characters in password', function () {
        $specialPassword = 'P@ssw0rd!#$%&*()_+-=[]{}|;:,.<>?';
        $hashedPassword = HashedPassword::fromPlain($specialPassword);

        expect($hashedPassword->verify($specialPassword))->toBeTrue();
    });

    it('hash size is consistent with bcrypt', function () {
        $hashedPassword = HashedPassword::fromPlain('SecurePassword123');
        $hash = $hashedPassword->value();

        // Bcrypt hashes are always 60 characters
        expect(strlen($hash))->toBe(60)
            ->and(substr($hash, 0, 4))->toBe('$2y$');
    });
});

