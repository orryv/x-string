<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToUnixFolderNameTest extends TestCase
{
    public function testUnixFolderSlashes(): void
    {
        $value = XString::new('var/log');
        $result = $value->toUnixFolderName();
        self::assertSame('var_log', (string) $result);
    }

    public function testUnixFolderReserved(): void
    {
        $value = XString::new('.');
        $result = $value->toUnixFolderName();
        self::assertSame('_', (string) $result);
    }

    public function testUnixFolderWhitespace(): void
    {
        $value = XString::new("   ");
        $result = $value->toUnixFolderName();
        self::assertSame('_', (string) $result);
    }

    public function testUnixFolderUnicode(): void
    {
        $value = XString::new('données');
        $result = $value->toUnixFolderName();
        self::assertSame('données', (string) $result);
    }

    public function testUnixFolderImmutability(): void
    {
        $value = XString::new('tmp/cache');
        $value->toUnixFolderName();
        self::assertSame('tmp/cache', (string) $value);
    }

}
