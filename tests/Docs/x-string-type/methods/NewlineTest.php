<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XStringType\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;
use Orryv\XStringType;
use Orryv\XString\Newline;

final class NewlineTest extends TestCase
{
    public function testXstringTypeNewlineDefault(): void
    {
        $newline = XStringType::newline();
        self::assertInstanceOf(Newline::class, $newline);
        self::assertSame(PHP_EOL, (string) $newline);
    }

    public function testXstringTypeNewlineCustom(): void
    {
        $windows = XStringType::newline("\r\n");
        self::assertSame("\r\n", (string) $windows);
    }

    public function testXstringTypeNewlineConstraints(): void
    {
        $newline = XStringType::newline()->startsWith('  Line1', trim: true);
        $config = $newline->getLineConstraint();
        self::assertSame(['type' => 'starts_with', 'needle' => 'Line1', 'trim' => true], $config);
    }

    public function testXstringTypeNewlineCombine(): void
    {
        $value = XString::new([
            'Header',
            XStringType::newline(),
            'Body',
        ]);
        self::assertSame('Header' . PHP_EOL . 'Body', (string) $value);
    }

}
