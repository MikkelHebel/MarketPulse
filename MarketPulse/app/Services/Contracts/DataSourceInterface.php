<?php

namespace App\Services\Contracts;

interface DataSourceInterface
{
    public function fetch(): array;
}
