<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testFromStringCreatesValidEmail(): void
    {
        $input = 'emils@example.test';
        $email = Email::fromString($input);

        self::assertSame($input, $email->value);
    }


    public function testFromStringTrimsWhitespace(): void
    {
        $input = 'emils@example.test';
        $email = Email::fromString(sprintf('     %s        ', $input));

        self::assertSame($input, $email->value);
    }

    public function testFromStringThrowsExceptionWhenEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('  ');
    }

    public function testFromStringThrowsExceptionWhenTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString(
            sprintf(
                '%s@example.test',
                str_repeat('e', Email::MAX_LENGTH)
            )
        );
    }

    public function testFromStringThrowsExceptionWhenInvalidSyntax(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('eee');
    }
}
