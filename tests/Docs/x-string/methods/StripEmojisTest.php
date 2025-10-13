<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class StripEmojisTest extends TestCase
{
    public function testStripEmojisBasic(): void
    {
        $value = XString::new('Launch 🚀 success 🎉');
        $result = $value->stripEmojis();
        self::assertSame('Launch  success ', (string) $result);
    }

    public function testStripEmojisZwj(): void
    {
        $value = XString::new('Developers 👩‍💻 collaborate 👨‍💻 daily');
        $result = $value->stripEmojis();
        self::assertSame('Developers  collaborate  daily', (string) $result);
    }

    public function testStripEmojisFlags(): void
    {
        $value = XString::new('Teams 🇺🇸 vs 🇯🇵 in finals');
        $result = $value->stripEmojis();
        self::assertSame('Teams  vs  in finals', (string) $result);
    }

    public function testStripEmojisKeycap(): void
    {
        $value = XString::new('Press 1️⃣ to continue, 0️⃣ to exit.');
        $result = $value->stripEmojis();
        self::assertSame('Press  to continue,  to exit.', (string) $result);
    }

    public function testStripEmojisImmutable(): void
    {
        $original = XString::new('Important notice #42');
        $processed = $original->stripEmojis();
        self::assertSame('Important notice #42', (string) $processed);
        self::assertSame('Important notice #42', (string) $original);
    }

    public function testStripEmojisEmpty(): void
    {
        $result = XString::new('')->stripEmojis();
        self::assertSame('', (string) $result);
    }

}
