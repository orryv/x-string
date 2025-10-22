<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeSafePathTest extends TestCase
{
    public function testSafeDecodePathDrive(): void
    {
        $value = XString::new('C%3A/logs/error%3F.txt');
        $result = $value->decodeSafePath();
        self::assertSame('C:/logs/error?.txt', (string) $result);
    }

    public function testSafeDecodePathUnc(): void
    {
        $value = XString::new('//server/share/%41UX');
        $result = $value->decodeSafePath();
        self::assertSame('//server/share/AUX', (string) $result);
    }

    public function testSafeDecodePathTrailing(): void
    {
        $value = XString::new('workspace/');
        $result = $value->decodeSafePath();
        self::assertSame('workspace/', (string) $result);
    }

    public function testSafeDecodePathPercent(): void
    {
        $value = XString::new('reports/100%25 ready');
        $result = $value->decodeSafePath();
        self::assertSame('reports/100% ready', (string) $result);
    }

}
