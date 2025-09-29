<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use stdClass;

final class PrependTest extends TestCase
{
    public function testPrependBasic(): void
    {
        $original = XString::new('world');
        $updated = $original->prepend('hello ');
        self::assertSame('hello world', (string) $updated);
    }

    public function testPrependArray(): void
    {
        $original = XString::new('body');
        $updated = $original->prepend(['<', 'div', '>']);
        self::assertSame('<div>body', (string) $updated);
    }

    public function testPrependStringables(): void
    {
        $original = XString::new('Content');
        $updated = $original->prepend([
            Regex::new('/^Title:/'),
            Newline::new(),
        ]);
        self::assertSame("/^Title:/\nContent", (string) $updated);
    }

    public function testPrependImmutability(): void
    {
        $original = XString::new('core');
        $updated = $original->prepend('pre-');
        self::assertSame('core', (string) $original);
        self::assertSame('pre-core', (string) $updated);
    }

    public function testPrependInvalid(): void
    {
        $original = XString::new('foo');
        $this->expectException(InvalidArgumentException::class);
        $original->prepend([new stdClass()]);
    }

}
