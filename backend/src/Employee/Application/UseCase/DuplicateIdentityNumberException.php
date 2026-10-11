<?php

declare(strict_types=1);


namespace App\Employee\Application\UseCase;

use RuntimeException;

final class DuplicateIdentityNumberException extends RuntimeException
{
}
