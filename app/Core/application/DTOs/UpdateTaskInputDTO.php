<?php

namespace App\Core\application\DTOs;

use App\Core\domain\Enums\TaskStatus;

readonly class UpdateTaskInputDTO
{
    /**
     * @param string|null $title
     * @param string|null $description
     * @param TaskStatus|null $status
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?TaskStatus $status = null
    )
    {}
}
