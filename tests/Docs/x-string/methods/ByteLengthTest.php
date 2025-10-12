<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ByteLengthTest extends TestCase
{
    public function testByteLengthAscii(): void
    {
        $xstring = XString::new('hello world');
        self::assertSame(11, $xstring->byteLength());
    }

    public function testByteLengthMultibyte(): void
    {
        $value = 'naïve façade';
        $xstring = XString::new($value);
        self::assertSame(strlen($value), $xstring->byteLength());
    }

    public function testByteLengthMode(): void
    {
        $value = "Ångström";
        $bytes = strlen($value);
        self::assertSame($bytes, XString::new($value)->byteLength());
        self::assertSame($bytes, XString::new($value)->asCodepoints()->byteLength());
        self::assertSame($bytes, XString::new($value)->asGraphemes()->byteLength());
    }

    public function testByteLengthEmoji(): void
    {
        $value = "👩‍🚀";
        self::assertSame(strlen($value), XString::new($value)->byteLength());
    }

    public function testByteLengthEmpty(): void
    {
        $xstring = XString::new('');
        self::assertSame(0, $xstring->byteLength());
    }

}
