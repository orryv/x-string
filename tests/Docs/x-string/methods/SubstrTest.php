<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class SubstrTest extends TestCase
{
    public function testSubstrFirstWord(): void
    {
        $original = XString::new('naïve café');
        $segment = $original->substr(0, 5);
        self::assertSame('naïve', (string) $segment);
        self::assertSame('naïve café', (string) $original);
    }

    public function testSubstrMiddle(): void
    {
        $original = XString::new('The quick brown fox');
        $segment = $original->substr(4, 5);
        self::assertSame('quick', (string) $segment);
    }

    public function testSubstrNegativeStart(): void
    {
        $original = XString::new('Spacewalk');
        $segment = $original->substr(-4);
        self::assertSame('walk', (string) $segment);
    }

    public function testSubstrNegativeLength(): void
    {
        $original = XString::new('Hello World');
        $segment = $original->substr(0, -6);
        self::assertSame('Hello', (string) $segment);
    }

    public function testSubstrModeCombining(): void
    {
        $value = XString::new("a\u{0301}b");
        $graphemes = $value->substr(0, 2);
        $bytes = $value->withMode('bytes')->substr(0, 2);
        self::assertSame("a\u{0301}b", (string) $graphemes);
        self::assertSame('61cc', bin2hex((string) $bytes));
    }

    public function testSubstrModeCodepoints(): void
    {
        $value = XString::new("👍🏽!");
        $graphemeSlice = $value->substr(0, 1);
        $codepointSlice = $value->withMode('codepoints')->substr(0, 1);
        self::assertSame("👍🏽", (string) $graphemeSlice);
        self::assertSame("👍", (string) $codepointSlice);
    }

    public function testSubstrEmpty(): void
    {
        $value = XString::new('');
        $result = $value->substr(0, 3);
        self::assertSame('', (string) $result);
    }

}
