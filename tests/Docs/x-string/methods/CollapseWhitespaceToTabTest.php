<?php

declare(strict_types=1);

namespace Orryv\XArray\Tests\Docs\XString\Methods;

use PHPUnit\Framework\TestCase;
use Orryv\XString;

final class CollapseWhitespaceToTabTest extends TestCase
{
    public function testCollapseWhitespaceToTabBasic(): void
    {
        $list = XString::new("apple  banana\ncherry");
        $result = $list->collapseWhitespaceToTab();
        self::assertSame("apple\tbanana\tcherry", (string) $result);
    }

    public function testCollapseWhitespaceToTabMixed(): void
    {
        $text = XString::new("value1\r\n  value2\t\tvalue3");
        $result = $text->collapseWhitespaceToTab();
        self::assertSame("value1\tvalue2\tvalue3", (string) $result);
        self::assertSame("value1\r\n  value2\t\tvalue3", (string) $text);
    }

    public function testCollapseWhitespaceToTabEmpty(): void
    {
        $empty = XString::new('');
        $result = $empty->collapseWhitespaceToTab();
        self::assertSame('', (string) $result);
    }

    public function testCollapseWhitespaceToTabImmutability(): void
    {
        $value = XString::new(" a \n b ");
        $collapsed = $value->collapseWhitespaceToTab();
        self::assertSame(" a \n b ", (string) $value);
        self::assertSame("\ta\tb\t", (string) $collapsed);
    }

}
