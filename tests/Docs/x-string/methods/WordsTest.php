<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class WordsTest extends TestCase
{
    public function testWordsBasic(): void
    {
        $words = XString::new('lorem ipsum dolor')->words();
        self::assertSame(['lorem', 'ipsum', 'dolor'], $words);
    }

    public function testWordsMixedWhitespace(): void
    {
        $words = XString::new("  alpha\tbeta\n\u{00A0}gamma  ")->words();
        self::assertSame(['alpha', 'beta', 'gamma'], $words);
    }

    public function testWordsTrim(): void
    {
        $words = XString::new("'Hello,' \"world!\"")->words(trim: true);
        self::assertSame(['Hello', 'world'], $words);
    }

    public function testWordsLimit(): void
    {
        $words = XString::new('one two three four')->words(limit: 3);
        self::assertSame(['one', 'two', 'three four'], $words);
    }

    public function testWordsMode(): void
    {
        $xstring = XString::new('Ångström är här')->withMode('bytes');
        $words = $xstring->words();
        self::assertSame(['Ångström', 'är', 'här'], $words);
    }

    public function testWordsTrimEmpty(): void
    {
        $words = XString::new('... --- ...')->words(trim: true);
        self::assertSame([], $words);
    }

    public function testWordsEmpty(): void
    {
        $words = XString::new('')->words();
        self::assertSame([], $words);
    }

    public function testWordsInvalidLimit(): void
    {
        $xstring = XString::new('alpha beta');
        $this->expectException(InvalidArgumentException::class);
        $xstring->words(limit: 0);
    }

}
