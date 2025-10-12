<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class LineCountTest extends TestCase
{
    public function testLineCountMixed(): void
    {
        $text = "alpha\nbravo\rcharlie\r\ndelta";
        self::assertSame(4, XString::new($text)->lineCount());
    }

    public function testLineCountTrailing(): void
    {
        $value = XString::new("First\nSecond\n");
        self::assertSame(3, $value->lineCount());
        self::assertSame(['First', 'Second', ''], $value->lines());
    }

    public function testLineCountEmpty(): void
    {
        self::assertSame(0, XString::new('')->lineCount());
    }

    public function testLineCountImmutability(): void
    {
        $original = XString::new("One\nTwo");
        $lines = $original->lineCount();
        self::assertSame(2, $lines);
        self::assertSame("One\nTwo", (string) $original);
    }

}
