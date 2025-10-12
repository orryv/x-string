<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToTitleTest extends TestCase
{
    public function testTotitleBasic(): void
    {
        $value = XString::new('once upon a time');
        $result = $value->toTitle();
        self::assertSame('Once Upon A Time', (string) $result);
        self::assertSame('once upon a time', (string) $value);
    }

    public function testTotitlePunctuation(): void
    {
        $result = XString::new('well-known co-founder')->toTitle();
        self::assertSame('Well-Known Co-Founder', (string) $result);
    }

    public function testTotitleSpacing(): void
    {
        $result = XString::new("multiple\tspaces\nallowed")->toTitle();
        self::assertSame("Multiple\tSpaces\nAllowed", (string) $result);
    }

    public function testTotitleAccents(): void
    {
        $result = XString::new("l'été à l'ombre")->toTitle();
        self::assertSame("L'Été À L'Ombre", (string) $result);
    }

    public function testTotitleGraphemeMode(): void
    {
        $value = XString::new('ångor ångström')->withMode('graphemes');
        $result = $value->toTitle();
        self::assertSame('Ångor Ångström', (string) $result);
        self::assertSame(14, $result->length());
    }

    public function testTotitleEmpty(): void
    {
        $result = XString::new('')->toTitle();
        self::assertSame('', (string) $result);
    }

    public function testTotitleImmutable(): void
    {
        $original = XString::new('mutable string');
        $title = $original->toTitle();
        self::assertSame('mutable string', (string) $original);
        self::assertSame('Mutable String', (string) $title);
    }

}
