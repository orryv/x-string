<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

final class BetweenTest extends TestCase
{
    public function testBetweenBasic(): void
    {
        $text = XString::new('Hello [World] Example');
        $result = $text->between('[', ']');
        self::assertSame('World', (string) $result);
        self::assertSame('Hello [World] Example', (string) $text);
    }

    public function testBetweenSkipStart(): void
    {
        $template = XString::new('{{first}} {{second}} {{third}}');
        $result = $template->between('{{', '}}', skip_start: 1);
        self::assertSame('second', (string) $result);
    }

    public function testBetweenSkipEnd(): void
    {
        $text = XString::new('[first|inner|final] tail');
        $result = $text->between('[', '|', skip_end: 1);
        self::assertSame('first|inner', (string) $result);
    }

    public function testBetweenReversed(): void
    {
        $text = XString::new('Start [first] Middle [second] End');
        $result = $text->between('[', ']', last_occurence: true);
        self::assertSame('second', (string) $result);
    }

    public function testBetweenSequences(): void
    {
        $html = XString::new('<article><section><p>Body</p></section></article>');
        $result = $html->between(['<article>', '<section>', '<p>'], ['</p>', '</section>']);
        self::assertSame('Body', (string) $result);
    }

    public function testBetweenMixedSequential(): void
    {
        $text = XString::new("<section>\nID: 42\n</section>");
        $result = $text->between(
            [HtmlTag::new('section'), Newline::new("\n"), 'ID: '],
            [Newline::new("\n"), HtmlTag::closeTag('section')]
        );
        self::assertSame('42', (string) $result);
    }

    public function testBetweenOrBehavior(): void
    {
        $text = XString::new('<title>Hello</title> {World}');
        $result = $text->between(['<title>', '{'], ['</title>', '}'], start_behavior: 'or', end_behavior: 'or');
        self::assertSame('Hello', (string) $result);
    }

    public function testBetweenMixedOr(): void
    {
        $text = XString::new("<value>100</value>\n{200}\n<result>300</result>\n");
        $result = $text->between(
            [
                HtmlTag::new('value'),
                '{',
                [Newline::new("\n"), '<result>'],
            ],
            [
                HtmlTag::closeTag('value'),
                '}',
                [Regex::new('</result>'), Newline::new("\n")],
            ],
            start_behavior: 'or',
            end_behavior: 'or'
        );
        self::assertSame('100', (string) $result);
    }

    public function testBetweenMissing(): void
    {
        $text = XString::new('No brackets here');
        $result = $text->between('[', ']');
        self::assertSame('', (string) $result);
    }

    public function testBetweenInvalidSkip(): void
    {
        $text = XString::new('Example content');
        $this->expectException(InvalidArgumentException::class);
        $text->between('[', ']', skip_start: -1);
    }

}
