<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class DecodeHtmlEntitiesTest extends TestCase
{
    public function testDecodeHtmlEntitiesBasic(): void
    {
        $value = XString::new('&lt;strong&gt;Bold &amp; clear&lt;/strong&gt;');
        $result = $value->decodeHtmlEntities();
        self::assertSame('<strong>Bold & clear</strong>', (string) $result);
    }

    public function testDecodeHtmlEntitiesFlags(): void
    {
        $value = XString::new('&lt;quotes&gt;&amp;&apos;');
        $result = $value->decodeHtmlEntities(ENT_NOQUOTES | ENT_HTML5);
        self::assertSame('<quotes>&&apos;', (string) $result);
    }

    public function testDecodeHtmlEntitiesEncoding(): void
    {
        $value = XString::new('Espa&ntilde;a');
        $result = $value->decodeHtmlEntities(ENT_QUOTES | ENT_HTML401, 'ISO-8859-1');
        self::assertSame('España', (string) $result);
    }

    public function testDecodeHtmlEntitiesImmutability(): void
    {
        $value = XString::new('&amp;copy; 2024');
        $value->decodeHtmlEntities();
        self::assertSame('&amp;copy; 2024', (string) $value);
    }

}
