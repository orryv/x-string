<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeSafeFileNameTest extends TestCase
{
    public function testSafeEncodeFilenameForbidden(): void
    {
        $value = XString::new('Report?.txt');
        $result = $value->encodeSafeFileName();
        self::assertSame('Report%3F.txt', (string) $result);
    }

    public function testSafeEncodeFilenameReserved(): void
    {
        $value = XString::new('CON');
        $result = $value->encodeSafeFileName();
        self::assertSame('%43ON', (string) $result);
    }

    public function testSafeEncodeFilenameTrailing(): void
    {
        $value = XString::new('log .');
        $result = $value->encodeSafeFileName();
        self::assertSame('log%20%2E', (string) $result);
    }

    public function testSafeEncodeFilenamePercent(): void
    {
        $value = XString::new('Invoice 100% complete');
        $result = $value->encodeSafeFileName();
        self::assertSame('Invoice 100%25 complete', (string) $result);
    }

    public function testSafeEncodeFilenameDoubleEncodeToggle(): void
    {
        $value = XString::new('Archive%202024?.zip');
        $noDouble = $value->encodeSafeFileName();
        $double = $value->encodeSafeFileName(true);
        self::assertSame('Archive%202024%3F.zip', (string) $noDouble);
        self::assertSame('Archive%252024%253F.zip', (string) $double);
    }

}
