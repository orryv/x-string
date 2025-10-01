<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class MaskTest extends TestCase
{
    public function testMaskPhoneNumber(): void
    {
        $number = XString::new('5558675309');
        $result = $number->mask('###-###-####');
        self::assertSame('555-867-5309', (string) $result);
    }

    public function testMaskCustomPlaceholder(): void
    {
        $number = XString::new('123456789');
        $result = $number->mask('(***) ***-****', '*');
        self::assertSame('(123) 456-789', (string) $result);
    }

    public function testMaskShortInput(): void
    {
        $id = XString::new('42');
        $result = $id->mask('ID-###-##');
        self::assertSame('ID-42', (string) $result);
    }

    public function testMaskByteMode(): void
    {
        $binary = XString::new("A\x00B\x01")->withMode('bytes');
        $result = $binary->mask('0x## ##');
        self::assertSame('30784100204201', bin2hex((string) $result));
        self::assertSame("A\x00B\x01", (string) $binary);
    }

    public function testMaskReversed(): void
    {
        $original = XString::new('123456789');
        $masked = $original->mask('*****####', reversed: true);
        self::assertSame('*****6789', (string) $masked);
        self::assertSame('123456789', (string) $original);
    }

    public function testMaskImmutability(): void
    {
        $source = XString::new('🙂🙃');
        $masked = $source->mask('##-##');
        self::assertSame('🙂🙃', (string) $source);
        self::assertSame('🙂🙃-', (string) $masked);
    }

    public function testMaskInvalidPlaceholder(): void
    {
        $source = XString::new('1234');
        $this->expectException(InvalidArgumentException::class);
        $source->mask('##-##', '');
    }

}
