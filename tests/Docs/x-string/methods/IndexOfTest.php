<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

final class IndexOfTest extends TestCase
{
    public function testIndexOfBasic(): void
    {
        $headline = XString::new('Welcome to XString, an immutable helper');
        self::assertSame(11, $headline->indexOf('XString'));
        self::assertFalse($headline->indexOf('missing'));
        self::assertSame('Welcome to XString, an immutable helper', (string) $headline);
    }

    public function testIndexOfArray(): void
    {
        $palette = XString::new('Palette: red, green, blue');
        self::assertSame(9, $palette->indexOf(['green', 'red']));
        self::assertSame(9, $palette->indexOf(['red', 'blue']));
    }

    public function testIndexOfReversed(): void
    {
        $phrase = XString::new('repeat repeat once');
        self::assertSame(7, $phrase->indexOf('repeat', reversed: true));
        self::assertSame(0, $phrase->indexOf('repeat'));
    }

    public function testIndexOfModes(): void
    {
        $sequence = XString::new("A👩‍🚀B");
        self::assertSame(2, $sequence->indexOf('B'));
        self::assertSame(4, $sequence->asCodepoints()->indexOf('B'));
        self::assertSame(12, $sequence->asBytes()->indexOf('B'));
    }

    public function testIndexOfHtml(): void
    {
        $markup = XString::new('<article><section class="intro">Welcome</section></article>');
        $section = HtmlTag::new('section')->withClass('intro');
        self::assertSame(9, $markup->indexOf($section));
        self::assertFalse($markup->indexOf(HtmlTag::new('aside')));
    }

    public function testIndexOfRegex(): void
    {
        $ticket = XString::new('Ticket #123 is open');
        self::assertSame(7, $ticket->indexOf(Regex::new('/#[0-9]+/')));
        self::assertFalse($ticket->indexOf(Regex::new('/#999/')));
    }

    public function testIndexOfSequential(): void
    {
        $workflow = XString::new('alpha beta gamma delta');
        self::assertSame(6, $workflow->indexOf(['alpha', 'beta'], behavior: 'sequential'));
        self::assertSame(17, $workflow->indexOf(['gamma', 'delta'], behavior: 'sequential'));
        self::assertFalse($workflow->indexOf(['beta', 'alpha'], behavior: 'sequential'));
    }

    public function testIndexOfOrGroups(): void
    {
        $workflow = XString::new('alpha beta gamma delta');
        self::assertSame(0, $workflow->indexOf([['alpha', 'beta'], ['gamma', 'delta']], behavior: 'or'));
        self::assertSame([0, 11], $workflow->indexOf([['alpha', 'beta'], ['gamma', 'delta']], behavior: 'or', limit: 0));
    }

    public function testIndexOfNewline(): void
    {
        $log = XString::new("First line\nSecond line");
        self::assertSame(10, $log->indexOf(Newline::new("\r\n")));
        self::assertSame(10, $log->indexOf(Newline::new("\n")));
    }

    public function testIndexOfLimitZero(): void
    {
        $record = XString::new('aba bab');
        self::assertSame([0, 5], $record->indexOf('ab', limit: 0));
        self::assertSame([5, 0], $record->indexOf('ab', reversed: true, limit: 0));
    }

    public function testIndexOfLimitCount(): void
    {
        $report = XString::new('one two one two one');
        self::assertSame([0, 8], $report->indexOf('one', limit: 2));
        self::assertSame([16, 8], $report->indexOf('one', reversed: true, limit: 2));
    }

    public function testIndexOfEmpty(): void
    {
        $text = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $text->indexOf('');
    }

}
