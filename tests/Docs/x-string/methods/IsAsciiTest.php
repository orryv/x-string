<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class IsAsciiTest extends TestCase
{
    public function testIsAsciiPlain(): void
    {
        $value = XString::new("Hello, World!\n");
        self::assertTrue($value->isAscii());
    }

    public function testIsAsciiAccented(): void
    {
        $value = XString::new('Café');
        self::assertFalse($value->isAscii());
    }

    public function testIsAsciiEmoji(): void
    {
        $value = XString::new('こんにちは 😊');
        self::assertFalse($value->isAscii());
    }

    public function testIsAsciiEmpty(): void
    {
        $value = XString::new('');
        self::assertTrue($value->isAscii());
    }

}
