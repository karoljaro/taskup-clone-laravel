<?php

namespace App\Core\Application\DTOs;

readonly class CreateTaskInputDTO
{
    /**
     * @param string $title
     * @param string|null $description
     */
    public function __construct(
        public string $title,
        public ?string $description = null
    )
    {}
}
