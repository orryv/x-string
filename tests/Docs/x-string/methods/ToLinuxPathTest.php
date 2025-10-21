<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToLinuxPathTest extends TestCase
{
    public function testLinuxPathMixed(): void
    {
        $value = XString::new('logs\\2024/errors');
        $result = $value->toLinuxPath();
        self::assertSame('logs/2024/errors', (string) $result);
    }

    public function testLinuxPathSpecial(): void
    {
        $value = XString::new('/etc/../passwd');
        $result = $value->toLinuxPath();
        self::assertSame('/etc/_/passwd', (string) $result);
    }

    public function testLinuxPathTrailing(): void
    {
        $value = XString::new('/var/log/');
        $result = $value->toLinuxPath();
        self::assertSame('/var/log/', (string) $result);
    }

    public function testLinuxPathEmpty(): void
    {
        $value = XString::new('   ');
        $result = $value->toLinuxPath();
        self::assertSame('_', (string) $result);
    }

}
