<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToWindowsFileNameTest extends TestCase
{
    public function testWindowsFilenameForbidden(): void
    {
        $value = XString::new('Report?.txt');
        $result = $value->toWindowsFileName();
        self::assertSame('Report_.txt', (string) $result);
    }

    public function testWindowsFilenameReserved(): void
    {
        $value = XString::new('CON');
        $result = $value->toWindowsFileName();
        self::assertSame('_CON', (string) $result);
    }

    public function testWindowsFilenameTrim(): void
    {
        $value = XString::new(' log . ');
        $result = $value->toWindowsFileName();
        self::assertSame('log', (string) $result);
    }

    public function testWindowsFilenameUnicode(): void
    {
        $value = XString::new('Résumé.txt');
        $result = $value->toWindowsFileName();
        self::assertSame('Résumé.txt', (string) $result);
    }

    public function testWindowsFilenameImmutability(): void
    {
        $value = XString::new('draft?.md');
        $value->toWindowsFileName();
        self::assertSame('draft?.md', (string) $value);
    }

}
