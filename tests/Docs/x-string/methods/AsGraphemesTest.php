<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class AsGraphemesTest extends TestCase
{
    public function testAsGraphemesLength(): void
    {
        $xstring = XString::new("a\u{0301}");
        $bytes = $xstring->asBytes();
        $graphemes = $bytes->asGraphemes();
        self::assertSame(1, $graphemes->length());
        self::assertSame(3, $bytes->length());
    }

    public function testAsGraphemesAlias(): void
    {
        $xstring = XString::new('👩‍💻 developer');
        $manual = $xstring->withMode('graphemes');
        $alias = $xstring->asGraphemes();
        self::assertSame($manual->length(), $alias->length());
    }

    public function testAsGraphemesEmptyEncoding(): void
    {
        $xstring = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $xstring->asGraphemes('');
    }

}
