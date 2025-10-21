<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToMacOSFolderNameTest extends TestCase
{
    public function testMacosFolderColon(): void
    {
        $value = XString::new('data:exports');
        $result = $value->toMacOSFolderName();
        self::assertSame('data_exports', (string) $result);
    }

    public function testMacosFolderSlash(): void
    {
        $value = XString::new('report/summary');
        $result = $value->toMacOSFolderName();
        self::assertSame('report_summary', (string) $result);
    }

    public function testMacosFolderSpecial(): void
    {
        $value = XString::new('..');
        $result = $value->toMacOSFolderName();
        self::assertSame('_', (string) $result);
    }

    public function testMacosFolderWhitespace(): void
    {
        $value = XString::new("   ");
        $result = $value->toMacOSFolderName();
        self::assertSame('_', (string) $result);
    }

    public function testMacosFolderImmutability(): void
    {
        $value = XString::new('reports:2024');
        $value->toMacOSFolderName();
        self::assertSame('reports:2024', (string) $value);
    }

}
