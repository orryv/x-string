<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class Br2nlTest extends TestCase
{
    public function testBr2nlBasic(): void
    {
        $result = XString::new('Line 1<br>Line 2')->br2nl();
        self::assertSame('Line 1' . PHP_EOL . 'Line 2', (string) $result);
    }

    public function testBr2nlVariations(): void
    {
        $result = XString::new('One<br />Two<BR/>Three')->br2nl();
        self::assertSame('One' . PHP_EOL . 'Two' . PHP_EOL . 'Three', (string) $result);
    }

    public function testBr2nlMultiple(): void
    {
        $result = XString::new('A<br><br>B')->br2nl();
        self::assertSame('A' . PHP_EOL . PHP_EOL . 'B', (string) $result);
    }

    public function testBr2nlTrailing(): void
    {
        $result = XString::new('End<br>')->br2nl();
        self::assertSame('End' . PHP_EOL, (string) $result);
    }

    public function testBr2nlMode(): void
    {
        $value = XString::new('Plain text')->withMode('graphemes');
        $result = $value->br2nl();
        self::assertSame('Plain text', (string) $result);
    }

    public function testBr2nlEmpty(): void
    {
        $result = XString::new('')->br2nl();
        self::assertSame('', (string) $result);
    }

    public function testBr2nlImmutable(): void
    {
        $value = XString::new('Keep<br>Original');
        $value->br2nl();
        self::assertSame('Keep<br>Original', (string) $value);
    }

}
