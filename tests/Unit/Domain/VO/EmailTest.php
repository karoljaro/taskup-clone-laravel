<?php

use App\Core\Domain\Exceptions\InvalidEmailException;
use App\Core\Domain\VO\Email;

describe('Email Value Object', function () {
    it('can be created with valid email', function () {
        $email = Email::create('john@example.com');

        expect($email)->toBeInstanceOf(Email::class)
            ->and($email->value())->toBe('john@example.com');
    });

    it('trims whitespace', function () {
        $email = Email::create('  john@example.com  ');

        expect($email->value())->toBe('john@example.com');
    });

    it('normalizes to lowercase for comparison', function () {
        $email = Email::create('JOHN@EXAMPLE.COM');

        expect($email->normalized())->toBe('john@example.com');
    });

    it('considers case-insensitive emails as equal', function () {
        $email1 = Email::create('john@example.com');
        $email2 = Email::create('JOHN@EXAMPLE.COM');
        $email3 = Email::create('John@Example.Com');

        expect($email1->equals($email2))->toBeTrue()
            ->and($email2->equals($email3))->toBeTrue()
            ->and($email1->equals($email3))->toBeTrue();
    });

    it('is immutable', function () {
        $email = Email::create('john@example.com');

        expect($email->value())->toBe('john@example.com');
    });

    it('fails with empty email', function () {
        Email::create('');
    })->throws(InvalidEmailException::class);

    it('fails with whitespace only email', function () {
        Email::create('   ');
    })->throws(InvalidEmailException::class);

    it('fails with invalid email format', function () {
        Email::create('invalid-email');
    })->throws(InvalidEmailException::class);

    it('fails with invalid format missing at symbol', function () {
        Email::create('john.example.com');
    })->throws(InvalidEmailException::class);

    it('fails with invalid format missing domain', function () {
        Email::create('john@');
    })->throws(InvalidEmailException::class);

    it('fails with email too long', function () {
        $longEmail = str_repeat('a', 250) . '@example.com';
        Email::create($longEmail);
    })->throws(InvalidEmailException::class);

    it('can be created from database value', function () {
        $email = Email::fromDatabase('john@example.com');

        expect($email->value())->toBe('john@example.com');
    });

    it('instances with same value are logically equal', function () {
        $email1 = Email::create('john@example.com');
        $email2 = Email::create('john@example.com');

        expect($email1->equals($email2))->toBeTrue();
    });

    it('considers different emails as not equal', function () {
        $email1 = Email::create('john@example.com');
        $email2 = Email::create('jane@example.com');

        expect($email1->equals($email2))->toBeFalse();
    });
});

