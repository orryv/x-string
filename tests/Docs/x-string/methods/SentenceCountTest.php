<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class SentenceCountTest extends TestCase
{
    public function testSentenceCountMixed(): void
    {
        $paragraph = XString::new('One sentence. Two more! Is it three? Yes.');
        self::assertSame(4, $paragraph->sentenceCount());
    }

    public function testSentenceCountAbbreviation(): void
    {
        $text = XString::new('He met Dr. Strange. They spoke with Mr. Smith Jr. about the plan.');
        self::assertSame(2, $text->sentenceCount());
    }

    public function testSentenceCountNewlines(): void
    {
        $note = XString::new("Line one without punctuation\nSecond line continues");
        self::assertSame(2, $note->sentenceCount());
    }

    public function testSentenceCountEllipses(): void
    {
        $value = XString::new('Wait... still here?! Absolutely.');
        self::assertSame(3, $value->sentenceCount());
    }

    public function testSentenceCountEmpty(): void
    {
        self::assertSame(0, XString::new('')->sentenceCount());
    }

}
