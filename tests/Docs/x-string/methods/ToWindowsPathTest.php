<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToWindowsPathTest extends TestCase
{
    public function testWindowsPathDrive(): void
    {
        $value = XString::new('C:/Temp/Project?/Readme.txt');
        $result = $value->toWindowsPath();
        self::assertSame('C:\\Temp\\Project_\\Readme.txt', (string) $result);
    }

    public function testWindowsPathUnc(): void
    {
        $value = XString::new('\\\\Server\\Share\\AUX\\');
        $result = $value->toWindowsPath();
        self::assertSame('\\\\Server\\Share\\_AUX\\', (string) $result);
    }

    public function testWindowsPathMixed(): void
    {
        $value = XString::new('logs//..\\current');
        $result = $value->toWindowsPath();
        self::assertSame('logs\\_\\current', (string) $result);
    }

    public function testWindowsPathTrailing(): void
    {
        $value = XString::new('C:\\Temp\\');
        $result = $value->toWindowsPath();
        self::assertSame('C:\\Temp\\', (string) $result);
    }

}
