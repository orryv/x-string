<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use RuntimeException;

final class ToEncodingTest extends TestCase
{
    public function testToEncodingIso(): void
    {
        $value = XString::new('Café au lait');
        $converted = $value->toEncoding('ISO-8859-1');
        self::assertSame("Caf\xE9 au lait", (string) $converted);
    }

    public function testToEncodingFrom(): void
    {
        $utf16le = pack('v*', 0x0048, 0x0069, 0x0021); // "Hi!" in UTF-16LE
        $text = XString::new($utf16le);
        $converted = $text->toEncoding('UTF-8', from_encoding: 'UTF-16LE');
        self::assertSame('Hi!', (string) $converted);
    }

    public function testToEncodingDetect(): void
    {
        $value = XString::new('Grüße');
        $ascii = $value->toEncoding('ASCII//TRANSLIT');
        self::assertSame('Grusse', (string) $ascii);
    }

    public function testToEncodingEmpty(): void
    {
        $value = XString::new('sample');
        $this->expectException(InvalidArgumentException::class);
        $value->toEncoding('   ');
    }

    public function testToEncodingInvalid(): void
    {
        $value = XString::new('content');
        $this->expectException(RuntimeException::class);
        $value->toEncoding('NO-SUCH-ENCODING');
    }

    public function testToEncodingImmutability(): void
    {
        $value = XString::new('Café');
        $value->toEncoding('ASCII//TRANSLIT');
        self::assertSame('Café', (string) $value);
    }

}
