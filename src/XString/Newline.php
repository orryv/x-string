<?php

declare(strict_types=1);

namespace Orryv\XString;

use InvalidArgumentException;
use Stringable;

final class Newline implements Stringable
{
    private const LINE_TRIM_CHARACTERS = " \t";

    private string $value;
    /**
     * @var array{
     *     type: 'starts_with'|'ends_with'|'contains'|'equals',
     *     needle: string,
     *     trim?: bool
     * }|null
     */
    private ?array $lineConstraint;

    private function __construct(string $value, ?array $line_constraint = null)
    {
        $this->value = $value;
        $this->lineConstraint = $line_constraint;
    }

    public static function new(?string $newline = null): self
    {
        return new self($newline ?? PHP_EOL);
    }

    public function startsWith(?string $string, bool $trim = false): self
    {
        $needle = $string ?? '';
        $normalized_prefix = $trim ? ltrim($needle, self::LINE_TRIM_CHARACTERS) : $needle;

        if ($normalized_prefix === '') {
            throw new InvalidArgumentException('Prefix for startsWith cannot be empty.');
        }

        return new self(
            $this->value,
            [
                'type' => 'starts_with',
                'needle' => $normalized_prefix,
                'trim' => $trim,
            ]
        );
    }

    /**
     * @return array{type: 'starts_with'|'ends_with'|'contains'|'equals', needle: string, trim?: bool}|null
     */
    public function getLineConstraint(): ?array
    {
        return $this->lineConstraint;
    }

    /**
     * @return array{prefix: string, trim: bool}|null
     */
    public function getStartsWithConfig(): ?array
    {
        if ($this->lineConstraint === null || $this->lineConstraint['type'] !== 'starts_with') {
            return null;
        }

        return [
            'prefix' => $this->lineConstraint['needle'],
            'trim' => (bool) ($this->lineConstraint['trim'] ?? false),
        ];
    }

    public function endsWith(?string $string, bool $trim = false): self
    {
        $needle = $string ?? '';
        $normalized_suffix = $trim ? rtrim($needle, self::LINE_TRIM_CHARACTERS) : $needle;

        if ($normalized_suffix === '') {
            throw new InvalidArgumentException('Suffix for endsWith cannot be empty.');
        }

        return new self(
            $this->value,
            [
                'type' => 'ends_with',
                'needle' => $normalized_suffix,
                'trim' => $trim,
            ]
        );
    }

    public function contains(?string $string): self
    {
        $needle = $string ?? '';

        if ($needle === '') {
            throw new InvalidArgumentException('Needle for contains cannot be empty.');
        }

        return new self(
            $this->value,
            [
                'type' => 'contains',
                'needle' => $needle,
            ]
        );
    }

    public function equals(?string $string): self
    {
        return new self(
            $this->value,
            [
                'type' => 'equals',
                'needle' => $string ?? '',
            ]
        );
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
