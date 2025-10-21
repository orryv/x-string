<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToLinuxFolderNameTest extends TestCase
{
    public function testLinuxFolderSlashes(): void
    {
        $value = XString::new('var/log');
        $result = $value->toLinuxFolderName();
        self::assertSame('var_log', (string) $result);
    }

    public function testLinuxFolderReserved(): void
    {
        $value = XString::new('.');
        $result = $value->toLinuxFolderName();
        self::assertSame('_', (string) $result);
    }

    public function testLinuxFolderWhitespace(): void
    {
        $value = XString::new("   ");
        $result = $value->toLinuxFolderName();
        self::assertSame('_', (string) $result);
    }

    public function testLinuxFolderUnicode(): void
    {
        $value = XString::new('données');
        $result = $value->toLinuxFolderName();
        self::assertSame('données', (string) $result);
    }

    public function testLinuxFolderImmutability(): void
    {
        $value = XString::new('tmp/cache');
        $value->toLinuxFolderName();
        self::assertSame('tmp/cache', (string) $value);
    }

}
