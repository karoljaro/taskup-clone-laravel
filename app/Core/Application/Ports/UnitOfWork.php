<?php

namespace App\Core\Application\Ports;

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

    /**
     * Execute a callback within a transaction.
     *
     * Automatically commits on success, rolls back on exception.
     *
     * @param callable $callback
     * @return mixed
     * @throws Exception If callback fails (exception is re-thrown after rollback)
     */
    public function transaction(callable $callback): mixed;
}

