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
    private ?string $body;
    private bool $includeClosingTag;
    private bool $newlineBeforeClosing;

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
        array $attributes = [],
        ?string $body = null,
        bool $include_closing_tag = false,
        bool $newline_before_closing = false
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
        $this->body = $body;
        $this->includeClosingTag = $include_closing_tag;
        $this->newlineBeforeClosing = $newline_before_closing;
    }

    public static function new(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): self
    {
        return new self($tag_name, $self_closing, false, $case_sensitive);
    }

    public static function closeTag(string $tag_name, bool $case_sensitive = false): self
    {
        return new self($tag_name, false, true, $case_sensitive);
    }

    public static function endTag(string $tag_name, bool $case_sensitive = false): self
    {
        return self::closeTag($tag_name, $case_sensitive);
    }

    /**
     * @param string|array<int, string> $class_name
     */
    public function withClass(string|array ...$class_name): self
    {
        if ($this->isClosing) {
            throw new InvalidArgumentException('Closing tags cannot define classes.');
        }

        if ($class_name === []) {
            throw new InvalidArgumentException('At least one class name must be provided.');
        }

        $input = count($class_name) === 1
            ? $class_name[0]
            : self::flattenClassInput($class_name);

        $existing = $this->classes;
        foreach (self::normalizeClassInput($input) as $candidate) {
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
            $this->attributes,
            $this->body,
            $this->includeClosingTag,
            $this->newlineBeforeClosing
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
            $this->attributes,
            $this->body,
            $this->includeClosingTag,
            $this->newlineBeforeClosing
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
            $attributes,
            $this->body,
            $this->includeClosingTag,
            $this->newlineBeforeClosing
        );
    }

    public function withBody(HtmlTag|Newline|Regex|Stringable|string|array $body): self
    {
        if ($this->isClosing) {
            throw new InvalidArgumentException('Closing tags cannot define a body.');
        }

        if ($this->selfClosing) {
            throw new InvalidArgumentException('Self-closing tags cannot define a body.');
        }

        $normalized = self::normalizeBodyContent($body);
        $combined_body = ($this->body ?? '') . $normalized;

        return new self(
            $this->rawTagName,
            false,
            $this->isClosing,
            $this->caseSensitive,
            $this->classes,
            $this->id,
            $this->attributes,
            $combined_body,
            $this->includeClosingTag,
            $this->newlineBeforeClosing
        );
    }

    public function withEndTag(bool $append_newline = true): self
    {
        if ($this->isClosing) {
            throw new InvalidArgumentException('Closing tags already represent an end tag.');
        }

        if ($this->selfClosing) {
            throw new InvalidArgumentException('Self-closing tags cannot include an end tag.');
        }

        return new self(
            $this->rawTagName,
            false,
            $this->isClosing,
            $this->caseSensitive,
            $this->classes,
            $this->id,
            $this->attributes,
            $this->body,
            true,
            $append_newline
        );
    }

    public function isClosingTag(): bool
    {
        return $this->isClosing;
    }

    public function isSelfClosingTag(): bool
    {
        return $this->selfClosing;
    }

    public function isCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    public function getRawTagName(): string
    {
        return $this->rawTagName;
    }

    public function matchesFragment(string $fragment): bool
    {
        return $this->isClosing
            ? $this->matchesClosingFragment($fragment)
            : $this->matchesOpeningFragment($fragment);
    }

    public function __toString(): string
    {
        $tag_name = $this->tagName;

        if ($this->isClosing) {
            return sprintf('</%s>', $tag_name);
        }

        $attribute_string = $this->renderAttributes();
        $prefix = $attribute_string === ''
            ? sprintf('<%s', $tag_name)
            : sprintf('<%s %s', $tag_name, $attribute_string);

        if ($this->selfClosing && $this->body === null && !$this->includeClosingTag) {
            return $prefix . ' />';
        }

        $prefix .= '>';

        $body = $this->body ?? '';

        if ($this->includeClosingTag) {
            if ($this->newlineBeforeClosing && !str_ends_with($body, PHP_EOL) && $body !== '') {
                $body .= PHP_EOL;
            }

            if ($this->newlineBeforeClosing && $body === '') {
                $body = PHP_EOL;
            }

            return $prefix . $body . sprintf('</%s>', $tag_name);
        }

        return $prefix . $body;
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

    /**
     * @param array<int, string|array<int, string>> $class_name
     * @return array<int, string>
     */
    private static function flattenClassInput(array $class_name): array
    {
        $flattened = [];

        foreach ($class_name as $value) {
            if (is_array($value)) {
                foreach ($value as $nested) {
                    $flattened[] = $nested;
                }
            } else {
                $flattened[] = $value;
            }
        }

        return $flattened;
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

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $content
     */
    private static function normalizeBodyContent(HtmlTag|Newline|Regex|Stringable|string|array $content): string
    {
        if (is_array($content)) {
            $buffer = '';
            foreach ($content as $fragment) {
                $buffer .= self::normalizeBodyContent($fragment);
            }

            return $buffer;
        }

        if ($content instanceof Stringable) {
            return (string) $content;
        }

        return (string) $content;
    }

    private function matchesClosingFragment(string $fragment): bool
    {
        $trimmed = trim($fragment);
        if ($trimmed === '') {
            return false;
        }

        $pattern = sprintf(
            '/^<\/%s\s*>$/%s',
            preg_quote($this->rawTagName, '/'),
            $this->caseSensitive ? '' : 'i'
        );

        return preg_match($pattern, $trimmed) === 1;
    }

    private function matchesOpeningFragment(string $fragment): bool
    {
        $trimmed = trim($fragment);
        if ($trimmed === '') {
            return false;
        }

        $ending = '\\s*\/?>';
        $pattern = sprintf(
            '/^<\\s*%s\\b([^>]*)%s$/%s',
            preg_quote($this->rawTagName, '/'),
            $ending,
            $this->caseSensitive ? '' : 'i'
        );

        if (preg_match($pattern, $trimmed, $matches) !== 1) {
            return false;
        }

        $attribute_string = $matches[1] ?? '';
        $parsed = self::parseAttributePairs($attribute_string);
        $attributes = $parsed['attributes'];
        $class_list = $parsed['classList'];

        if ($this->id !== null) {
            $candidate_id = $attributes['id'] ?? null;
            if ($candidate_id === null) {
                return false;
            }

            if ($this->caseSensitive) {
                if ($candidate_id !== $this->id) {
                    return false;
                }
            } elseif (strcasecmp($candidate_id, $this->id) !== 0) {
                return false;
            }
        }

        if ($this->classes !== []) {
            if ($class_list === []) {
                return false;
            }

            $candidate_classes = $this->caseSensitive
                ? $class_list
                : array_map('strtolower', $class_list);
            $required_classes = $this->caseSensitive
                ? $this->classes
                : array_map('strtolower', $this->classes);

            foreach ($required_classes as $class) {
                if (!in_array($class, $candidate_classes, true)) {
                    return false;
                }
            }
        }

        foreach ($this->attributes as $attribute) {
            $attribute_name = strtolower($attribute['name']);
            if (!array_key_exists($attribute_name, $attributes)) {
                return false;
            }

            $candidate_value = $attributes[$attribute_name];
            if ($attribute['value'] !== null) {
                if ($candidate_value === null) {
                    return false;
                }

                if ($this->caseSensitive) {
                    if ($candidate_value !== $attribute['value']) {
                        return false;
                    }
                } elseif (strcasecmp($candidate_value, (string) $attribute['value']) !== 0) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array{attributes: array<string, string|null>, classList: array<int, string>}
     */
    private static function parseAttributePairs(string $attribute_string): array
    {
        $attributes = [];
        $class_list = [];

        $clean = trim($attribute_string);
        if ($clean === '') {
            return ['attributes' => $attributes, 'classList' => $class_list];
        }

        if (str_ends_with($clean, '/')) {
            $clean = rtrim(substr($clean, 0, -1));
        }

        if ($clean === '') {
            return ['attributes' => $attributes, 'classList' => $class_list];
        }

        $pattern = "/([A-Za-z_][A-Za-z0-9:._-]*)(?:\\s*=\\s*(?:\"([^\"]*)\"|'([^']*)'|([^\\s\"'=<>`]+)))?/";

        if (preg_match_all($pattern, $clean, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $name = strtolower($match[1]);
                $value = $match[2] ?? $match[3] ?? $match[4] ?? null;

                $attributes[$name] = $value;

                if ($name === 'class' && $value !== null) {
                    try {
                        $class_list = self::normalizeClassInput($value);
                    } catch (InvalidArgumentException) {
                        $class_list = [];
                    }
                }
            }
        }

        return ['attributes' => $attributes, 'classList' => $class_list];
    }
}
