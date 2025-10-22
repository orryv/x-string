<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeUnixPathTest extends TestCase
{
    public function testUnixDecodePathPercent(): void
    {
        $value = XString::new('logs/data%25/report');
        $result = $value->decodeUnixPath();
        self::assertSame('logs/data%/report', (string) $result);
    }

    public function testUnixDecodePathTrailing(): void
    {
        $value = XString::new('/workspace/');
        $result = $value->decodeUnixPath();
        self::assertSame('/workspace/', (string) $result);
    }

    public function testUnixDecodePathNull(): void
    {
        $value = XString::new('/app/%00cache');
        $result = $value->decodeUnixPath();
        self::assertSame("/app/" . "\0" . "cache", (string) $result);
    }

    public function testUnixDecodePathUnchanged(): void
    {
        $value = XString::new('logs/2024/errors');
        $result = $value->decodeUnixPath();
        self::assertSame('logs/2024/errors', (string) $result);
    }

}
