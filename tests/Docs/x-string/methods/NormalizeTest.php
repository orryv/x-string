<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Normalizer;
use Orryv\XString;

final class NormalizeTest extends TestCase
{
    public function testNormalizeNfc(): void
    {
        $xstring = XString::new("Cafe\u{0301}");
        $result = $xstring->normalize();
        self::assertSame('Café', (string) $result);
        self::assertTrue(Normalizer::isNormalized((string) $result, Normalizer::FORM_C));
    }

    public function testNormalizeNfd(): void
    {
        $xstring = XString::new('Ångström');
        $result = $xstring->normalize(Normalizer::FORM_D);
        self::assertSame("A\u{030A}ngstro\u{0308}m", (string) $result);
        self::assertTrue(Normalizer::isNormalized((string) $result, Normalizer::FORM_D));
    }

    public function testNormalizeNfkc(): void
    {
        $xstring = XString::new("Å");
        $result = $xstring->normalize(Normalizer::FORM_KC);
        self::assertSame('Å', (string) $result);
    }

    public function testNormalizeImmutability(): void
    {
        $xstring = XString::new("e\u{0301}");
        $normalized = $xstring->normalize();
        self::assertSame("e\u{0301}", (string) $xstring);
        self::assertSame('é', (string) $normalized);
    }

    public function testNormalizeInvalid(): void
    {
        $xstring = XString::new('data');
        $this->expectException(InvalidArgumentException::class);
        $xstring->normalize(-1);
    }

}
