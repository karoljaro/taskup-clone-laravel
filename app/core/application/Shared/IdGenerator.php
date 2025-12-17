<?php

namespace App\core\application\Shared;

interface IdGenerator
{
    public function generate(): string;
}
