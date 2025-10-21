<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToSafeFolderNameTest extends TestCase
{
    public function testSafeFolderReserved(): void
    {
        $value = XString::new('CON?');
        $result = $value->toSafeFolderName();
        self::assertSame('CON_', (string) $result);
    }

    public function testSafeFolderUnicode(): void
    {
        $value = XString::new('Récap/2024');
        $result = $value->toSafeFolderName();
        self::assertSame('Récap_2024', (string) $result);
    }

    public function testSafeFolderSpecial(): void
    {
        $value = XString::new('..');
        $result = $value->toSafeFolderName();
        self::assertSame('_', (string) $result);
    }

    public function testSafeFolderWhitespace(): void
    {
        $value = XString::new("   ");
        $result = $value->toSafeFolderName();
        self::assertSame('_', (string) $result);
    }

    public function testSafeFolderImmutability(): void
    {
        $value = XString::new('draft?.bak');
        $value->toSafeFolderName();
        self::assertSame('draft?.bak', (string) $value);
    }

}
