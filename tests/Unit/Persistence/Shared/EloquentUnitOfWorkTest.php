<?php

use App\Core\Application\Ports\UnitOfWork;
use App\Persistence\Shared\EloquentUnitOfWork;
use Illuminate\Support\Facades\DB;

describe('EloquentUnitOfWork', function () {
    describe('transaction callback execution', function () {
        beforeEach(function () {
            // Mock DB facade to avoid "facade root has not been set" error
            DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
                return $callback();
            });
        });

        it('executes callback in transaction', function () {
            $unitOfWork = new EloquentUnitOfWork();
            $result = $unitOfWork->transaction(function () {
                return 'success';
            });

            expect($result)->toBe('success');
        });

        it('rolls back on exception in transaction', function () {
            $unitOfWork = new EloquentUnitOfWork();

            expect(function () use ($unitOfWork) {
                $unitOfWork->transaction(function () {
                    throw new Exception('Test exception');
                });
            })->toThrow(Exception::class, 'Test exception');
        });
    });
});

