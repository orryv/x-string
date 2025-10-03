<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XString\Exceptions\EmptyCharacterSetException;
use Orryv\XString\Exceptions\InvalidLengthException;

final class RandTest extends TestCase
{
    public function testRandAbcdef(): void
    {
        $x = XString::rand(10, 'abcdef');
        self::assertEquals(10, $x->length());
        self::assertMatchesRegularExpression('/^[abcdef]{10}$/', (string) $x);
    }

    public function testRandDefault(): void
    {
        $x = XString::rand(15);
        self::assertEquals(15, $x->length());
        self::assertMatchesRegularExpression('/^[0-9a-zA-Z]{15}$/', (string) $x);
    }

    public function testRandUnicode(): void
    {
        $x = XString::rand(4, '🍎🍇🍉');
        self::assertSame(4, $x->length());
        self::assertMatchesRegularExpression('/^[🍎🍇🍉]{4}$/u', (string) $x);
    }

    public function testRandInvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        XString::rand(0);
    }

    public function testRandEmptyCharacters(): void
    {
        $this->expectException(EmptyCharacterSetException::class);
        XString::rand(5, '');
    }

    public function testRandLengthAcrossModes(): void
    {
        $random = XString::rand(6, 'abcdef');
        $bytes = $random->withMode('bytes');
        $roundTrip = $bytes->withMode('graphemes');
        self::assertSame(6, $random->length());
        self::assertSame(6, $bytes->length());
        self::assertSame((string) $random, (string) $roundTrip);
    }

}
