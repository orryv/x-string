<?php

declare(strict_types=1);

namespace Orryv\XString\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Orryv\XString;

final class UuidTest extends TestCase
{
    public function testUuidV4(): void
    {
        $uuid = XString::uuid();
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', (string) $uuid);
    }

    public function testUuidV1(): void
    {
        $uuid = XString::uuid(1);
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', (string) $uuid);
        self::assertNotSame(XString::uuid(1)->__toString(), (string) $uuid);
    }

    public function testUuidV3Deterministic(): void
    {
        $namespace = '6ba7b810-9dad-11d1-80b4-00c04fd430c8'; // DNS namespace
        $first = XString::uuid(3, $namespace, 'example.com');
        $second = XString::uuid(3, $namespace, 'example.com');
        self::assertSame('9073926b-929f-31c2-abc9-fad77ae3e8eb', (string) $first);
        self::assertSame((string) $first, (string) $second);
    }

    public function testUuidV5Deterministic(): void
    {
        $namespace = '6ba7b810-9dad-11d1-80b4-00c04fd430c8'; // DNS namespace
        $uuid = XString::uuid(5, $namespace, 'example.com');
        self::assertSame('cfbff0d1-9375-5685-968c-48ce8b15ae17', (string) $uuid);
    }

    public function testUuidMissingNamespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        XString::uuid(3, null, 'name');
    }

    public function testUuidInvalidNamespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        XString::uuid(5, 'not-a-uuid', 'name');
    }

    public function testUuidInvalidVersion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        XString::uuid(2);
    }

}
