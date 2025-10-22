<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeUnixFileNameTest extends TestCase
{
    public function testUnixDecodeFilenameSlash(): void
    {
        $value = XString::new('data%2Freport.csv');
        $result = $value->decodeUnixFileName();
        self::assertSame('data/report.csv', (string) $result);
    }

    public function testUnixDecodeFilenamePercent(): void
    {
        $value = XString::new('config%25test');
        $result = $value->decodeUnixFileName();
        self::assertSame('config%test', (string) $result);
    }

    public function testUnixDecodeFilenameNull(): void
    {
        $value = XString::new('file%00name');
        $result = $value->decodeUnixFileName();
        self::assertSame("file\0name", (string) $result);
    }

    public function testUnixDecodeFilenameUnicode(): void
    {
        $value = XString::new('résumé.txt');
        $result = $value->decodeUnixFileName();
        self::assertSame('résumé.txt', (string) $result);
    }

}
