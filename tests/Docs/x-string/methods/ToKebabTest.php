<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToKebabTest extends TestCase
{
    public function testTokebabBasic(): void
    {
        $title = XString::new('Hello World Example');
        $result = $title->toKebab();
        self::assertSame('hello-world-example', (string) $result);
        self::assertSame('Hello World Example', (string) $title);
    }

    public function testTokebabCamel(): void
    {
        $value = XString::new('XMLHttpRequestParser');
        $result = $value->toKebab();
        self::assertSame('xml-http-request-parser', (string) $result);
    }

    public function testTokebabMixedSeparators(): void
    {
        $value = XString::new('double--dash__value');
        $result = $value->toKebab();
        self::assertSame('double-dash-value', (string) $result);
    }

    public function testTokebabByteMode(): void
    {
        $value = XString::new('Ångström Growth')->withMode('bytes');
        $result = $value->toKebab();
        self::assertSame('ångström-growth', (string) $result);
        self::assertSame(17, $result->length());
    }

    public function testTokebabGraphemeMode(): void
    {
        $value = XString::new('🙂 Smile')->withMode('graphemes');
        $result = $value->toKebab();
        self::assertSame('🙂-smile', (string) $result);
        self::assertSame(7, $result->length());
    }

    public function testTokebabEmpty(): void
    {
        $empty = XString::new('');
        $result = $empty->toKebab();
        self::assertSame('', (string) $result);
    }

    public function testTokebabImmutable(): void
    {
        $original = XString::new('Mutable Value');
        $converted = $original->toKebab();
        self::assertSame('Mutable Value', (string) $original);
        self::assertSame('mutable-value', (string) $converted);
    }

}
