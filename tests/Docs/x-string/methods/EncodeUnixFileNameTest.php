<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeUnixFileNameTest extends TestCase
{
    public function testUnixEncodeFilenameSlash(): void
    {
        $value = XString::new('data/report.csv');
        $result = $value->encodeUnixFileName();
        self::assertSame('data%2Freport.csv', (string) $result);
    }

    public function testUnixEncodeFilenamePercent(): void
    {
        $value = XString::new('config%test');
        $result = $value->encodeUnixFileName();
        self::assertSame('config%25test', (string) $result);
    }

    public function testUnixEncodeFilenameNull(): void
    {
        $value = XString::new("file\0name");
        $result = $value->encodeUnixFileName();
        self::assertSame('file%00name', (string) $result);
    }

    public function testUnixEncodeFilenameUnicode(): void
    {
        $value = XString::new('résumé.txt');
        $result = $value->encodeUnixFileName();
        self::assertSame('résumé.txt', (string) $result);
    }

    public function testUnixEncodeFilenameDoubleEncodeToggle(): void
    {
        $value = XString::new('Revenue%202024/report');
        $noDouble = $value->encodeUnixFileName();
        $double = $value->encodeUnixFileName(true);
        self::assertSame('Revenue%202024%2Freport', (string) $noDouble);
        self::assertSame('Revenue%252024%2Freport', (string) $double);
    }

}
