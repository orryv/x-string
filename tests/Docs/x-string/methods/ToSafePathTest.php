<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToSafePathTest extends TestCase
{
    public function testSafePathMixed(): void
    {
        $value = XString::new('C:\\Temp\\AUX\\report?.txt');
        $result = $value->toSafePath();
        self::assertSame('C_/Temp/_AUX/report_.txt', (string) $result);
    }

    public function testSafePathPosix(): void
    {
        $value = XString::new('/etc/passwd');
        $result = $value->toSafePath();
        self::assertSame('/etc/passwd', (string) $result);
    }

    public function testSafePathSpecial(): void
    {
        $value = XString::new('../..');
        $result = $value->toSafePath();
        self::assertSame('_/_', (string) $result);
    }

    public function testSafePathEmpty(): void
    {
        $value = XString::new('   ');
        $result = $value->toSafePath();
        self::assertSame('_', (string) $result);
    }

    public function testSafePathTrailing(): void
    {
        $value = XString::new('/var/tmp/');
        $result = $value->toSafePath();
        self::assertSame('/var/tmp/', (string) $result);
    }

}
