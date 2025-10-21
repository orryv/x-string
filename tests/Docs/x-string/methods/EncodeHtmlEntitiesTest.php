<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class EncodeHtmlEntitiesTest extends TestCase
{
    public function testEncodeHtmlEntitiesBasic(): void
    {
        $value = XString::new('<span>Me & You</span>');
        $result = $value->encodeHtmlEntities();
        self::assertSame('&lt;span&gt;Me &amp; You&lt;/span&gt;', (string) $result);
    }

    public function testEncodeHtmlEntitiesDoubleEncode(): void
    {
        $value = XString::new('Already &amp; escaped');
        $result = $value->encodeHtmlEntities();
        self::assertSame('Already &amp; escaped', (string) $result);
    }

    public function testEncodeHtmlEntitiesForceDouble(): void
    {
        $value = XString::new('Already &amp; escaped');
        $result = $value->encodeHtmlEntities(ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, null, true);
        self::assertSame('Already &amp;amp; escaped', (string) $result);
    }

    public function testEncodeHtmlEntitiesFlags(): void
    {
        $value = XString::new("Café");
        $result = $value->encodeHtmlEntities(ENT_NOQUOTES | ENT_SUBSTITUTE, 'ISO-8859-1');
        self::assertSame('Caf&eacute;', (string) $result);
    }

    public function testEncodeHtmlEntitiesImmutability(): void
    {
        $value = XString::new('Rock & Roll');
        $value->encodeHtmlEntities();
        self::assertSame('Rock & Roll', (string) $value);
    }

}
