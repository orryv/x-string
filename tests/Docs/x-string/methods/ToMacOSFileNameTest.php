<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToMacOSFileNameTest extends TestCase
{
    public function testMacosFilenameColon(): void
    {
        $value = XString::new('data:export.csv');
        $result = $value->toMacOSFileName();
        self::assertSame('data_export.csv', (string) $result);
    }

    public function testMacosFilenameSlash(): void
    {
        $value = XString::new('report/summary');
        $result = $value->toMacOSFileName();
        self::assertSame('report_summary', (string) $result);
    }

    public function testMacosFilenameSpecial(): void
    {
        $value = XString::new('..');
        $result = $value->toMacOSFileName();
        self::assertSame('_', (string) $result);
    }

    public function testMacosFilenameWhitespace(): void
    {
        $value = XString::new("   ");
        $result = $value->toMacOSFileName();
        self::assertSame('_', (string) $result);
    }

}
