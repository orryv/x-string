<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToCamelTest extends TestCase
{
    public function testTocamelBasic(): void
    {
        $phrase = XString::new('hello world example');
        $result = $phrase->toCamel();
        self::assertSame('helloWorldExample', (string) $result);
        self::assertSame('hello world example', (string) $phrase);
    }

    public function testTocamelMixed(): void
    {
        $value = XString::new('make-HTTP_response 42');
        $result = $value->toCamel();
        self::assertSame('makeHttpResponse42', (string) $result);
    }

    public function testTocamelPascalFlag(): void
    {
        $result = XString::new('customer account')->toCamel(true);
        self::assertSame('CustomerAccount', (string) $result);
    }

    public function testTocamelExisting(): void
    {
        $value = XString::new('alreadyCamelCase');
        $result = $value->toCamel();
        self::assertSame('alreadyCamelCase', (string) $result);
    }

    public function testTocamelUnicode(): void
    {
        $result = XString::new('Olá Mundo')->toCamel();
        self::assertSame('oláMundo', (string) $result);
    }

    public function testTocamelByteMode(): void
    {
        $value = XString::new('Ångström Growth')->withMode('bytes');
        $result = $value->toCamel();
        self::assertSame('ångströmGrowth', (string) $result);
        self::assertSame(16, $result->length());
    }

    public function testTocamelEmpty(): void
    {
        $empty = XString::new('');
        $result = $empty->toCamel();
        self::assertSame('', (string) $result);
    }

    public function testTocamelImmutable(): void
    {
        $original = XString::new('Mutable Value');
        $camel = $original->toCamel();
        self::assertSame('Mutable Value', (string) $original);
        self::assertSame('mutableValue', (string) $camel);
    }

}
