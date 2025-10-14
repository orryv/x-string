<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XStringType\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XStringType;
use Orryv\XString\Regex;
use ValueError;

final class RegexTest extends TestCase
{
    public function testXstringTypeRegexMatch(): void
    {
        $pattern = XStringType::regex('/\d+/');
        $result = XString::new('Invoice-2048')->match($pattern);
        self::assertInstanceOf(Regex::class, $pattern);
        self::assertSame('2048', (string) $result);
    }

    public function testXstringTypeRegexReuse(): void
    {
        $pattern = XStringType::regex('/ETA:\s*(\d{2}:\d{2})/');
        $schedule = XString::new('ETA: 19:45');
        $announcement = $schedule->replace($pattern, 'Arrives at $1');
        $message = XString::new('Boarding ETA: 19:45');
        self::assertSame('Arrives at 19:45', (string) $announcement);
        self::assertTrue($message->contains($pattern));
        self::assertSame('ETA: 19:45', (string) $schedule);
    }

    public function testXstringTypeRegexImmutable(): void
    {
        $first = XStringType::regex('/foo/');
        $second = XStringType::regex('/foo/');
        self::assertSame((string) $first, (string) $second);
        self::assertNotSame($first, $second);
    }

    public function testXstringTypeRegexInvalid(): void
    {
        $string = XString::new('abc');
        $this->expectException(ValueError::class);
        $string->match(XStringType::regex('/(?P<unbalanced/'));
    }

}
