<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class AsCodepointsTest extends TestCase
{
    public function testAsCodepointsLength(): void
    {
        $xstring = XString::new("a\u{0301}");
        $codepoints = $xstring->asCodepoints();
        self::assertSame(2, $codepoints->length());
        self::assertSame(1, $xstring->length());
    }

    public function testAsCodepointsAlias(): void
    {
        $xstring = XString::new('Résumé');
        $manual = $xstring->withMode('codepoints');
        $alias = $xstring->asCodepoints();
        self::assertSame($manual->length(), $alias->length());
    }

    public function testAsCodepointsEmptyEncoding(): void
    {
        $xstring = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $xstring->asCodepoints('');
    }

    public function testAsCodepointsImmutable(): void
    {
        $emoji = XString::new('👩‍💻');
        $codepoints = $emoji->asCodepoints();
        self::assertSame(3, $codepoints->length());
        self::assertSame(1, $emoji->length());
        self::assertNotSame($emoji, $codepoints);
    }

    public function testAsCodepointsTrimEncoding(): void
    {
        $value = XString::new('Résumé');
        $codepoints = $value->asCodepoints("  UTF-8  ");
        $lower = $codepoints->toLower();
        self::assertSame('résumé', (string) $lower);
        self::assertSame('Résumé', (string) $value);
    }

}
