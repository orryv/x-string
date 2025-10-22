<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeUnixPathTest extends TestCase
{
    public function testUnixEncodePathUnchanged(): void
    {
        $value = XString::new('logs/2024/errors');
        $result = $value->encodeUnixPath();
        self::assertSame('logs/2024/errors', (string) $result);
    }

    public function testUnixEncodePathPercent(): void
    {
        $value = XString::new('logs/data%/report');
        $result = $value->encodeUnixPath();
        self::assertSame('logs/data%25/report', (string) $result);
    }

    public function testUnixEncodePathTrailing(): void
    {
        $value = XString::new('/workspace/');
        $result = $value->encodeUnixPath();
        self::assertSame('/workspace/', (string) $result);
    }

    public function testUnixEncodePathNull(): void
    {
        $value = XString::new("/app/" . "\0" . "cache");
        $result = $value->encodeUnixPath();
        self::assertSame('/app/%00cache', (string) $result);
    }

    public function testUnixEncodePathDoubleEncodeToggle(): void
    {
        $value = XString::new('reports%202024/summary%20draft');
        $noDouble = $value->encodeUnixPath();
        $double = $value->encodeUnixPath(true);
        self::assertSame('reports%202024/summary%20draft', (string) $noDouble);
        self::assertSame('reports%252024/summary%2520draft', (string) $double);
    }

}
