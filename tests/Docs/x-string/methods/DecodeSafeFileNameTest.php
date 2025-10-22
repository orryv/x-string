<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeSafeFileNameTest extends TestCase
{
    public function testSafeDecodeFilenameForbidden(): void
    {
        $value = XString::new('Report%3F.txt');
        $result = $value->decodeSafeFileName();
        self::assertSame('Report?.txt', (string) $result);
    }

    public function testSafeDecodeFilenameReserved(): void
    {
        $value = XString::new('%43ON');
        $result = $value->decodeSafeFileName();
        self::assertSame('CON', (string) $result);
    }

    public function testSafeDecodeFilenameTrailing(): void
    {
        $value = XString::new('log%20%2E');
        $result = $value->decodeSafeFileName();
        self::assertSame('log .', (string) $result);
    }

    public function testSafeDecodeFilenamePercent(): void
    {
        $value = XString::new('Invoice 100%25 complete');
        $result = $value->decodeSafeFileName();
        self::assertSame('Invoice 100% complete', (string) $result);
    }

}
