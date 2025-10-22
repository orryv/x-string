<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeWindowsPathTest extends TestCase
{
    public function testWindowsEncodePathForbidden(): void
    {
        $value = XString::new('C:\\logs\\error?.txt');
        $result = $value->encodeWindowsPath();
        self::assertSame('C:\\logs\\error%3F.txt', (string) $result);
    }

    public function testWindowsEncodePathReserved(): void
    {
        $value = XString::new('\\\\server\\share\\aux');
        $result = $value->encodeWindowsPath();
        self::assertSame('\\\\server\\share\\%61ux', (string) $result);
    }

    public function testWindowsEncodePathTrailing(): void
    {
        $value = XString::new('C:\\data \\');
        $result = $value->encodeWindowsPath();
        self::assertSame('C:\\data%20\\', (string) $result);
    }

    public function testWindowsEncodePathPercent(): void
    {
        $value = XString::new('D:\\reports\\100% ready');
        $result = $value->encodeWindowsPath();
        self::assertSame('D:\\reports\\100%25 ready', (string) $result);
    }

    public function testWindowsEncodePathDoubleEncodeToggle(): void
    {
        $value = XString::new('C:\\Archive%202024\\Logs?.txt');
        $noDouble = $value->encodeWindowsPath();
        $double = $value->encodeWindowsPath(true);
        self::assertSame('C:\\Archive%202024\\Logs%3F.txt', (string) $noDouble);
        self::assertSame('C:\\Archive%252024\\Logs%253F.txt', (string) $double);
    }

}
