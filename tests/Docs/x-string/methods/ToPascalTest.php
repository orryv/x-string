<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class ToPascalTest extends TestCase
{
    public function testTopascalBasic(): void
    {
        $result = XString::new('make interface great again')->toPascal();
        self::assertSame('MakeInterfaceGreatAgain', (string) $result);
    }

    public function testTopascalMixed(): void
    {
        $result = XString::new('api-response_builder')->toPascal();
        self::assertSame('ApiResponseBuilder', (string) $result);
    }

    public function testTopascalExisting(): void
    {
        $value = XString::new('AlreadyPascalCase');
        $result = $value->toPascal();
        self::assertSame('AlreadyPascalCase', (string) $result);
    }

    public function testTopascalUnicode(): void
    {
        $result = XString::new('élève du soir')->toPascal();
        self::assertSame('ÉlèveDuSoir', (string) $result);
    }

    public function testTopascalByteMode(): void
    {
        $value = XString::new('ångström growth')->withMode('bytes');
        $result = $value->toPascal();
        self::assertSame('ÅngströmGrowth', (string) $result);
        self::assertSame(16, $result->length());
    }

    public function testTopascalEmpty(): void
    {
        $result = XString::new('')->toPascal();
        self::assertSame('', (string) $result);
    }

    public function testTopascalImmutable(): void
    {
        $original = XString::new('mutable string');
        $pascal = $original->toPascal();
        self::assertSame('mutable string', (string) $original);
        self::assertSame('MutableString', (string) $pascal);
    }

}
