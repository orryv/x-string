<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeWindowsFileNameTest extends TestCase
{
    public function testWindowsEncodeFilenameForbidden(): void
    {
        $value = XString::new('Report?.txt');
        $result = $value->encodeWindowsFileName();
        self::assertSame('Report%3F.txt', (string) $result);
    }

    public function testWindowsEncodeFilenameReserved(): void
    {
        $value = XString::new('CON');
        $result = $value->encodeWindowsFileName();
        self::assertSame('%43ON', (string) $result);
    }

    public function testWindowsEncodeFilenameTrailing(): void
    {
        $value = XString::new('log .');
        $result = $value->encodeWindowsFileName();
        self::assertSame('log%20%2E', (string) $result);
    }

    public function testWindowsEncodeFilenamePercent(): void
    {
        $value = XString::new('Invoice 100% complete');
        $result = $value->encodeWindowsFileName();
        self::assertSame('Invoice 100%25 complete', (string) $result);
    }

    public function testWindowsEncodeFilenameDoubleEncodeToggle(): void
    {
        $value = XString::new('Archive%202024?.zip');
        $noDouble = $value->encodeWindowsFileName();
        $double = $value->encodeWindowsFileName(true);
        self::assertSame('Archive%202024%3F.zip', (string) $noDouble);
        self::assertSame('Archive%252024%253F.zip', (string) $double);
    }

}
