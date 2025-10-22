<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeMacOSFolderNameTest extends TestCase
{
    public function testMacEncodeFolderSlash(): void
    {
        $value = XString::new('config/nginx');
        $result = $value->encodeMacOSFolderName();
        self::assertSame('config%2Fnginx', (string) $result);
    }

    public function testMacEncodeFolderColon(): void
    {
        $value = XString::new('cache:tmp');
        $result = $value->encodeMacOSFolderName();
        self::assertSame('cache%3Atmp', (string) $result);
    }

    public function testMacEncodeFolderPercent(): void
    {
        $value = XString::new('cache%data');
        $result = $value->encodeMacOSFolderName();
        self::assertSame('cache%25data', (string) $result);
    }

    public function testMacEncodeFolderUnicode(): void
    {
        $value = XString::new('データ');
        $result = $value->encodeMacOSFolderName();
        self::assertSame('データ', (string) $result);
    }

    public function testMacEncodeFolderNameDoubleEncodeToggle(): void
    {
        $value = XString::new('Projects%202024:Specs');
        $noDouble = $value->encodeMacOSFolderName();
        $double = $value->encodeMacOSFolderName(true);
        self::assertSame('Projects%202024%3ASpecs', (string) $noDouble);
        self::assertSame('Projects%252024%253ASpecs', (string) $double);
    }

}
