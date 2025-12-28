<?php

use App\Core\Domain\Exceptions\DomainError;
use App\Core\Domain\Exceptions\DomainExceptionGroup;
use App\Core\Domain\Exceptions\InvalidTitleException;
use App\Core\Domain\Exceptions\TaskNotFoundException;
use App\Core\Domain\VO\TaskId;
use App\Http\Exceptions\ExceptionToHttpMapper;
use Symfony\Component\HttpFoundation\Response;

describe('ExceptionToHttpMapper', function () {
    describe('map()', function () {
        it('maps TaskNotFoundException to 404 response', function () {
            $exception = new TaskNotFoundException(
                new TaskId('550e8400-e29b-41d4-a716-446655440000')
            );

            $response = ExceptionToHttpMapper::map($exception);

            expect($response)->toBeInstanceOf(Response::class)
                ->and($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND);
        });

        it('maps NOT_FOUND group to 404 status', function () {
            $exception = new TaskNotFoundException(
                new TaskId('550e8400-e29b-41d4-a716-446655440000')
            );

            $response = ExceptionToHttpMapper::map($exception);

            expect($response->getStatusCode())->toBe(404);
        });

        it('maps INVALID_INPUT group to 422 status', function () {
            $exception = new InvalidTitleException('Invalid title');

            $response = ExceptionToHttpMapper::map($exception);

            expect($response->getStatusCode())->toBe(422);
        });

        it('returns JSON response with error and message', function () {
            $exception = new TaskNotFoundException(
                new TaskId('550e8400-e29b-41d4-a716-446655440000')
            );

            $response = ExceptionToHttpMapper::map($exception);
            $content = json_decode($response->getContent(), true);

            expect($content)->toBeArray()
                ->and($content['error'])->toBe('not_found')
                ->and($content['message'])->toContain('550e8400-e29b-41d4-a716-446655440000');
        });

        it('sets correct Content-Type header', function () {
            $exception = new TaskNotFoundException(
                new TaskId('550e8400-e29b-41d4-a716-446655440000')
            );

            $response = ExceptionToHttpMapper::map($exception);

            expect($response->headers->get('Content-Type'))->toBe('application/json');
        });

        it('returns null for non-DomainError exceptions', function () {
            $exception = new Exception('Some error');

            $response = ExceptionToHttpMapper::map($exception);

            expect($response)->toBeNull();
        });

        it('includes exception message in response', function () {
            $taskId = '550e8400-e29b-41d4-a716-446655440000';
            $exception = new TaskNotFoundException(new TaskId($taskId));

            $response = ExceptionToHttpMapper::map($exception);
            $content = json_decode($response->getContent(), true);

            expect($content['message'])->toContain($taskId);
        });

        it('handles multiple exception types', function () {
            $invalidTitleException = new InvalidTitleException('Too short');
            $notFoundException = new TaskNotFoundException(new TaskId('550e8400-e29b-41d4-a716-446655440000'));

            $response1 = ExceptionToHttpMapper::map($invalidTitleException);
            $response2 = ExceptionToHttpMapper::map($notFoundException);

            expect($response1->getStatusCode())->toBe(422)
                ->and($response2->getStatusCode())->toBe(404);
        });

        it('maps CONFLICT group to 409 status', function () {
            // Create a custom exception for CONFLICT group to test
            $conflictException = new class extends DomainError {
                public function group(): DomainExceptionGroup
                {
                    return DomainExceptionGroup::CONFLICT;
                }
            };

            $response = ExceptionToHttpMapper::map($conflictException);

            expect($response->getStatusCode())->toBe(409);
        });
    });

    describe('response structure', function () {
        it('response is valid JSON', function () {
            $exception = new TaskNotFoundException(
                new TaskId('550e8400-e29b-41d4-a716-446655440000')
            );

            $response = ExceptionToHttpMapper::map($exception);

            $content = json_decode($response->getContent(), true);

            expect($content)->not->toBeNull();
        });

        it('response has required fields', function () {
            $exception = new InvalidTitleException('Test error');

            $response = ExceptionToHttpMapper::map($exception);
            $content = json_decode($response->getContent(), true);

            expect($content)->toHaveKeys(['error', 'message']);
        });

        it('error field matches exception group value', function () {
            $exception = new TaskNotFoundException(
                new TaskId('550e8400-e29b-41d4-a716-446655440000')
            );

            $response = ExceptionToHttpMapper::map($exception);
            $content = json_decode($response->getContent(), true);

            expect($content['error'])->toBe(
                $exception->group()->value
            );
        });
    });
});

