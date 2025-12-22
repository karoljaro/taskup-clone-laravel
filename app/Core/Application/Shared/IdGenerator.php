<?php

namespace App\Core\Application\Shared;

interface IdGenerator
{
    public function generate(): string;
}
