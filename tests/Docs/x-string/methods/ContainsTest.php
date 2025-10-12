<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use ValueError;

final class ContainsTest extends TestCase
{
    public function testContainsBasic(): void
    {
        $message = XString::new('Status: processing order #42');
        self::assertTrue($message->contains('order'));
        self::assertFalse($message->contains('shipped'));
        self::assertSame('Status: processing order #42', (string) $message);
    }

    public function testContainsArray(): void
    {
        $body = XString::new('Choose blue or green.');
        self::assertTrue($body->contains(['red', 'green']));
        self::assertFalse($body->contains(['cyan', 'magenta']));
    }

    public function testContainsNewline(): void
    {
        $log = XString::new("First line\nSecond line");
        self::assertTrue($log->contains(Newline::new("\r\n")));
        self::assertTrue($log->contains(Newline::new("\n")));
    }

    public function testContainsRegex(): void
    {
        $text = XString::new('Ticket #123 is open');
        self::assertTrue($text->contains(Regex::new('/#\d+/')));
        self::assertFalse($text->contains(Regex::new('/#99/')));
    }

    public function testContainsHtml(): void
    {
        $html = XString::new('<article><section class="intro">Welcome</section></article>');
        $section = HtmlTag::new('section')->withClass('intro');
        self::assertTrue($html->contains($section));
        self::assertFalse($html->contains(HtmlTag::new('aside')));
    }

    public function testContainsEmpty(): void
    {
        $value = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $value->contains('');
    }

    public function testContainsInvalidRegex(): void
    {
        $value = XString::new('sample');
        $this->expectException(ValueError::class);
        $value->contains(Regex::new('/[unclosed/'));
    }

}
