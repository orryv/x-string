<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

final class MatchAllTest extends TestCase
{
    public function testMatchAllDigits(): void
    {
        $log = XString::new('IDs: 135, 246, 579.');
        $matches = $log->matchAll(Regex::new('/\d{3}/'));
        self::assertSame(['135', '246', '579'], $matches[0]);
    }

    public function testMatchAllLimit(): void
    {
        $sequence = XString::new('1, 2, 3, 4, 5, 6');
        $matches = $sequence->matchAll(Regex::new('/\d/'), limit: 3);
        self::assertSame(['1', '2', '3'], $matches[0]);
    }

    public function testMatchAllMultiplePatterns(): void
    {
        $report = XString::new('Sprint #42 finished with 17 tasks done.');
        $matches = $report->matchAll([
            Regex::new('/(?P<number>\d+)/'),
            Regex::new('/(?P<tag>#\d+)/'),
        ]);
        self::assertSame(['42', '17'], $matches['number']);
        self::assertSame(['#42'], $matches['tag']);
    }

    public function testMatchAllSetOrder(): void
    {
        $line = XString::new('age=39, score=98');
        $matches = $line->matchAll(
            Regex::new('/(?P<key>\w+)=(?P<value>\d+)/'),
            flags: PREG_SET_ORDER
        );
        self::assertCount(2, $matches);
        self::assertSame('age', $matches[0]['key']);
        self::assertSame('39', $matches[0]['value']);
        self::assertSame('score', $matches[1]['key']);
        self::assertSame('98', $matches[1]['value']);
    }

    public function testMatchAllFlagsArray(): void
    {
        $text = XString::new('Line 1\nLine 2');
        $matches = $text->matchAll(
            Regex::new('/Line (?P<index>\d)/'),
            flags: [PREG_SET_ORDER, PREG_OFFSET_CAPTURE]
        );
        self::assertSame('1', $matches[0]['index'][0]);
        self::assertSame(['Line 2', 8], $matches[1][0]);
    }

    public function testMatchAllLimitZero(): void
    {
        $value = XString::new('numbers: 10 20 30');
        $matches = $value->matchAll(Regex::new('/\d+/'), limit: 0);
        self::assertSame([], $matches);
    }

    public function testMatchAllEmptyPatterns(): void
    {
        $value = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $value->matchAll([]);
    }

    public function testMatchAllNonRegex(): void
    {
        $value = XString::new('content');
        $patterns = [Regex::new('/foo/'), 'bar'];
        $this->expectException(InvalidArgumentException::class);
        $value->matchAll($patterns);
    }

    public function testMatchAllInvalidPattern(): void
    {
        $value = XString::new('content');
        $this->expectException(ValueError::class);
        $value->matchAll(Regex::new('/(unclosed/'));
    }

    public function testMatchAllImmutability(): void
    {
        $value = XString::new('Order #55 processed');
        $value->matchAll(Regex::new('/\d+/'));
        self::assertSame('Order #55 processed', (string) $value);
    }

}
