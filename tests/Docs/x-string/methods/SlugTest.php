<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class SlugTest extends TestCase
{
    public function testSlugBasic(): void
    {
        $value = XString::new('Hello, World!');
        $slug = $value->slug();
        self::assertSame('hello-world', (string) $slug);
    }

    public function testSlugPunctuation(): void
    {
        $value = XString::new(' Rock   &   Roll!!! ');
        $slug = $value->slug();
        self::assertSame('rock-roll', (string) $slug);
    }

    public function testSlugAccented(): void
    {
        $value = XString::new('Crème brûlée à la carte');
        $slug = $value->slug();
        self::assertSame('creme-brulee-a-la-carte', (string) $slug);
    }

    public function testSlugCustomSeparator(): void
    {
        $value = XString::new('foo bar baz');
        $slug = $value->slug('_');
        self::assertSame('foo_bar_baz', (string) $slug);
    }

    public function testSlugMultiSeparator(): void
    {
        $value = XString::new('Ready... Set... Go!');
        $slug = $value->slug('--');
        self::assertSame('ready--set--go', (string) $slug);
    }

    public function testSlugEmptyInput(): void
    {
        $value = XString::new('');
        $slug = $value->slug();
        self::assertSame('', (string) $slug);
    }

    public function testSlugEmptySeparator(): void
    {
        $value = XString::new('will fail');
        $this->expectException(InvalidArgumentException::class);
        $value->slug('');
    }

    public function testSlugImmutable(): void
    {
        $value = XString::new('Mutable? No!');
        $slug = $value->slug();
        self::assertSame('Mutable? No!', (string) $value);
        self::assertSame('mutable-no', (string) $slug);
    }

}
