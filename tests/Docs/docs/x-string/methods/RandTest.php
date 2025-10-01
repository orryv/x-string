<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

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

}
