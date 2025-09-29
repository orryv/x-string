<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToUpperTest extends TestCase
{
    public function testToUpperBasic(): void
    {
        $xstring = XString::new('hello world');
        $upper = $xstring->toUpper();
        self::assertSame('HELLO WORLD', (string) $upper);
    }

    public function testToUpperMultibyte(): void
    {
        $xstring = XString::new('Résumé');
        $upper = $xstring->toUpper();
        self::assertSame('RÉSUMÉ', (string) $upper);
    }

    public function testToUpperAlias(): void
    {
        $xstring = XString::new('alias');
        self::assertSame((string) $xstring->toUpper(), (string) $xstring->toUpperCase());
    }

    public function testToUpperImmutability(): void
    {
        $xstring = XString::new('keep me');
        $upper = $xstring->toUpper();
        self::assertSame('keep me', (string) $xstring);
        self::assertSame('KEEP ME', (string) $upper);
    }

    public function testToUpperEmpty(): void
    {
        $xstring = XString::new('');
        $upper = $xstring->toUpper();
        self::assertSame('', (string) $upper);
    }

}
