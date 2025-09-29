<?php

declare(strict_types=1);

namespace Orryv\XString;

use Stringable;

final class Regex implements Stringable
{
    private string $pattern;

    private function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public static function new(string $pattern): self
    {
        return new self($pattern);
    }

    public function __toString(): string
    {
        return $this->pattern;
    }
}
