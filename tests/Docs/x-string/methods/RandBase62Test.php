<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidLengthException;

final class RandBase62Test extends TestCase
{
    public function testRandBase62Basic(): void
    {
        $token = XString::randBase62(20);
        self::assertSame(20, $token->length());
        self::assertMatchesRegularExpression('/^[0-9A-Za-z]{20}$/', (string) $token);
    }

    public function testRandBase62CharacterDiversity(): void
    {
        $token = XString::randBase62(60);
        $characters = str_split((string) $token);
        self::assertGreaterThanOrEqual(1, count(array_intersect($characters, range('0', '9'))));
        self::assertGreaterThanOrEqual(1, count(array_intersect($characters, range('A', 'Z'))));
        self::assertGreaterThanOrEqual(1, count(array_intersect($characters, range('a', 'z'))));
    }

    public function testRandBase62InvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        XString::randBase62(0);
    }

}
