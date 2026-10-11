<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use InvalidArgumentException;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
}
