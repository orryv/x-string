<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeMacOSFileNameTest extends TestCase
{
    public function testMacEncodeFilenameSlash(): void
    {
        $value = XString::new('data/report.csv');
        $result = $value->encodeMacOSFileName();
        self::assertSame('data%2Freport.csv', (string) $result);
    }

    public function testMacEncodeFilenameColon(): void
    {
        $value = XString::new('audio:mix');
        $result = $value->encodeMacOSFileName();
        self::assertSame('audio%3Amix', (string) $result);
    }

    public function testMacEncodeFilenamePercent(): void
    {
        $value = XString::new('config%test');
        $result = $value->encodeMacOSFileName();
        self::assertSame('config%25test', (string) $result);
    }

    public function testMacEncodeFilenameNull(): void
    {
        $value = XString::new("file\0name");
        $result = $value->encodeMacOSFileName();
        self::assertSame('file%00name', (string) $result);
    }

    public function testMacEncodeFilenameDoubleEncodeToggle(): void
    {
        $value = XString::new('Invoices%202024:Final');
        $noDouble = $value->encodeMacOSFileName();
        $double = $value->encodeMacOSFileName(true);
        self::assertSame('Invoices%202024%3AFinal', (string) $noDouble);
        self::assertSame('Invoices%252024%253AFinal', (string) $double);
    }

}
