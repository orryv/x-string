<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class CharAtTest extends TestCase
{
    public function testCharAtGrapheme(): void
    {
        $value = XString::new('résumé');
        self::assertSame('é', $value->charAt(1));
        self::assertSame('m', $value->charAt(4));
        self::assertSame('résumé', (string) $value);
    }

    public function testCharAtNegative(): void
    {
        $text = XString::new('Unicode');
        self::assertSame('e', $text->charAt(-1));
        self::assertSame('U', $text->charAt(-7));
    }

    public function testCharAtBytes(): void
    {
        $bytes = XString::new('Café')->asBytes();
        self::assertSame('f', $bytes->charAt(2));
        self::assertSame('c3', bin2hex($bytes->charAt(3)));
        self::assertSame('a9', bin2hex($bytes->charAt(4)));
    }

    public function testCharAtEmoji(): void
    {
        $emoji = XString::new('👨‍👩‍👧‍👦');
        self::assertSame('👨‍👩‍👧‍👦', $emoji->charAt(0));
        self::assertSame("\u{200D}", $emoji->asCodepoints()->charAt(1));
    }

    public function testCharAtException(): void
    {
        $empty = XString::new('');
        $this->expectException(InvalidArgumentException::class);
        $empty->charAt(0);
    }

}
