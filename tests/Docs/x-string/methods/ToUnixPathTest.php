<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToUnixPathTest extends TestCase
{
    public function testUnixPathMixed(): void
    {
        $value = XString::new('logs\\2024/errors');
        $result = $value->toUnixPath();
        self::assertSame('logs/2024/errors', (string) $result);
    }

    public function testUnixPathSpecial(): void
    {
        $value = XString::new('/etc/../passwd');
        $result = $value->toUnixPath();
        self::assertSame('/etc/_/passwd', (string) $result);
    }

    public function testUnixPathTrailing(): void
    {
        $value = XString::new('/var/log/');
        $result = $value->toUnixPath();
        self::assertSame('/var/log/', (string) $result);
    }

    public function testUnixPathEmpty(): void
    {
        $value = XString::new('   ');
        $result = $value->toUnixPath();
        self::assertSame('_', (string) $result);
    }

}
