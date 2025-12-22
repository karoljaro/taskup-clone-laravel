<?php

namespace App\Core\domain\VO;

use App\Core\domain\Validation\SharedBusinessValidation;

final readonly class TaskId
{
    public function __construct(
        private string $value
    )
    {
        $id = trim($this->value);
        SharedBusinessValidation::validateId($id);
    }

    public function value(): string {
        return $this->value;
    }

    public function equals(TaskId $other): bool
    {
        return $this->value === $other->value;
    }
}
