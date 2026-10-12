<?php

declare(strict_types=1);


namespace App\Employee\Application\Exception;

use RuntimeException;

final class DuplicateIdentityNumberException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('An employee with this identity number already exists');
    }
}
