<?php

namespace App\core\domain\VO;

final readonly class TaskId
{
    public function __construct(
        private string $value
    )
    {
        // TODO: Add validations
    }

    public function value(): string {
        return $this->value;
    }

    public function equals(TaskId $other): bool
    {
        return $this->value === $other->value;
    }
}
