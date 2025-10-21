<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToLinuxFileNameTest extends TestCase
{
    public function testLinuxFilenameSlashes(): void
    {
        $value = XString::new('logs/error.log');
        $result = $value->toLinuxFileName();
        self::assertSame('logs_error.log', (string) $result);
    }

    public function testLinuxFilenameSpecial(): void
    {
        $value = XString::new('..');
        $result = $value->toLinuxFileName();
        self::assertSame('_', (string) $result);
    }

    public function testLinuxFilenameWhitespace(): void
    {
        $value = XString::new("   ");
        $result = $value->toLinuxFileName();
        self::assertSame('_', (string) $result);
    }

    public function testLinuxFilenameUnicode(): void
    {
        $value = XString::new('résumé.txt');
        $result = $value->toLinuxFileName();
        self::assertSame('résumé.txt', (string) $result);
    }

}
