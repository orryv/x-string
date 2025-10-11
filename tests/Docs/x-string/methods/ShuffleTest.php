<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ShuffleTest extends TestCase
{
    public function testShuffleSeeded(): void
    {
        mt_srand(1234);
        $value = XString::new('abcd');
        $result = $value->shuffle();
        self::assertSame('bcad', (string) $result);
    }

    public function testShuffleGraphemes(): void
    {
        mt_srand(2);
        $value = XString::new("a\u{0301}b");
        $result = $value->shuffle();
        self::assertSame("ba\u{0301}", (string) $result);
    }

    public function testShuffleBytes(): void
    {
        mt_srand(5);
        $value = XString::new("a\u{0301}b")->withMode('bytes');
        $result = $value->shuffle();
        self::assertSame(4, $result->length());
        self::assertSame('81cc6162', bin2hex((string) $result));
    }

    public function testShuffleCodepoints(): void
    {
        mt_srand(13);
        $value = XString::new('🇳🇱🇩🇪🇧🇪');
        $graphemeShuffle = $value->shuffle();
        mt_srand(13);
        $codepointShuffle = $value->withMode('codepoints')->shuffle();
        self::assertSame('🇧🇪🇳🇱🇩🇪', (string) $graphemeShuffle);
        self::assertSame('🇱🇳🇪🇪🇩🇧', (string) $codepointShuffle);
    }

    public function testShuffleEmpty(): void
    {
        $value = XString::new('');
        $result = $value->shuffle();
        self::assertSame('', (string) $result);
    }

    public function testShuffleImmutable(): void
    {
        mt_srand(99);
        $value = XString::new('loop');
        $result = $value->shuffle();
        self::assertSame('loop', (string) $value);
        self::assertSame('lopo', (string) $result);
    }

}
