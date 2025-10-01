<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class ToSnakeTest extends TestCase
{
    public function testTosnakeBasic(): void
    {
        $title = XString::new('Hello World Example');
        $result = $title->toSnake();
        self::assertSame('hello_world_example', (string) $result);
        self::assertSame('Hello World Example', (string) $title);
    }

    public function testTosnakeHyphen(): void
    {
        $slug = XString::new('already-separated-value');
        $result = $slug->toSnake('-');
        self::assertSame('already_separated_value', (string) $result);
    }

    public function testTosnakeMultipleDelimiters(): void
    {
        $value = XString::new('this.is-aString');
        $result = $value->toSnake(['.', '-']);
        self::assertSame('this_is_a_string', (string) $result);
    }

    public function testTosnakePascal(): void
    {
        $class = XString::new('HTTPRequestHandler');
        $result = $class->toSnake();
        self::assertSame('http_request_handler', (string) $result);
    }

    public function testTosnakeDigits(): void
    {
        $version = XString::new('Version2Update');
        $result = $version->toSnake();
        self::assertSame('version_2_update', (string) $result);
    }

    public function testTosnakeExisting(): void
    {
        $value = XString::new('Already_Snake');
        $result = $value->toSnake('_');
        self::assertSame('already_snake', (string) $result);
    }

    public function testTosnakeEmpty(): void
    {
        $empty = XString::new('');
        $result = $empty->toSnake();
        self::assertSame('', (string) $result);
    }

    public function testTosnakeImmutability(): void
    {
        $value = XString::new('MutableValue');
        $snake = $value->toSnake();
        self::assertSame('MutableValue', (string) $value);
        self::assertSame('mutable_value', (string) $snake);
    }

    public function testTosnakeInvalidDelimiter(): void
    {
        $value = XString::new('Example');
        $this->expectException(InvalidArgumentException::class);
        $value->toSnake('');
    }

}
