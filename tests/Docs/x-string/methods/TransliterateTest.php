<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class TransliterateTest extends TestCase
{
    public function testTransliterateDefault(): void
    {
        $title = XString::new('façade déjà vu');
        $result = $title->transliterate();
        self::assertSame('facade deja vu', (string) $result);
        self::assertNotSame($title, $result);
    }

    public function testTransliterateIgnore(): void
    {
        $value = XString::new('Smörgåsbord 🍣');
        $result = $value->transliterate('ASCII//TRANSLIT//IGNORE');
        self::assertSame('Smorgasbord ?', (string) $result);
    }

    public function testTransliterateIso(): void
    {
        $value = XString::new('Zażółć gęślą jaźń');
        $result = $value->transliterate('ISO-8859-1//TRANSLIT');
        $utf8View = iconv('ISO-8859-1', 'UTF-8', (string) $result);
        self::assertSame('Zazólc gesla jazn', $utf8View);
    }

    public function testTransliterateInvalidId(): void
    {
        $value = XString::new('пример');
        $this->expectException(InvalidArgumentException::class);
        $value->transliterate('Unknown-ID');
    }

    public function testTransliterateInvalidEncoding(): void
    {
        $value = XString::new('text');
        $this->expectException(InvalidArgumentException::class);
        $value->transliterate('INVALID-ENCODING');
    }

    public function testTransliterateImmutability(): void
    {
        $value = XString::new('über cool');
        $value->transliterate();
        self::assertSame('über cool', (string) $value);
    }

}
