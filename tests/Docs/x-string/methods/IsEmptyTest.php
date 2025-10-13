<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class IsEmptyTest extends TestCase
{
    public function testIsEmptyBasic(): void
    {
        $value = XString::new("   \n   ");
        self::assertTrue($value->isEmpty());
        self::assertSame("   \n   ", (string) $value);
    }

    public function testIsEmptyNewlineExcluded(): void
    {
        $value = XString::new(" \n ");
        self::assertTrue($value->isEmpty());
        self::assertFalse($value->isEmpty(newline: false));
    }

    public function testIsEmptyNonEmpty(): void
    {
        $value = XString::new('  content  ');
        self::assertFalse($value->isEmpty());
    }

    public function testIsEmptyTabs(): void
    {
        $value = XString::new("\t");
        self::assertTrue($value->isEmpty());
        self::assertFalse($value->isEmpty(tab: false));
    }

    public function testIsEmptyZero(): void
    {
        $value = XString::new('0');
        self::assertFalse($value->isEmpty());
        self::assertFalse($value->isEmpty(newline: false, space: false, tab: false));
    }

    public function testIsEmptyTrulyEmpty(): void
    {
        $value = XString::new('');
        self::assertTrue($value->isEmpty());
    }

}
