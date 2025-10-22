<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeWindowsFolderNameTest extends TestCase
{
    public function testWindowsDecodeFolderSlash(): void
    {
        $value = XString::new('config%2Fapp');
        $result = $value->decodeWindowsFolderName();
        self::assertSame('config/app', (string) $result);
    }

    public function testWindowsDecodeFolderReserved(): void
    {
        $value = XString::new('%41UX');
        $result = $value->decodeWindowsFolderName();
        self::assertSame('AUX', (string) $result);
    }

    public function testWindowsDecodeFolderTrailing(): void
    {
        $value = XString::new('data%20%2E');
        $result = $value->decodeWindowsFolderName();
        self::assertSame('data .', (string) $result);
    }

    public function testWindowsDecodeFolderControl(): void
    {
        $value = XString::new('cache%07');
        $result = $value->decodeWindowsFolderName();
        self::assertSame("cache\x07", (string) $result);
    }

}
