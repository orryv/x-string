<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\Regex\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Regex;
use ValueError;

final class NewTest extends TestCase
{
    public function testRegexNewBasic(): void
    {
        $pattern = Regex::new('/^user-\d+$/');
        self::assertSame('/^user-\d+$/', (string) $pattern);
    }

    public function testRegexNewMatch(): void
    {
        $response = XString::new('Order #2048 confirmed');
        $match = $response->match(Regex::new('/\d+/'));
        self::assertSame('2048', (string) $match);
    }

    public function testRegexNewReplace(): void
    {
        $template = XString::new('Invoice-12345.pdf');
        $result = $template->replace(Regex::new('/-\d+/'), '-{id}');
        self::assertSame('Invoice-{id}.pdf', (string) $result);
    }

    public function testRegexNewImmutability(): void
    {
        $original = Regex::new('/foo/');
        $another = Regex::new('/foo/');
        self::assertSame((string) $original, (string) $another);
        self::assertNotSame($original, $another);
    }

    public function testRegexNewInvalid(): void
    {
        $string = XString::new('abc');
        $this->expectException(ValueError::class);
        $string->match(Regex::new('/(?P<unbalanced/'));
    }

}
