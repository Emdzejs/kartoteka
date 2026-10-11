<?php

namespace App\Tests\Employee\Domain\PreviousEmployment;

use App\Employee\Domain\PreviousEmployment\JobRole;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class JobRoleTest extends TestCase
{
    public function testFromStringCreatesValidJobRole(): void
    {
        $inputName = 'Programmēšanas inženieris';
        $jobRole = JobRole::fromString($inputName);

        self::assertSame($inputName, $jobRole->value);
    }


    public function testFromStringTrimsWhitespace(): void
    {
        $inputName = 'Programmēšanas inženieris';
        $jobRole = JobRole::fromString(sprintf('     %s        ', $inputName));

        self::assertSame($inputName, $jobRole->value);
    }

    public function testFromStringThrowsExceptionWhenEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        JobRole::fromString('  ');
    }

    public function testFromStringThrowsExceptionWhenTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        JobRole::fromString(str_repeat('e', JobRole::MAX_LENGTH + 1));
    }
}
