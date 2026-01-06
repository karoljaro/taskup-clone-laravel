<?php

use App\Core\Domain\Repositories\TaskRepository;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\Repositories\UserRepository;
use App\Persistence\Shared\EloquentUnitOfWork;
use Illuminate\Support\Facades\DB;

describe('EloquentUnitOfWork', function () {
    $userRepository = null;
    $taskRepository = null;
    $tokenRepository = null;
    $unitOfWork = null;

    beforeEach(function () use (&$userRepository, &$taskRepository, &$tokenRepository, &$unitOfWork) {
        $userRepository = mock(UserRepository::class);
        $taskRepository = mock(TaskRepository::class);
        $tokenRepository = mock(TokenRepository::class);

        $unitOfWork = new EloquentUnitOfWork(
            $userRepository,
            $taskRepository,
            $tokenRepository
        );
    });

    describe('transaction management', function () use (&$unitOfWork) {
        it('begins transaction', function () use (&$unitOfWork) {
            DB::shouldReceive('beginTransaction')->once();
            $unitOfWork->begin();
        });

        it('commits transaction', function () use (&$unitOfWork) {
            DB::shouldReceive('commit')->once();
            $unitOfWork->commit();

            // Test passes if no exception is thrown
            expect(true)->toBeTrue();
        });

        it('rolls back on commit failure', function () use (&$unitOfWork) {
            DB::shouldReceive('commit')->once()->andThrow(new Exception('Commit failed'));
            DB::shouldReceive('rollBack')->once();

            $unitOfWork->commit();
        })->throws(Exception::class);

        it('rolls back transaction', function () use (&$unitOfWork) {
            DB::shouldReceive('rollBack')->once();
            $unitOfWork->rollback();

            // Test passes if no exception is thrown
            expect(true)->toBeTrue();
        });
    });

    describe('repository accessors', function () use (&$unitOfWork, &$userRepository, &$taskRepository, &$tokenRepository) {
        it('returns user repository', function () use (&$unitOfWork, &$userRepository) {
            $users = $unitOfWork->users();
            expect($users)->toBe($userRepository);
        });

        it('returns task repository', function () use (&$unitOfWork, &$taskRepository) {
            $tasks = $unitOfWork->tasks();
            expect($tasks)->toBe($taskRepository);
        });

        it('returns token repository', function () use (&$unitOfWork, &$tokenRepository) {
            $tokens = $unitOfWork->tokens();
            expect($tokens)->toBe($tokenRepository);
        });

        it('caches user repository on subsequent calls', function () use (&$unitOfWork) {
            $users1 = $unitOfWork->users();
            $users2 = $unitOfWork->users();
            expect($users1)->toBe($users2);
        });

        it('caches task repository on subsequent calls', function () use (&$unitOfWork) {
            $tasks1 = $unitOfWork->tasks();
            $tasks2 = $unitOfWork->tasks();
            expect($tasks1)->toBe($tasks2);
        });

        it('caches token repository on subsequent calls', function () use (&$unitOfWork) {
            $tokens1 = $unitOfWork->tokens();
            $tokens2 = $unitOfWork->tokens();
            expect($tokens1)->toBe($tokens2);
        });
    });
});

