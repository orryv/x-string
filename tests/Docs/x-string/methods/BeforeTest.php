<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

final class BeforeTest extends TestCase
{
    public function testBeforeEmail(): void
    {
        $email = XString::new('user@example.com');
        $result = $email->before('@');
        self::assertSame('user', (string) $result);
        self::assertSame('user@example.com', (string) $email);
    }

    public function testBeforeSkip(): void
    {
        $path = XString::new('one/two/three/four');
        $result = $path->before('/', skip: 2);
        self::assertSame('one/two/three', (string) $result);
    }

    public function testBeforeReversed(): void
    {
        $path = XString::new('path/to/file.txt');
        $result = $path->before('/', last_occurence: true);
        self::assertSame('path/to', (string) $result);
    }

    public function testBeforeMissing(): void
    {
        $text = XString::new('no delimiter');
        $result = $text->before('|');
        self::assertSame('no delimiter', (string) $result);
    }

    public function testBeforeImmutability(): void
    {
        $value = XString::new('abc-def');
        $before = $value->before('-');
        self::assertSame('abc-def', (string) $value);
        self::assertSame('abc', (string) $before);
    }

    public function testBeforeMixedSequential(): void
    {
        $document = XString::new("Intro\n<header>\nTitle: Report</header>");
        $result = $document->before([HtmlTag::new('header'), Newline::new("\n"), 'Title: ']);
        self::assertSame("Intro\n", (string) $result);
    }

    public function testBeforeOrBehavior(): void
    {
        $value = XString::new("Prefix {data} <id>42</id>\nResult: done");
        $sequential = $value->before([Newline::new("\n"), 'Result: ']);
        $either = $value->before([
            HtmlTag::new('id'),
            Regex::new('{'),
            [Newline::new("\n"), 'Result: '],
        ], start_behavior: 'or');
        self::assertStringContainsString('<id>42</id', (string) $sequential);
        self::assertSame('Prefix ', (string) $either);
    }

    public function testBeforeInvalidSkip(): void
    {
        $value = XString::new('example');
        $this->expectException(InvalidArgumentException::class);
        $value->before('e', skip: -1);
    }

}
