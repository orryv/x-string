<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToWindowsFolderNameTest extends TestCase
{
    public function testWindowsFolderForbidden(): void
    {
        $value = XString::new('Reports?:2024');
        $result = $value->toWindowsFolderName();
        self::assertSame('Reports__2024', (string) $result);
    }

    public function testWindowsFolderReserved(): void
    {
        $value = XString::new('NUL');
        $result = $value->toWindowsFolderName();
        self::assertSame('_NUL', (string) $result);
    }

    public function testWindowsFolderTrim(): void
    {
        $value = XString::new(' logs . ');
        $result = $value->toWindowsFolderName();
        self::assertSame('logs', (string) $result);
    }

    public function testWindowsFolderUnicode(): void
    {
        $value = XString::new('Réunion');
        $result = $value->toWindowsFolderName();
        self::assertSame('Réunion', (string) $result);
    }

    public function testWindowsFolderImmutability(): void
    {
        $value = XString::new('temp?.tmp');
        $value->toWindowsFolderName();
        self::assertSame('temp?.tmp', (string) $value);
    }

}
