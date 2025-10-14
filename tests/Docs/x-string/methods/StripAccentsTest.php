<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class StripAccentsTest extends TestCase
{
    public function testStripAccentsBasic(): void
    {
        $value = XString::new('Café crème brûlée');
        $result = $value->stripAccents();
        self::assertSame('Cafe creme brulee', (string) $result);
    }

    public function testStripAccentsLigatures(): void
    {
        $value = XString::new('Ångström & Straße — façade');
        $result = $value->stripAccents();
        self::assertSame('Angstrom & Strasse — facade', (string) $result);
    }

    public function testStripAccentsCombining(): void
    {
        $value = XString::new("Cafe\u{0301} mañana");
        $result = $value->stripAccents();
        self::assertSame('Cafe manana', (string) $result);
    }

    public function testStripAccentsNonLatin(): void
    {
        $value = XString::new('中文 日本語 한국어');
        $result = $value->stripAccents();
        self::assertSame('中文 日本語 한국어', (string) $result);
    }

    public function testStripAccentsImmutable(): void
    {
        $original = XString::new('Señorita');
        $processed = $original->stripAccents();
        self::assertSame('Senorita', (string) $processed);
        self::assertSame('Señorita', (string) $original);
    }

    public function testStripAccentsEmpty(): void
    {
        $result = XString::new('')->stripAccents();
        self::assertSame('', (string) $result);
    }

}
