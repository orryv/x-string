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
    public function testMatchFirstTicket(): void
    {
        $message = XString::new('Tickets #4321 resolved, #99 reopened');
        $match = $message->match(Regex::new('/#\d+/'));
        self::assertInstanceOf(XString::class, $match);
        self::assertSame('#4321', (string) $match);
    }

    public function testMatchOffset(): void
    {
        $value = XString::new('ID: #12, ID: #45');
        $match = $value->match(Regex::new('/#\d+/'), offset: 7);
        self::assertSame('#45', (string) $match);
    }

    public function testMatchLowestPosition(): void
    {
        $value = XString::new('Release v2.5.0-beta');
        $patterns = [
            Regex::new('/beta/'),
            Regex::new('/v\d+/'),
            Regex::new('/\.\d/'),
        ];
        $match = $value->match($patterns);
        self::assertSame('v2', (string) $match);
    }

    public function testMatchNoResult(): void
    {
        $result = XString::new('No numbers here')->match(Regex::new('/\d+/'));
        self::assertNull($result);
    }

    public function testMatchNegativeOffset(): void
    {
        $value = XString::new('content');
        $this->expectException(InvalidArgumentException::class);
        $value->match(Regex::new('/./'), offset: -1);
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
        $match = $value->match(Regex::new('/#(\d+)/'));
        self::assertSame('Order #77 processed', (string) $value);
        self::assertNotSame($value, $match);
        self::assertSame('#77', (string) $match);
    }

}
