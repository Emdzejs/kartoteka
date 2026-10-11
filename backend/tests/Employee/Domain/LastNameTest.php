<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\LastName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LastNameTest extends TestCase
{

    public function testFromStringCreatesValidLastName(): void
    {
        $inputName = 'Liepa';
        $LastName = LastName::fromString($inputName);

        self::assertSame($inputName, $LastName->value);
    }


    public function testFromStringTrimsWhitespace(): void
    {
        $inputName = 'Liepa';
        $LastName = LastName::fromString(sprintf('     %s        ', $inputName));

        self::assertSame($inputName, $LastName->value);
    }

    public function testFromStringThrowsExceptionWhenEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LastName::fromString('  ');
    }

    public function testFromStringThrowsExceptionWhenTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LastName::fromString(str_repeat('e', LastName::MAX_LENGTH + 1));
    }
}
