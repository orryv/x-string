<?php

declare(strict_types=1);

namespace Orryv\XString;

use InvalidArgumentException;
use Stringable;

final class HtmlTag implements Stringable
{
    private string $rawTagName;
    private string $tagName;
    private bool $selfClosing;
    private bool $isClosing;
    private bool $caseSensitive;
    /** @var array<int, string> */
    private array $classes;
    private ?string $id;
    /**
     * @var array<int, array{name: string, value: string|null}>
     */
    private array $attributes;

    /**
     * @param array<int, string> $classes
     * @param array<int, array{name: string, value: string|null}> $attributes
     */
    private function __construct(
        string $tag_name,
        bool $self_closing,
        bool $is_closing,
        bool $case_sensitive,
        array $classes = [],
        ?string $id = null,
        array $attributes = []
    ) {
        $normalized_name = self::normalizeTagName($tag_name);

        $this->rawTagName = $normalized_name;
        $this->tagName = $case_sensitive ? $normalized_name : strtolower($normalized_name);
        $this->selfClosing = $self_closing;
        $this->isClosing = $is_closing;
        $this->caseSensitive = $case_sensitive;
        $this->classes = $classes;
        $this->id = $id;
        $this->attributes = $attributes;
    }

    public static function new(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): self
    {
        return new self($tag_name, $self_closing, false, $case_sensitive);
    }

    public static function closeTag(string $tag_name, bool $case_sensitive = false): self
    {
        return new self($tag_name, false, true, $case_sensitive);
    }

    /**
     * @param string|array<int, string> $class_name
     */
    public function withClass(string|array $class_name): self
    {
        if ($this->isClosing) {
            throw new InvalidArgumentException('Closing tags cannot define classes.');
        }

        $existing = $this->classes;
        foreach (self::normalizeClassInput($class_name) as $candidate) {
            if (!in_array($candidate, $existing, true)) {
                $existing[] = $candidate;
            }
        }

        return new self(
            $this->rawTagName,
            $this->selfClosing,
            $this->isClosing,
            $this->caseSensitive,
            $existing,
            $this->id,
            $this->attributes
        );
    }

    public function withId(string $id): self
    {
        if ($this->isClosing) {
            throw new InvalidArgumentException('Closing tags cannot define an ID.');
        }

        $normalized = trim($id);
        if ($normalized === '') {
            throw new InvalidArgumentException('ID cannot be empty.');
        }

        return new self(
            $this->rawTagName,
            $this->selfClosing,
            $this->isClosing,
            $this->caseSensitive,
            $this->classes,
            $normalized,
            $this->attributes
        );
    }

    public function withAttribute(string $attr_name, ?string $attr_value = null, bool $case_sensitive = false): self
    {
        if ($this->isClosing) {
            throw new InvalidArgumentException('Closing tags cannot define attributes.');
        }

        $trimmed_name = trim($attr_name);
        if ($trimmed_name === '') {
            throw new InvalidArgumentException('Attribute name cannot be empty.');
        }

        $normalized_name = $case_sensitive ? $trimmed_name : strtolower($trimmed_name);
        $lower_name = strtolower($trimmed_name);

        if ($lower_name === 'class') {
            if ($attr_value === null) {
                throw new InvalidArgumentException('Class attribute requires a value.');
            }

            return $this->withClass(preg_split('/\s+/', $attr_value, -1, PREG_SPLIT_NO_EMPTY) ?: []);
        }

        if ($lower_name === 'id') {
            if ($attr_value === null) {
                throw new InvalidArgumentException('ID attribute requires a value.');
            }

            return $this->withId($attr_value);
        }

        $attributes = $this->attributes;
        $attributes[] = [
            'name' => $case_sensitive ? $trimmed_name : $normalized_name,
            'value' => $attr_value,
        ];

        return new self(
            $this->rawTagName,
            $this->selfClosing,
            $this->isClosing,
            $this->caseSensitive,
            $this->classes,
            $this->id,
            $attributes
        );
    }

    public function __toString(): string
    {
        $tag_name = $this->tagName;

        if ($this->isClosing) {
            return sprintf('</%s>', $tag_name);
        }

        $attribute_string = $this->renderAttributes();
        $suffix = $this->selfClosing ? ' />' : '>';

        if ($attribute_string === '') {
            return sprintf('<%s%s', $tag_name, $suffix);
        }

        return sprintf('<%s %s%s', $tag_name, $attribute_string, $suffix);
    }

    /**
     * @return array<int, string>
     */
    private static function normalizeClassInput(string|array $class_name): array
    {
        $candidates = is_array($class_name)
            ? $class_name
            : preg_split('/\s+/', $class_name, -1, PREG_SPLIT_NO_EMPTY);

        if ($candidates === false) {
            throw new InvalidArgumentException('Invalid class name input.');
        }

        $normalized = [];
        foreach ($candidates as $candidate) {
            $trimmed = trim((string) $candidate);
            if ($trimmed === '') {
                continue;
            }

            if (preg_match('/\s/', $trimmed) === 1) {
                throw new InvalidArgumentException('Class names cannot contain whitespace.');
            }

            $normalized[] = $trimmed;
        }

        return $normalized;
    }

    private static function normalizeTagName(string $tag_name): string
    {
        $trimmed = trim($tag_name);
        if ($trimmed === '') {
            throw new InvalidArgumentException('Tag name cannot be empty.');
        }

        if (preg_match('/^[A-Za-z][A-Za-z0-9:_-]*$/', $trimmed) !== 1) {
            throw new InvalidArgumentException('Tag name contains invalid characters.');
        }

        return $trimmed;
    }

    private function renderAttributes(): string
    {
        $parts = [];

        if ($this->id !== null) {
            $parts[] = sprintf('id="%s"', $this->id);
        }

        if ($this->classes !== []) {
            $parts[] = sprintf('class="%s"', implode(' ', $this->classes));
        }

        foreach ($this->attributes as $attribute) {
            $parts[] = $attribute['value'] === null
                ? $attribute['name']
                : sprintf('%s="%s"', $attribute['name'], $attribute['value']);
        }

        return implode(' ', $parts);
    }
}
