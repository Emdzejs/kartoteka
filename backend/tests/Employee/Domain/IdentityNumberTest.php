<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\IdentityNumber;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IdentityNumberTest extends TestCase
{
    public function testFromStringCreatesIdentityNumber(): void
    {
        $input = '11111122222';
        $identityNumber = IdentityNumber::fromString($input);

        self::assertSame($input, $identityNumber->value);
    }

    public function testFromStringCreatesNormalizedIdentityNumber(): void
    {
        $identityNumber = IdentityNumber::fromString('111111-22222');

        self::assertSame('11111122222', $identityNumber->value);
    }

    public function testFromStringTrimsWhitespace(): void
    {
        $input = '11111122222';
        $identityNumber = IdentityNumber::fromString(sprintf('     %s        ', $input));

        self::assertSame($input, $identityNumber->value);
    }

    public function testFromStringThrowsExceptionWhenEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        IdentityNumber::fromString('  ');
    }

    public function testFromStringThrowsExceptionWhenTooShort(): void
    {
        $this->expectException(InvalidArgumentException::class);

        IdentityNumber::fromString('11-222');
    }

    public function testFromStringThrowsExceptionWhenTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        IdentityNumber::fromString(str_repeat('1', IdentityNumber::LENGTH + 1));
    }

    public function testFromStringThrowsExceptionWhenIncludesLetters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        IdentityNumber::fromString('111111-A2222');
    }

    public function testFromStringThrowsExceptionWhenWrongHyphenPosition(): void
    {
        $this->expectException(InvalidArgumentException::class);

        IdentityNumber::fromString('111-11122222');
    }

    public function testFromStringThrowsExceptionWhenMultipleHyphens(): void
    {
        $this->expectException(InvalidArgumentException::class);

        IdentityNumber::fromString('111111--22222');
    }
}
