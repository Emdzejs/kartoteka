<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\PhoneNumber;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function testFromStringCreatesValidPhoneNumber(): void
    {
        $input = '+37122222222';
        $phoneNumber = PhoneNumber::fromString($input);

        self::assertSame($input, $phoneNumber->value);
    }

    public function testFromStringTrimsWhitespace(): void
    {
        $input = '+37122222222';
        $phoneNumber = PhoneNumber::fromString(sprintf('     %s        ', $input));

        self::assertSame($input, $phoneNumber->value);
    }

    public function testFromStringThrowsExceptionWhenEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PhoneNumber::fromString('  ');
    }

    public function testFromStringThrowsExceptionWhenTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PhoneNumber::fromString(
            sprintf("+%s",
                str_repeat('3', PhoneNumber::MAX_LENGTH)
            )
        );
    }

    public function testFromStringThrowsExceptionWhenInvalidStart(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PhoneNumber::fromString('37122222222');
    }

    public function testFromStringThrowsExceptionWhenFirstDigitInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PhoneNumber::fromString('+07122222222');
    }

    public function testFromStringThrowsExceptionWhenIncludesLetters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PhoneNumber::fromString('+071222A2222');
    }

    public function testFromStringThrowsExceptionWhenIncludesSpaces(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PhoneNumber::fromString('+071222 2222');
    }
}
