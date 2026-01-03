<?php

namespace App\Persistence\Shared;

use App\Core\Application\Ports\UnitOfWork;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Eloquent implementation of UnitOfWork pattern.
 * Manages database transactions using Laravel's DB facade.
 */
final class EloquentUnitOfWork implements UnitOfWork
{
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

    /**
     * Execute a callback within a transaction.
     *
     * Automatically commits on success, rolls back on exception.
     *
     * @param callable $callback
     * @return mixed
     * @throws Throwable If callback fails
     */
    public function transaction(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}

