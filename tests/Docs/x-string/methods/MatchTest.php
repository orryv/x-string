<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

final class MatchTest extends TestCase
{
    public function testMatchBasic(): void
    {
        $message = XString::new('Tickets #4321 resolved, #99 reopened');
        $matches = $message->match(Regex::new('/#(?P<id>\d+)/'));
        self::assertCount(2, $matches);
        self::assertSame('#4321', $matches[0][0]);
        self::assertSame('4321', $matches[0]['id']);
        self::assertSame('#99', $matches[1][0]);
        self::assertSame('99', $matches[1]['id']);
    }

    public function testMatchNoResult(): void
    {
        $result = XString::new('No numbers here')->match(Regex::new('/\d+/'));
        self::assertNull($result);
    }

    public function testMatchMultiplePatterns(): void
    {
        $value = XString::new('v2.5.0-beta.3');
        $patterns = [
            Regex::new('/^v(?P<major>\d+)/'),
            Regex::new('/\.(?P<section>\d+)/'),
        ];
        $matches = $value->match($patterns);
        self::assertCount(4, $matches);
        self::assertSame('2', $matches[0]['major']);
        self::assertSame('.5', $matches[1][0]);
        self::assertSame('5', $matches[1]['section']);
        self::assertSame('.0', $matches[2][0]);
        self::assertSame('.3', $matches[3][0]);
        self::assertSame('3', $matches[3]['section']);
    }

    public function testMatchEmptyArray(): void
    {
        $value = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $value->match([]);
    }

    public function testMatchInvalidEntry(): void
    {
        $value = XString::new('content');
        $patterns = [Regex::new('/foo/'), 'bar'];
        $this->expectException(InvalidArgumentException::class);
        $value->match($patterns);
    }

    public function testMatchInvalidRegex(): void
    {
        $value = XString::new('content');
        $this->expectException(ValueError::class);
        $value->match(Regex::new('/(unclosed/'));
    }

    public function testMatchImmutable(): void
    {
        $value = XString::new('Order #77 processed');
        $value->match(Regex::new('/#(\d+)/'));
        self::assertSame('Order #77 processed', (string) $value);
    }

}
