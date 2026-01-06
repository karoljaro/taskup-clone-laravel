<?php

namespace App\Persistence\Shared;

use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Repositories\TaskRepository;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Eloquent implementation of UnitOfWork pattern.
 *
 * Manages database transactions and provides access to repositories.
 * Repositories are lazy-loaded on first access.
 */
final class EloquentUnitOfWork implements UnitOfWork
{
    // Repository caches (lazy-loaded)
    private ?UserRepository $userRepository = null;
    private ?TaskRepository $taskRepository = null;
    private ?TokenRepository $tokenRepository = null;

    /**
     * @param UserRepository $eloquentUserRepository
     * @param TaskRepository $eloquentTaskRepository
     * @param TokenRepository $eloquentTokenRepository
     */
    public function __construct(
        private readonly UserRepository $eloquentUserRepository,
        private readonly TaskRepository $eloquentTaskRepository,
        private readonly TokenRepository $eloquentTokenRepository
    ) {}

    // ==========================[ TRANSACTION MANAGEMENT ] ==========================

    /**
     * Begin a database transaction.
     *
     * @return void
     * @throws Throwable If transaction cannot be started
     */
    public function begin(): void
    {
        DB::beginTransaction();
    }

    /**
     * Commit all changes in the transaction.
     *
     * @return void
     * @throws Throwable If commit fails
     */
    public function commit(): void
    {
        try {
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Rollback all changes in the transaction.
     *
     * @return void
     * @throws Throwable If rollback fails
     */
    public function rollback(): void
    {
        DB::rollBack();
    }


    // ==========================[ REPOSITORY ACCESSORS ] ==========================

    /**
     * Get the UserRepository instance.
     * Lazy-loads the repository on first access.
     *
     * @return UserRepository
     */
    public function users(): UserRepository
    {
        if ($this->userRepository === null) {
            $this->userRepository = $this->eloquentUserRepository;
        }
        return $this->userRepository;
    }

    /**
     * Get the TaskRepository instance.
     * Lazy-loads the repository on first access.
     *
     * @return TaskRepository
     */
    public function tasks(): TaskRepository
    {
        if ($this->taskRepository === null) {
            $this->taskRepository = $this->eloquentTaskRepository;
        }
        return $this->taskRepository;
    }

    /**
     * Get the TokenRepository instance.
     * Lazy-loads the repository on first access.
     *
     * @return TokenRepository
     */
    public function tokens(): TokenRepository
    {
        if ($this->tokenRepository === null) {
            $this->tokenRepository = $this->eloquentTokenRepository;
        }
        return $this->tokenRepository;
    }
}

