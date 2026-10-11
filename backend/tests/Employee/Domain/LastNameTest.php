<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\LastName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LastNameTest extends TestCase
{

    public function testFromStringCreatesValidLastName(): void
    {
        $input = 'Liepa';
        $LastName = LastName::fromString($input);

        self::assertSame($input, $LastName->value);
    }


    public function testFromStringTrimsWhitespace(): void
    {
        $input = 'Liepa';
        $LastName = LastName::fromString(sprintf('     %s        ', $input));

        self::assertSame($input, $LastName->value);
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
