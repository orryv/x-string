<?php

declare(strict_types=1);

namespace Orryv;

use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;

/**
 * @internal Factory helpers mirroring the "type" shortcuts documented in README.
 */
final class XStringType
{
    private function __construct()
    {
    }

    public static function newline(?string $newline = null): Newline
    {
        return Newline::new($newline);
    }

    public static function regex(string $pattern): Regex
    {
        return Regex::new($pattern);
    }

    public static function htmlTag(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): HtmlTag
    {
        return HtmlTag::new($tag_name, $self_closing, $case_sensitive);
    }

    public static function htmlCloseTag(string $tag_name, bool $case_sensitive = false): HtmlTag
    {
        return HtmlTag::closeTag($tag_name, $case_sensitive);
    }
}
