<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\FirstName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FirstNameTest extends TestCase
{
    public function testFromStringCreatesValidFirstName(): void
    {
        $inputName = 'Emīls';
        $firstName = FirstName::fromString($inputName);

        self::assertSame($inputName, $firstName->value);
    }


    public function testFromStringTrimsWhitespace(): void
    {
        $inputName = 'Emīls';
        $firstName = FirstName::fromString(sprintf('     %s        ', $inputName));

        self::assertSame($inputName, $firstName->value);
    }

    public function testFromStringThrowsExceptionWhenEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        FirstName::fromString('  ');
    }

    public function testFromStringThrowsExceptionWhenTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        FirstName::fromString(str_repeat('e', FirstName::MAX_LENGTH + 1));
    }
}
