<?php

namespace App\Core\Application\Ports;

use App\Core\Domain\Repositories\TaskRepository;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\Repositories\UserRepository;
use Exception;

/**
 * UnitOfWork pattern port.
 * Coordinates database transactions for multiple repositories.
 *
 * Ensures atomicity of operations - either all succeed or all rollback on failure.
 */
interface UnitOfWork
{
    /**
     * Begin a database transaction.
     *
     * @return void
     */
    public function begin(): void;

    /**
     * Commit all changes in the transaction.
     *
     * @return void
     * @throws Exception If commit fails
     */
    public function commit(): void;

    /**
     * Rollback all changes in the transaction.
     *
     * @return void
     */
    public function rollback(): void;

    // ==========================[ REPOSITORY ACCESSORS ] ==========================

    public function users(): UserRepository;

    public function tasks(): TaskRepository;

    public function tokens(): TokenRepository;
}

