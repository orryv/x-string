<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ReverseTest extends TestCase
{
    public function testReverseBasic(): void
    {
        $value = XString::new('desserts');
        $result = $value->reverse();
        self::assertSame('stressed', (string) $result);
    }

    public function testReverseGraphemes(): void
    {
        $value = XString::new("a\u{0301}b");
        $result = $value->reverse();
        self::assertSame("ba\u{0301}", (string) $result);
    }

    public function testReverseBytes(): void
    {
        $value = XString::new("a\u{0301}b")->withMode('bytes');
        $result = $value->reverse();
        self::assertSame('6281cc61', bin2hex((string) $result));
        self::assertSame(4, $result->length());
    }

    public function testReverseCodepoints(): void
    {
        $value = XString::new('👍🏽🙂');
        $grapheme = $value->reverse();
        $codepoints = $value->withMode('codepoints')->reverse();
        self::assertSame('🙂👍🏽', (string) $grapheme);
        self::assertSame("🙂🏽👍", (string) $codepoints);
    }

    public function testReverseEmpty(): void
    {
        $value = XString::new('');
        $result = $value->reverse();
        self::assertSame('', (string) $result);
    }

    public function testReverseImmutable(): void
    {
        $value = XString::new('Palindrome');
        $result = $value->reverse();
        self::assertSame('Palindrome', (string) $value);
        self::assertSame('emordnilaP', (string) $result);
    }

}
