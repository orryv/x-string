<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use stdClass;

final class AppendTest extends TestCase
{
    public function testAppendBasic(): void
    {
        $original = XString::new('Hello');
        $updated = $original->append(', World');
        self::assertSame('Hello, World', (string) $updated);
    }

    public function testAppendArray(): void
    {
        $original = XString::new('foo');
        $updated = $original->append(['-', 'bar', '-', 'baz']);
        self::assertSame('foo-bar-baz', (string) $updated);
    }

    public function testAppendStringables(): void
    {
        $original = XString::new('Pattern');
        $updated = $original->append([
            Newline::new(),
            HtmlTag::new('span')->withClass('highlight'),
            Regex::new('/[a-z]+/i'),
        ]);
        self::assertSame('Pattern' . PHP_EOL . '<span class="highlight">/[a-z]+/i', (string) $updated);
    }

    public function testAppendImmutability(): void
    {
        $original = XString::new('start');
        $updated = $original->append(' end');
        self::assertSame('start', (string) $original);
        self::assertSame('start end', (string) $updated);
    }

    public function testAppendInvalid(): void
    {
        $original = XString::new('foo');
        $this->expectException(InvalidArgumentException::class);
        $original->append([new stdClass()]);
    }

}
