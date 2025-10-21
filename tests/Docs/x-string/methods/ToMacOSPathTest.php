<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToMacOSPathTest extends TestCase
{
    public function testMacosPathColon(): void
    {
        $value = XString::new('Users:Shared/Logs');
        $result = $value->toMacOSPath();
        self::assertSame('Users_Shared/Logs', (string) $result);
    }

    public function testMacosPathTrailing(): void
    {
        $value = XString::new('/Library//Caches/');
        $result = $value->toMacOSPath();
        self::assertSame('/Library/Caches/', (string) $result);
    }

    public function testMacosPathSpecial(): void
    {
        $value = XString::new('../config');
        $result = $value->toMacOSPath();
        self::assertSame('_/config', (string) $result);
    }

    public function testMacosPathEmpty(): void
    {
        $value = XString::new('   ');
        $result = $value->toMacOSPath();
        self::assertSame('_', (string) $result);
    }

}
