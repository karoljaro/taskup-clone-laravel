<?php

namespace App\Core\application\Shared;

interface IdGenerator
{
    public function generate(): string;
}
