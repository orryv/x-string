<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class WithModeTest extends TestCase
{
    public function testWithModeBytes(): void
    {
        $xstring = XString::new("a\u{0301}");
        $bytes = $xstring->withMode('bytes');
        self::assertSame(1, $xstring->length());
        self::assertSame(3, $bytes->length());
    }

    public function testWithModeCodepoints(): void
    {
        $xstring = XString::new("a\u{0301}");
        $codepoints = $xstring->withMode('codepoints');
        self::assertSame(2, $codepoints->length());
        self::assertSame(1, $xstring->length());
    }

    public function testWithModeEncoding(): void
    {
        $xstring = XString::new('hello world');
        $iso = $xstring->withMode('graphemes', 'ISO-8859-1');
        $upper = $iso->toUpper();
        self::assertSame('HELLO WORLD', (string) $upper);
        self::assertSame('hello world', (string) $xstring);
    }

    public function testWithModeInvalidMode(): void
    {
        $xstring = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $xstring->withMode('invalid');
    }

    public function testWithModeEmptyEncoding(): void
    {
        $xstring = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $xstring->withMode('codepoints', '');
    }

    public function testWithModeCaseInsensitive(): void
    {
        $xstring = XString::new('é');
        $bytes = $xstring->withMode('BYTES');
        self::assertSame(2, $bytes->length());
        self::assertNotSame($xstring, $bytes);
    }

    public function testWithModeEncodingLength(): void
    {
        $word = XString::new('Ångström');
        $utf8 = $word->withMode('codepoints', 'UTF-8');
        $iso = $word->withMode('codepoints', 'ISO-8859-1');
        self::assertSame(8, $utf8->length());
        self::assertSame(10, $iso->length());
        self::assertSame('Ångström', (string) $word);
    }

    public function testWithModeRoundTrip(): void
    {
        $emoji = XString::new('👩‍💻');
        $bytes = $emoji->withMode('bytes');
        $graphemes = $bytes->withMode('graphemes');
        self::assertSame(11, $bytes->length());
        self::assertSame(1, $graphemes->length());
        self::assertSame('👩‍💻', (string) $emoji);
    }

}
