<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeMacOSFileNameTest extends TestCase
{
    public function testMacDecodeFilenameSlash(): void
    {
        $value = XString::new('data%2Freport.csv');
        $result = $value->decodeMacOSFileName();
        self::assertSame('data/report.csv', (string) $result);
    }

    public function testMacDecodeFilenameColon(): void
    {
        $value = XString::new('audio%3Amix');
        $result = $value->decodeMacOSFileName();
        self::assertSame('audio:mix', (string) $result);
    }

    public function testMacDecodeFilenamePercent(): void
    {
        $value = XString::new('config%25test');
        $result = $value->decodeMacOSFileName();
        self::assertSame('config%test', (string) $result);
    }

    public function testMacDecodeFilenameNull(): void
    {
        $value = XString::new('file%00name');
        $result = $value->decodeMacOSFileName();
        self::assertSame("file\0name", (string) $result);
    }

}
