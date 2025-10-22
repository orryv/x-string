<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeWindowsFileNameTest extends TestCase
{
    public function testWindowsDecodeFilenameForbidden(): void
    {
        $value = XString::new('Report%3F.txt');
        $result = $value->decodeWindowsFileName();
        self::assertSame('Report?.txt', (string) $result);
    }

    public function testWindowsDecodeFilenameReserved(): void
    {
        $value = XString::new('%43ON');
        $result = $value->decodeWindowsFileName();
        self::assertSame('CON', (string) $result);
    }

    public function testWindowsDecodeFilenameTrailing(): void
    {
        $value = XString::new('log%20%2E');
        $result = $value->decodeWindowsFileName();
        self::assertSame('log .', (string) $result);
    }

    public function testWindowsDecodeFilenamePercent(): void
    {
        $value = XString::new('Invoice 100%25 complete');
        $result = $value->decodeWindowsFileName();
        self::assertSame('Invoice 100% complete', (string) $result);
    }

}
