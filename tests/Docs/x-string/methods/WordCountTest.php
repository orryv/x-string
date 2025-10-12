<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class WordCountTest extends TestCase
{
    public function testWordCountPunctuation(): void
    {
        $sentence = XString::new('Hello, world! This is 2024.');
        self::assertSame(5, $sentence->wordCount());
        self::assertSame('Hello, world! This is 2024.', (string) $sentence);
    }

    public function testWordCountSpacing(): void
    {
        $messy = XString::new("alpha\t\t beta    gamma\u{00A0}delta");
        self::assertSame(4, $messy->wordCount());
        self::assertSame(['alpha', 'beta', 'gamma', 'delta'], $messy->words(trim: true));
    }

    public function testWordCountNewlines(): void
    {
        $text = XString::new("line one\nline two\rline three");
        self::assertSame(6, $text->wordCount());
    }

    public function testWordCountEmpty(): void
    {
        self::assertSame(0, XString::new('')->wordCount());
    }

}
