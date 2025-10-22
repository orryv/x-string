<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeSafePathTest extends TestCase
{
    public function testSafeEncodePathDrive(): void
    {
        $value = XString::new('C:\\logs\\error?.txt');
        $result = $value->encodeSafePath();
        self::assertSame('C%3A/logs/error%3F.txt', (string) $result);
    }

    public function testSafeEncodePathUnc(): void
    {
        $value = XString::new('\\\\server\\share\\AUX');
        $result = $value->encodeSafePath();
        self::assertSame('//server/share/%41UX', (string) $result);
    }

    public function testSafeEncodePathTrailing(): void
    {
        $value = XString::new('workspace\\');
        $result = $value->encodeSafePath();
        self::assertSame('workspace/', (string) $result);
    }

    public function testSafeEncodePathPercent(): void
    {
        $value = XString::new('reports/100% ready');
        $result = $value->encodeSafePath();
        self::assertSame('reports/100%25 ready', (string) $result);
    }

    public function testSafeEncodePathDoubleEncodeToggle(): void
    {
        $value = XString::new('Archive%202024/Logs?.txt');
        $noDouble = $value->encodeSafePath();
        $double = $value->encodeSafePath(true);
        self::assertSame('Archive%202024/Logs%3F.txt', (string) $noDouble);
        self::assertSame('Archive%252024/Logs%253F.txt', (string) $double);
    }

}
