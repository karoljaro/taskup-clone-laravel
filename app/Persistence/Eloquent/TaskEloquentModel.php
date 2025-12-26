<?php

namespace App\Persistence\Eloquent;

use App\Core\Domain\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $title
 * @property string $description
 * @property TaskStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TaskEloquentModel extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'id',
        'title',
        'description',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'id' => 'string',
        'status' => TaskStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
}
