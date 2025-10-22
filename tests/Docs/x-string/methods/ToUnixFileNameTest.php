<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToUnixFileNameTest extends TestCase
{
    public function testUnixFilenameSlashes(): void
    {
        $value = XString::new('logs/error.log');
        $result = $value->toUnixFileName();
        self::assertSame('logs_error.log', (string) $result);
    }

    public function testUnixFilenameSpecial(): void
    {
        $value = XString::new('..');
        $result = $value->toUnixFileName();
        self::assertSame('_', (string) $result);
    }

    public function testUnixFilenameWhitespace(): void
    {
        $value = XString::new("   ");
        $result = $value->toUnixFileName();
        self::assertSame('_', (string) $result);
    }

    public function testUnixFilenameUnicode(): void
    {
        $value = XString::new('résumé.txt');
        $result = $value->toUnixFileName();
        self::assertSame('résumé.txt', (string) $result);
    }

}
