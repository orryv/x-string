<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;

final class EnsurePrefixTest extends TestCase
{
    public function testEnsurePrefixAdd(): void
    {
        $value = XString::new('example.com');
        $result = $value->ensurePrefix('https://');
        self::assertSame('https://example.com', (string) $result);
    }

    public function testEnsurePrefixExisting(): void
    {
        $value = XString::new('https://example.com');
        $result = $value->ensurePrefix('https://');
        self::assertSame('https://example.com', (string) $result);
    }

    public function testEnsurePrefixHtmltag(): void
    {
        $value = XString::new('important');
        $result = $value->ensurePrefix(HtmlTag::new('strong'));
        self::assertSame('<strong>important', (string) $result);
    }

    public function testEnsurePrefixNewline(): void
    {
        $value = XString::new('Subject');
        $result = $value->ensurePrefix(Newline::new("\r\n"));
        self::assertSame("\r\nSubject", (string) $result);
    }

    public function testEnsurePrefixEmpty(): void
    {
        $value = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $value->ensurePrefix('');
    }

    public function testEnsurePrefixImmutable(): void
    {
        $original = XString::new('value');
        $original->ensurePrefix('> ');
        self::assertSame('value', (string) $original);
    }

}
