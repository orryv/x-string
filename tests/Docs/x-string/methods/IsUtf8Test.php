<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class IsUtf8Test extends TestCase
{
    public function testIsUtf8Valid(): void
    {
        $value = XString::new('こんにちは世界');
        self::assertTrue($value->isUtf8());
    }

    public function testIsUtf8Ascii(): void
    {
        $value = XString::new("Tabs\tand newlines\nare fine.");
        self::assertTrue($value->isUtf8());
    }

    public function testIsUtf8InvalidLeading(): void
    {
        $value = XString::new("\xC3\x28");
        self::assertFalse($value->isUtf8());
    }

    public function testIsUtf8InvalidMixed(): void
    {
        $value = XString::new("\xFF\xFEUTF-8");
        self::assertFalse($value->isUtf8());
    }

    public function testIsUtf8Immutability(): void
    {
        $value = XString::new('Grüße');
        $value->isUtf8();
        self::assertSame('Grüße', (string) $value);
    }

}
