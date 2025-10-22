<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeWindowsFolderNameTest extends TestCase
{
    public function testWindowsEncodeFolderSlash(): void
    {
        $value = XString::new('config/app');
        $result = $value->encodeWindowsFolderName();
        self::assertSame('config%2Fapp', (string) $result);
    }

    public function testWindowsEncodeFolderReserved(): void
    {
        $value = XString::new('AUX');
        $result = $value->encodeWindowsFolderName();
        self::assertSame('%41UX', (string) $result);
    }

    public function testWindowsEncodeFolderTrailing(): void
    {
        $value = XString::new('data .');
        $result = $value->encodeWindowsFolderName();
        self::assertSame('data%20%2E', (string) $result);
    }

    public function testWindowsEncodeFolderControl(): void
    {
        $value = XString::new("cache\x07");
        $result = $value->encodeWindowsFolderName();
        self::assertSame('cache%07', (string) $result);
    }

    public function testWindowsEncodeFolderNameDoubleEncodeToggle(): void
    {
        $value = XString::new('Reports%202024?');
        $noDouble = $value->encodeWindowsFolderName();
        $double = $value->encodeWindowsFolderName(true);
        self::assertSame('Reports%202024%3F', (string) $noDouble);
        self::assertSame('Reports%252024%253F', (string) $double);
    }

}
