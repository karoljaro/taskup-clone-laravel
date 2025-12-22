<?php

namespace App\Core\application\DTOs;

class CreateTaskInputDTO
{
    /**
     * @param string $title
     * @param string|null $description
     */
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null
    )
    {}
}
