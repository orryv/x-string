<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class AsBytesTest extends TestCase
{
    public function testAsBytesLength(): void
    {
        $xstring = XString::new("a\u{0301}");
        $bytes = $xstring->asBytes();
        self::assertSame(3, $bytes->length());
        self::assertSame(1, $xstring->length());
    }

    public function testAsBytesEncoding(): void
    {
        $xstring = XString::new('hello');
        $iso = $xstring->asBytes('ISO-8859-1');
        $upper = $iso->toUpper();
        self::assertSame('HELLO', (string) $upper);
        self::assertSame('hello', (string) $xstring);
    }

    public function testAsBytesEmptyEncoding(): void
    {
        $xstring = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $xstring->asBytes('');
    }

    public function testAsBytesImmutable(): void
    {
        $emoji = XString::new('👩‍💻');
        $bytes = $emoji->asBytes();
        self::assertSame(11, $bytes->length());
        self::assertSame(1, $emoji->length());
        self::assertNotSame($emoji, $bytes);
    }

    public function testAsBytesTrimEncoding(): void
    {
        $value = XString::new('Résumé');
        $bytes = $value->asBytes('  UTF-8  ');
        $upper = $bytes->toUpper();
        self::assertSame('RÉSUMÉ', (string) $upper);
        self::assertSame('Résumé', (string) $value);
    }

}
