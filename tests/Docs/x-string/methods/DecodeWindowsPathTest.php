<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeWindowsPathTest extends TestCase
{
    public function testWindowsDecodePathForbidden(): void
    {
        $value = XString::new('C:\\logs\\error%3F.txt');
        $result = $value->decodeWindowsPath();
        self::assertSame('C:\\logs\\error?.txt', (string) $result);
    }

    public function testWindowsDecodePathReserved(): void
    {
        $value = XString::new('\\\\server\\share\\%61ux');
        $result = $value->decodeWindowsPath();
        self::assertSame('\\\\server\\share\\aux', (string) $result);
    }

    public function testWindowsDecodePathTrailing(): void
    {
        $value = XString::new('C:\\data%20\\');
        $result = $value->decodeWindowsPath();
        self::assertSame('C:\\data \\', (string) $result);
    }

    public function testWindowsDecodePathPercent(): void
    {
        $value = XString::new('D:\\reports\\100%25 ready');
        $result = $value->decodeWindowsPath();
        self::assertSame('D:\\reports\\100% ready', (string) $result);
    }

}
