<?php

namespace App\core\domain\VO;

use App\core\domain\Validation\SharedBusinessValidation;

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
