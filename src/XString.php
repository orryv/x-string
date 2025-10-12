<?php

declare(strict_types=1);

namespace Orryv;

use InvalidArgumentException;
use Normalizer;
use Orryv\XString\HtmlTag;
use Orryv\XString\Newline;
use Orryv\XString\Regex;
use Orryv\XString\Compute\Similarity;
use Orryv\XString\Exceptions\EmptyCharacterSetException;
use Orryv\XString\Exceptions\InvalidLengthException;
use RuntimeException;
use Stringable;
use ValueError;

final class XString implements Stringable
{
    private const DEFAULT_ENCODING = 'UTF-8';
    private const DEFAULT_MODE = 'graphemes';
    /** @var array<int, string> */
    private const VALID_MODES = ['bytes', 'codepoints', 'graphemes'];

    private string $value;
    private string $mode;
    private string $encoding;

    private function __construct(string $value, string $mode = self::DEFAULT_MODE, string $encoding = self::DEFAULT_ENCODING)
    {
        $this->value = $value;
        $this->mode = self::normalizeMode($mode);
        $this->encoding = self::normalizeEncoding($encoding);
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string> $data
     */
    public static function new(HtmlTag|Newline|Regex|Stringable|string|array $data = ''): self
    {
        if (is_array($data)) {
            return new self(self::concatenateFragments($data));
        }

        return new self(self::normalizeFragment($data));
    }

    public static function rand(int $length, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): self
    {
        if ($length < 1) {
            throw new InvalidLengthException('Random length must be at least 1.');
        }

        $pool = self::splitCharacters($characters);
        if (empty($pool)) {
            throw new EmptyCharacterSetException('Random generator requires at least one character.');
        }

        $buffer = '';
        $maxIndex = count($pool) - 1;
        for ($i = 0; $i < $length; $i++) {
            $buffer .= $pool[random_int(0, $maxIndex)];
        }

        return new self($buffer);
    }

    public static function randInt(int $length, int $int_min = 0, int $int_max = 9): self
    {
        if ($length < 1) {
            throw new InvalidLengthException('Random length must be at least 1.');
        }

        if ($int_min > $int_max) {
            throw new InvalidArgumentException('The minimum digit cannot be greater than the maximum digit.');
        }

        if ($int_min < 0 || $int_max > 9) {
            throw new InvalidArgumentException('randInt only supports digits in the inclusive range 0-9.');
        }

        $buffer = '';
        for ($i = 0; $i < $length; $i++) {
            $buffer .= (string) random_int($int_min, $int_max);
        }

        return new self($buffer);
    }

    public static function randLower(int $length, bool $include_numbers = false): self
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        if ($include_numbers) {
            $characters .= '0123456789';
        }

        return self::rand($length, $characters);
    }

    public static function randUpper(int $length, bool $include_numbers = false): self
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($include_numbers) {
            $characters .= '0123456789';
        }

        return self::rand($length, $characters);
    }

    public static function randAlpha(int $length): self
    {
        return self::rand($length, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    public static function randHex(int $length): self
    {
        return self::rand($length, '0123456789abcdef');
    }

    public static function randBase64(int $length): self
    {
        return self::rand($length, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/');
    }

    public static function randBase62(int $length): self
    {
        return self::rand($length, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
    }

    public static function uuid(int $version = 4, ?string $namespace = null, ?string $name = null): self
    {
        return new self(self::generateUuid($version, $namespace, $name));
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string> $data
     */
    public static function implode(array $data, string $glue = ''): self
    {
        $normalized = [];
        foreach ($data as $fragment) {
            $normalized[] = self::normalizeFragment($fragment);
        }

        return new self(implode($glue, $normalized));
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string> $data
     */
    public static function join(array $data, string $glue = ''): self
    {
        return self::implode($data, $glue);
    }

    public static function fromFile(
        string $file_path,
        ?int $length = null,
        ?int $offset = 0,
        string $encoding = self::DEFAULT_ENCODING
    ): self {
        if (!is_file($file_path) || !is_readable($file_path)) {
            throw new RuntimeException(sprintf('File "%s" is not readable.', $file_path));
        }

        $offset = $offset ?? 0;
        if ($offset < 0) {
            throw new InvalidArgumentException('Offset must be greater than or equal to 0.');
        }

        if ($length !== null && $length < 0) {
            throw new InvalidArgumentException('Length must be greater than or equal to 0.');
        }

        $encoding = self::normalizeEncoding($encoding);

        $content = $length === null
            ? file_get_contents($file_path, false, null, $offset)
            : file_get_contents($file_path, false, null, $offset, $length);

        if ($content === false) {
            throw new RuntimeException(sprintf('Failed to read file "%s".', $file_path));
        }

        return new self($content, self::DEFAULT_MODE, $encoding);
    }

    public function length(): int
    {
        return match ($this->mode) {
            'bytes' => strlen($this->value),
            'codepoints' => function_exists('mb_strlen')
                ? mb_strlen($this->value, $this->encoding)
                : strlen($this->value),
            default => $this->graphemeLengthOrFallback(),
        };
    }

    public function byteLength(): int
    {
        return strlen($this->value);
    }

    public function graphemeLength(): int
    {
        return $this->graphemeLengthOrFallback();
    }

    public function withMode(string $mode = self::DEFAULT_MODE, string $encoding = self::DEFAULT_ENCODING): self
    {
        return new self($this->value, $mode, $encoding);
    }

    public function asBytes(string $encoding = self::DEFAULT_ENCODING): self
    {
        return $this->withMode('bytes', $encoding);
    }

    public function asCodepoints(string $encoding = self::DEFAULT_ENCODING): self
    {
        return $this->withMode('codepoints', $encoding);
    }

    public function asGraphemes(string $encoding = self::DEFAULT_ENCODING): self
    {
        return $this->withMode('graphemes', $encoding);
    }

    public function charAt(int $index): string
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        if ($units === []) {
            throw new InvalidArgumentException('Index is out of range.');
        }

        $count = count($units);
        $position = $index >= 0 ? $index : $count + $index;

        if ($position < 0 || $position >= $count) {
            throw new InvalidArgumentException('Index is out of range.');
        }

        return $units[$position];
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string>|null $data
     */
    public function append(HtmlTag|Newline|Regex|Stringable|string|array|null $data): self
    {
        $additional = is_array($data)
            ? self::concatenateFragments($data)
            : self::normalizeFragment($data);

        return new self($this->value . $additional, $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string>|null $data
     */
    public function prepend(HtmlTag|Newline|Regex|Stringable|string|array|null $data): self
    {
        $additional = is_array($data)
            ? self::concatenateFragments($data)
            : self::normalizeFragment($data);

        return new self($additional . $this->value, $this->mode, $this->encoding);
    }

    public function substr(int $start, ?int $length = null): self
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        $total = count($units);

        if ($total === 0) {
            return new self('', $this->mode, $this->encoding);
        }

        $start_index = $start >= 0 ? $start : $total + $start;
        if ($start_index < 0) {
            $start_index = 0;
        }

        if ($start_index >= $total) {
            return new self('', $this->mode, $this->encoding);
        }

        if ($length === null) {
            $slice = array_slice($units, $start_index);
        } elseif ($length >= 0) {
            $slice = array_slice($units, $start_index, $length);
        } else {
            $end_index = $total + $length;
            if ($end_index <= $start_index) {
                return new self('', $this->mode, $this->encoding);
            }

            $slice = array_slice($units, $start_index, $end_index - $start_index);
        }

        return new self(implode('', $slice), $this->mode, $this->encoding);
    }

    public function repeat(int $times): self
    {
        if ($times < 0) {
            throw new InvalidArgumentException('Repeat count must be greater than or equal to 0.');
        }

        if ($times === 0) {
            return new self('', $this->mode, $this->encoding);
        }

        return new self(str_repeat($this->value, $times), $this->mode, $this->encoding);
    }

    public function reverse(): self
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        if ($units === []) {
            return new self('', $this->mode, $this->encoding);
        }

        $units = array_reverse($units);

        return new self(implode('', $units), $this->mode, $this->encoding);
    }

    public function shuffle(): self
    {
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);
        if (count($units) <= 1) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        shuffle($units);

        return new self(implode('', $units), $this->mode, $this->encoding);
    }

    public function slug(Newline|HtmlTag|string $separator = '-'): self
    {
        $normalized_separator = self::normalizeFragment($separator);
        if ($normalized_separator === '') {
            throw new InvalidArgumentException('Separator cannot be empty.');
        }

        $slug_source = self::toAsciiForSlug($this->value, $this->encoding);
        $slug_source = self::lowercaseString($slug_source, $this->encoding);

        $slug = preg_replace('/[^a-z0-9]+/i', $normalized_separator, $slug_source);
        if ($slug === null) {
            $slug = '';
        }

        $quoted_separator = preg_quote($normalized_separator, '/');
        $slug = preg_replace(sprintf('/%s{2,}/', $quoted_separator), $normalized_separator, $slug);
        if ($slug === null) {
            $slug = '';
        }

        $slug = trim($slug, $normalized_separator);

        return new self($slug, $this->mode, $this->encoding);
    }

    public function fileNameSlug(Newline|HtmlTag|string $separator = '-'): self
    {
        $normalized_separator = self::normalizeFragment($separator);
        if ($normalized_separator === '') {
            throw new InvalidArgumentException('Separator cannot be empty.');
        }

        $slug_source = self::toAsciiForSlug($this->value, $this->encoding);
        $slug_source = self::lowercaseString($slug_source, $this->encoding);

        $slug_source = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], ' ', $slug_source);

        $slug = preg_replace('/[^a-z0-9._-]+/i', $normalized_separator, $slug_source);
        if ($slug === null) {
            $slug = '';
        }

        $slug = preg_replace('/\.{2,}/', '.', $slug);
        if ($slug === null) {
            $slug = '';
        }

        $quoted_separator = preg_quote($normalized_separator, '/');
        $slug = preg_replace(sprintf('/(?:%s){2,}/', $quoted_separator), $normalized_separator, $slug);
        if ($slug === null) {
            $slug = '';
        }

        if ($normalized_separator !== '') {
            while (str_contains($slug, $normalized_separator . '.')) {
                $slug = str_replace($normalized_separator . '.', '.', $slug);
            }

            while (str_contains($slug, '.' . $normalized_separator)) {
                $slug = str_replace('.' . $normalized_separator, '.', $slug);
            }
        }

        $slug = trim($slug, $normalized_separator . '.');

        return new self($slug, $this->mode, $this->encoding);
    }

    public function insertAtInterval(Newline|HtmlTag|Regex|string $insert, int $interval): self
    {
        if ($interval < 1) {
            throw new InvalidArgumentException('Interval must be at least 1.');
        }

        $fragment = self::normalizeFragment($insert);
        $units = self::splitByMode($this->value, $this->mode, $this->encoding);

        if ($units === []) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $parts = [];
        foreach ($units as $index => $unit) {
            if ($index > 0 && $index % $interval === 0) {
                $parts[] = $fragment;
            }

            $parts[] = $unit;
        }

        return new self(implode('', $parts), $this->mode, $this->encoding);
    }

    public function wrap(Newline|HtmlTag|Regex|string $before, Newline|HtmlTag|Regex|string|null $after = null): self
    {
        $prefix = self::normalizeFragment($before);
        $suffix = $after === null
            ? $prefix
            : self::normalizeFragment($after);

        return new self($prefix . $this->value . $suffix, $this->mode, $this->encoding);
    }

    public function indent(int $spaces = 2, int $tabs = 0, int $lines = 0): self
    {
        if ($spaces < 0 || $tabs < 0) {
            throw new InvalidArgumentException('Indentation parameters must be greater than or equal to 0.');
        }

        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $indentation = str_repeat(' ', $spaces) . str_repeat("\t", $tabs);
        if ($indentation === '') {
            return new self($this->value, $this->mode, $this->encoding);
        }

        if ($lines < 0) {
            $total_lines = preg_match_all('/^/m', $this->value);
            if ($total_lines === false || $total_lines === 0) {
                return new self($this->value, $this->mode, $this->encoding);
            }

            $target = min(-$lines, $total_lines);
            if ($target === 0) {
                return new self($this->value, $this->mode, $this->encoding);
            }

            $processed = 0;
            $result = preg_replace_callback(
                '/^/m',
                static function (array $matches) use (&$processed, $total_lines, $target, $indentation): string {
                    $should_indent = $processed >= ($total_lines - $target);
                    $processed++;

                    return $should_indent ? $indentation : $matches[0];
                },
                $this->value
            );
        } else {
            $limit = $lines > 0 ? $lines : -1;

            $result = preg_replace('/^/m', $indentation, $this->value, $limit);
        }

        if ($result === null) {
            $result = $this->value;
        }

        return new self($result, $this->mode, $this->encoding);
    }

    public function outdent(int $spaces = 2, int $tabs = 0, int $lines = 0): self
    {
        if ($spaces < 0 || $tabs < 0) {
            throw new InvalidArgumentException('Indentation parameters must be greater than or equal to 0.');
        }

        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        if ($spaces === 0 && $tabs === 0) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        if ($lines < 0) {
            $total_lines = preg_match_all('/^/m', $this->value);
            if ($total_lines === false || $total_lines === 0) {
                return new self($this->value, $this->mode, $this->encoding);
            }

            $target = min(-$lines, $total_lines);
            if ($target === 0) {
                return new self($this->value, $this->mode, $this->encoding);
            }

            $processed = 0;
            $result = preg_replace_callback(
                '/^([ \t]*)/m',
                static function (array $matches) use (&$processed, $total_lines, $target, $spaces, $tabs): string {
                    $should_outdent = $processed >= ($total_lines - $target);
                    $processed++;

                    if ($should_outdent) {
                        return self::removeIndentationPrefix($matches[1], $spaces, $tabs);
                    }

                    return $matches[1];
                },
                $this->value
            );
        } else {
            $limit = $lines > 0 ? $lines : -1;

            $result = preg_replace_callback(
                '/^([ \t]*)/m',
                static function (array $matches) use ($spaces, $tabs): string {
                    return self::removeIndentationPrefix($matches[1], $spaces, $tabs);
                },
                $this->value,
                $limit
            );
        }

        if ($result === null) {
            $result = $this->value;
        }

        return new self($result, $this->mode, $this->encoding);
    }

    public function normalize(int $form = Normalizer::FORM_C): self
    {
        if (!class_exists(Normalizer::class)) {
            throw new RuntimeException('The intl extension (Normalizer class) is required for normalization.');
        }

        try {
            $normalized = Normalizer::normalize($this->value, $form);
        } catch (ValueError $exception) {
            throw new InvalidArgumentException('Invalid normalization form provided.', 0, $exception);
        }
        if ($normalized === false) {
            throw new RuntimeException('Failed to normalize the string.');
        }

        return new self($normalized, $this->mode, $this->encoding);
    }

    public function pad(
        int $length,
        Newline|HtmlTag|Regex|string $pad_string = ' ',
        bool $left = true,
        bool $right = false
    ): self {
        if ($length < 0) {
            throw new InvalidArgumentException('Target length must be greater than or equal to 0.');
        }

        if (!$left && !$right) {
            throw new InvalidArgumentException('At least one side must be selected for padding.');
        }

        $current_length = $this->length();
        if ($length <= $current_length) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $pad_fragment = self::normalizeFragment($pad_string);
        if ($pad_fragment === '') {
            throw new InvalidArgumentException('Pad string cannot be empty.');
        }

        $units_needed = $length - $current_length;

        if ($left && $right) {
            $left_units = intdiv($units_needed, 2);
            $right_units = $units_needed - $left_units;
        } elseif ($left) {
            $left_units = $units_needed;
            $right_units = 0;
        } else {
            $left_units = 0;
            $right_units = $units_needed;
        }

        $left_padding = $left_units > 0
            ? self::buildPadding($pad_fragment, $left_units, $this->mode, $this->encoding)
            : '';
        $right_padding = $right_units > 0
            ? self::buildPadding($pad_fragment, $right_units, $this->mode, $this->encoding)
            : '';

        return new self($left_padding . $this->value . $right_padding, $this->mode, $this->encoding);
    }

    public function lpad(int $length, Newline|HtmlTag|Regex|string $pad_string = ' '): self
    {
        return $this->pad($length, $pad_string, true, false);
    }

    public function rpad(int $length, Newline|HtmlTag|Regex|string $pad_string = ' '): self
    {
        return $this->pad($length, $pad_string, false, true);
    }

    public function center(int $length, Newline|HtmlTag|Regex|string $pad_string = ' '): self
    {
        return $this->pad($length, $pad_string, true, true);
    }

    public function mask(
        Newline|HtmlTag|Regex|string $mask,
        Newline|HtmlTag|Regex|string $mask_char = '#',
        bool $reversed = false
    ): self {
        $pattern = self::normalizeFragment($mask);
        $placeholder = self::normalizeFragment($mask_char);

        $placeholder_units = self::splitGraphemes($placeholder, $this->encoding);
        if (count($placeholder_units) !== 1) {
            throw new InvalidArgumentException('Mask placeholder must be a single grapheme.');
        }

        $placeholder_unit = $placeholder_units[0];
        $mask_units = self::splitGraphemes($pattern, $this->encoding);
        $source_units = self::splitByMode($this->value, $this->mode, $this->encoding);

        $result = '';
        $source_count = count($source_units);

        if ($reversed) {
            $placeholder_count = 0;
            foreach ($mask_units as $mask_unit) {
                if ($mask_unit === $placeholder_unit) {
                    $placeholder_count++;
                }
            }

            $source_index = max(0, $source_count - $placeholder_count);
        } else {
            $source_index = 0;
        }

        foreach ($mask_units as $mask_unit) {
            if ($mask_unit === $placeholder_unit) {
                if ($source_index < $source_count) {
                    $result .= $source_units[$source_index];
                    $source_index++;
                } else {
                    break;
                }

                continue;
            }

            $result .= $mask_unit;
        }

        return new self($result, $this->mode, $this->encoding);
    }

    public function collapseWhitespace(bool $space = true, bool $tab = true, bool $newline = false): self
    {
        if (!$space && !$tab && !$newline) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $result = $this->value;

        if ($space) {
            $collapsed = preg_replace('/ {2,}/', ' ', $result);
            if ($collapsed !== null) {
                $result = $collapsed;
            }
        }

        if ($tab) {
            $collapsed = preg_replace("/\t{2,}/", "\t", $result);
            if ($collapsed !== null) {
                $result = $collapsed;
            }
        }

        if ($newline) {
            $collapsed = preg_replace_callback(
                '/(?:\r\n|\r|\n){2,}/',
                static function (array $matches): string {
                    $sequence = $matches[0];

                    if (str_contains($sequence, "\r\n")) {
                        return "\r\n";
                    }

                    if (str_contains($sequence, "\r")) {
                        return "\r";
                    }

                    return "\n";
                },
                $result
            );

            if ($collapsed !== null) {
                $result = $collapsed;
            }
        }

        return new self($result, $this->mode, $this->encoding);
    }

    public function collapseWhitespaceToSpace(): self
    {
        return $this->collapseWhitespaceToReplacement(' ');
    }

    public function collapseWhitespaceToTab(): self
    {
        return $this->collapseWhitespaceToReplacement("\t");
    }

    public function collapseWhitespaceToNewline(): self
    {
        return $this->collapseWhitespaceToReplacement("\n");
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $start
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $end
     */
    public function between(
        HtmlTag|Newline|Regex|Stringable|string|array $start,
        HtmlTag|Newline|Regex|Stringable|string|array $end,
        bool $last_occurence = false,
        int $skip_start = 0,
        int $skip_end = 0,
        string $start_behavior = 'sequential',
        string $end_behavior = 'sequential'
    ): self {
        if ($skip_start < 0 || $skip_end < 0) {
            throw new InvalidArgumentException('Skip values must be greater than or equal to 0.');
        }

        $start_sequence = self::normalizeSearchOptions($start, $start_behavior);
        $end_sequence = self::normalizeSearchOptions($end, $end_behavior);

        if ($last_occurence) {
            $start_occurrences = self::findAllSequences($this->value, $start_sequence);
            if ($start_occurrences === []) {
                return new self('', $this->mode, $this->encoding);
            }

            $start_index = count($start_occurrences) - 1 - $skip_start;
            if ($start_index < 0 || $start_index >= count($start_occurrences)) {
                return new self('', $this->mode, $this->encoding);
            }

            $start_match = $start_occurrences[$start_index];
        } else {
            $start_match = self::findSequenceWithSkip($this->value, $start_sequence, 0, $skip_start);
            if ($start_match === null) {
                return new self('', $this->mode, $this->encoding);
            }
        }

        $end_match = self::findSequenceWithSkip($this->value, $end_sequence, $start_match['end'], $skip_end);
        if ($end_match === null || $end_match['start'] < $start_match['end']) {
            return new self('', $this->mode, $this->encoding);
        }

        return new self(
            substr($this->value, $start_match['end'], $end_match['start'] - $start_match['end']),
            $this->mode,
            $this->encoding
        );
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $start
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $end
     * @return list<string>
     */
    public function betweenAll(
        HtmlTag|Newline|Regex|Stringable|string|array $start,
        HtmlTag|Newline|Regex|Stringable|string|array $end,
        bool $reversed = false,
        string $start_behavior = 'sequential',
        string $end_behavior = 'sequential'
    ): array {
        $start_sequence = self::normalizeSearchOptions($start, $start_behavior);
        $end_sequence = self::normalizeSearchOptions($end, $end_behavior);

        if ($this->value === '') {
            return [];
        }

        $results = [];
        $offset = 0;

        while (true) {
            $start_match = self::findSequence($this->value, $start_sequence, $offset);
            if ($start_match === null) {
                break;
            }

            $end_match = self::findSequence($this->value, $end_sequence, $start_match['end']);
            if ($end_match === null) {
                break;
            }

            if ($end_match['start'] < $start_match['end']) {
                $offset = $start_match['end'];
                continue;
            }

            $results[] = substr(
                $this->value,
                $start_match['end'],
                $end_match['start'] - $start_match['end']
            );

            $offset = $end_match['end'];
        }

        if ($reversed) {
            $results = array_reverse($results);
        }

        /** @var list<string> $results */
        $results = array_values($results);

        return $results;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $search
     */
    public function before(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        bool $last_occurence = false,
        int $skip = 0,
        string $start_behavior = 'sequential'
    ): self {
        if ($skip < 0) {
            throw new InvalidArgumentException('Skip must be greater than or equal to 0.');
        }

        $sequence = self::normalizeSearchOptions($search, $start_behavior);
        $occurrences = self::findAllSequences($this->value, $sequence);

        if ($occurrences === []) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $index = $last_occurence
            ? count($occurrences) - 1 - $skip
            : $skip;

        if ($index < 0 || $index >= count($occurrences)) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $match = $occurrences[$index];

        return new self(substr($this->value, 0, $match['start']), $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string|array> $search
     */
    public function after(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        bool $last_occurence = false,
        int $skip = 0,
        string $start_behavior = 'sequential'
    ): self {
        if ($skip < 0) {
            throw new InvalidArgumentException('Skip must be greater than or equal to 0.');
        }

        $sequence = self::normalizeSearchOptions($search, $start_behavior);
        $occurrences = self::findAllSequences($this->value, $sequence);

        if ($occurrences === []) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $index = $last_occurence
            ? count($occurrences) - 1 - $skip
            : $skip;

        if ($index < 0 || $index >= count($occurrences)) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $match = $occurrences[$index];

        return new self(substr($this->value, $match['end']), $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $delimiter
     * @return list<string>
     */
    public function split(HtmlTag|Newline|Regex|Stringable|string|array $delimiter, ?int $limit = null): array
    {
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('Limit must be greater than or equal to 1 when provided.');
        }

        if ($this->value === '') {
            return [];
        }

        $delimiters = is_array($delimiter) ? $delimiter : [$delimiter];
        if ($delimiters === []) {
            throw new InvalidArgumentException('Delimiter list cannot be empty.');
        }

        $max_parts = $limit ?? PHP_INT_MAX;
        $parts = [];
        $offset = 0;

        while (count($parts) + 1 < $max_parts) {
            $match = self::findNextDelimiterMatch($this->value, $delimiters, $offset);
            if ($match === null) {
                break;
            }

            $parts[] = substr($this->value, $offset, $match['start'] - $offset);
            $offset = $match['end'];
        }

        $parts[] = substr($this->value, $offset);

        /** @var list<string> $parts */
        $parts = array_values($parts);

        return $parts;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $delimiter
     * @return list<string>
     */
    public function explode(HtmlTag|Newline|Regex|Stringable|string|array $delimiter, ?int $limit = null): array
    {
        return $this->split($delimiter, $limit);
    }

    /**
     * @return list<string>
     */
    public function lines(bool $trim = false, ?int $limit = null): array
    {
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('Limit must be greater than or equal to 1 when provided.');
        }

        if ($this->value === '') {
            return [];
        }

        $segments = self::withRegexErrorHandling(
            fn () => preg_split('/\r\n|\r|\n/', $this->value, $limit ?? -1)
        );

        if (!is_array($segments) || $segments === false) {
            $segments = [$this->value];
        }

        if ($segments === ['']) {
            return [];
        }

        if ($trim) {
            $segments = array_map(static fn (string $line): string => trim($line), $segments);
        }

        /** @var list<string> $segments */
        $segments = array_values($segments);

        return $segments;
    }

    public function lineCount(): int
    {
        return count($this->lines());
    }

    /**
     * @return list<string>
     */
    public function words(bool $trim = false, ?int $limit = null): array
    {
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('Limit must be greater than or equal to 1 when provided.');
        }

        if ($this->value === '') {
            return [];
        }

        $segments = self::withRegexErrorHandling(
            fn () => preg_split('/[\s\p{Z}]+/u', $this->value, $limit ?? -1, PREG_SPLIT_NO_EMPTY)
        );

        if (!is_array($segments) || $segments === false) {
            $segments = preg_split('/\s+/', $this->value, $limit ?? -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_array($segments) || $segments === false) {
            $segments = [];
        }

        if ($segments === []) {
            return [];
        }

        if ($trim) {
            $segments = array_map(
                static fn (string $word): string => trim($word, " \t\r\n\f\v\"'.,;:!?()[]{}<>-_")
                ,
                $segments
            );
            $segments = array_values(array_filter(
                $segments,
                static fn (string $word): bool => $word !== ''
            ));

            /** @var list<string> $segments */
            return $segments;
        }

        /** @var list<string> $segments */
        return array_values($segments);
    }

    public function wordCount(): int
    {
        return count($this->words());
    }

    public function sentenceCount(): int
    {
        $normalized = trim($this->value);
        if ($normalized === '') {
            return 0;
        }

        $boundaries = [];
        $matchCount = self::withRegexErrorHandling(
            static function () use ($normalized, &$boundaries) {
                return preg_match_all(
                    '/[.!?…]+(?:["\'\)\]\}»”’]+)?(?=\s+|\R|$)/u',
                    $normalized,
                    $boundaries,
                    PREG_OFFSET_CAPTURE
                );
            }
        );

        $entries = [];
        if (is_int($matchCount) && $matchCount > 0 && isset($boundaries[0]) && is_array($boundaries[0])) {
            /** @var list<array{0: string, 1: int}> $entries */
            $entries = $boundaries[0];
        }

        $sentenceCount = 0;
        $start = 0;
        $length = strlen($normalized);

        foreach ($entries as $entry) {
            [$punctuation, $offset] = $entry;
            if (!is_string($punctuation) || !is_int($offset)) {
                continue;
            }

            $end = $offset + strlen($punctuation);
            $fragment = substr($normalized, $start, $end - $start);
            if ($fragment === false || trim($fragment) === '') {
                continue;
            }

            if (self::fragmentEndsWithAbbreviation($fragment)) {
                continue;
            }

            $sentenceCount++;
            $start = $end;
        }

        if ($entries !== [] && $start < $length) {
            $tail = trim(substr($normalized, $start));
            if ($tail !== '') {
                $sentenceCount++;
            }
        }

        if ($sentenceCount === 0) {
            $lines = preg_split('/\R+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY);
            if (is_array($lines) && $lines !== []) {
                return count($lines);
            }

            return 1;
        }

        return $sentenceCount;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $input_delimiter
     */
    public function toSnake(Newline|HtmlTag|Regex|string|array $input_delimiter = ' '): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $words = $this->extractCaseWords($input_delimiter);

        return new self(implode('_', $words), $this->mode, $this->encoding);
    }

    public function toKebab(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $snake = $this->toSnake([' ', '-', '_']);

        return new self(str_replace('_', '-', (string) $snake), $this->mode, $this->encoding);
    }

    public function toCamel(bool $capitalize_first = false): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $snake = (string) $this->toSnake([' ', '-', '_']);
        if ($snake === '') {
            return new self('', $this->mode, $this->encoding);
        }

        $parts = array_values(array_filter(
            explode('_', $snake),
            static fn (string $part): bool => $part !== ''
        ));

        if ($parts === []) {
            return new self('', $this->mode, $this->encoding);
        }

        $first = array_shift($parts);
        $result = $capitalize_first
            ? self::uppercaseFirst($first, $this->encoding)
            : $first;

        foreach ($parts as $part) {
            $result .= self::uppercaseFirst($part, $this->encoding);
        }

        return new self($result, $this->mode, $this->encoding);
    }

    public function toTitle(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        if (function_exists('mb_convert_case')) {
            $converted = mb_convert_case($this->value, MB_CASE_TITLE_SIMPLE, $this->encoding);
            $converted = preg_replace_callback(
                "/(?<=['’])\p{L}/u",
                function (array $match): string {
                    if (function_exists('mb_strtoupper')) {
                        return mb_strtoupper($match[0], $this->encoding);
                    }

                    return strtoupper($match[0]);
                },
                $converted
            ) ?? $converted;
        } else {
            $converted = ucwords(
                self::lowercaseString($this->value, $this->encoding),
                " \t\r\n\f\v-'_"
            );
        }

        return new self($converted, $this->mode, $this->encoding);
    }

    public function toPascal(): self
    {
        return $this->toCamel(true);
    }

    /**
     * @param Regex|array<int, Regex> $pattern
     * @return array<int|string, string>|null
     */
    public function match(Regex|array $pattern): ?array
    {
        $patterns = is_array($pattern) ? $pattern : [$pattern];

        if ($patterns === []) {
            throw new InvalidArgumentException('Pattern array cannot be empty.');
        }

        $all_matches = [];

        foreach ($patterns as $candidate) {
            if (!$candidate instanceof Regex) {
                throw new InvalidArgumentException('All patterns must be instances of Regex.');
            }

            $matches = [];
            set_error_handler(
                static function (int $errno, string $errstr): bool {
                    throw new ValueError($errstr);
                }
            );

            try {
                $result = preg_match_all((string) $candidate, $this->value, $matches, PREG_SET_ORDER);
            } finally {
                restore_error_handler();
            }

            if ($result > 0) {
                $all_matches = array_merge($all_matches, $matches);
            }
        }

        if ($all_matches === []) {
            return null;
        }

        return $all_matches;
    }

    public function trim(bool $newline = true, bool $space = true, bool $tab = true): self
    {
        return $this->trimInternal(true, true, $newline, $space, $tab);
    }

    public function ltrim(bool $newline = true, bool $space = true, bool $tab = true): self
    {
        return $this->trimInternal(true, false, $newline, $space, $tab);
    }

    public function rtrim(bool $newline = true, bool $space = true, bool $tab = true): self
    {
        return $this->trimInternal(false, true, $newline, $space, $tab);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    public function replace(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        HtmlTag|Newline|Regex|Stringable|string $replace,
        ?int $limit = null,
        bool $reversed = false
    ): self {
        if ($limit !== null && $limit < 0) {
            throw new InvalidArgumentException('Limit must be greater than or equal to 0.');
        }

        if ($limit === 0) {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $replacement = self::normalizeFragment($replace);
        $remaining = $limit ?? PHP_INT_MAX;
        $result = $this->value;

        $search_values = is_array($search) ? $search : [$search];

        foreach ($search_values as $search_value) {
            if ($remaining === 0) {
                break;
            }

            if ($search_value instanceof Newline) {
                $config = $search_value->getStartsWithConfig();
                if ($config !== null) {
                    $result = self::replaceLinesStartingWith(
                        $result,
                        $search_value,
                        $replacement,
                        $remaining,
                        $reversed
                    );

                    continue;
                }
            }

            if ($search_value instanceof Regex) {
                $result = self::replaceWithRegex(
                    $result,
                    (string) $search_value,
                    $replacement,
                    $remaining,
                    $reversed
                );

                continue;
            }

            if ($search_value instanceof HtmlTag) {
                $result = self::replaceHtmlTag(
                    $result,
                    $search_value,
                    $replacement,
                    $remaining,
                    $reversed
                );

                continue;
            }

            $normalized_search = self::normalizeFragment($search_value);
            if ($normalized_search === '') {
                throw new InvalidArgumentException('Search value cannot be empty.');
            }

            if ($reversed) {
                $result = self::replaceFromEnd($result, $normalized_search, $replacement, $remaining);
            } else {
                $result = self::replaceFromStart($result, $normalized_search, $replacement, $remaining);
            }
        }

        return new self($result, $this->mode, $this->encoding);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    public function replaceFirst(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        HtmlTag|Newline|Regex|Stringable|string $replace
    ): self {
        return $this->replace($search, $replace, 1);
    }

    public function replaceLast(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        HtmlTag|Newline|Regex|Stringable|string $replace
    ): self {
        return $this->replace($search, $replace, 1, true);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    public function strip(
        HtmlTag|Newline|Regex|Stringable|string|array $search,
        ?int $limit = null,
        bool $reversed = false
    ): self {
        return $this->replace($search, '', $limit, $reversed);
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $search
     */
    public function contains(HtmlTag|Newline|Regex|Stringable|string|array $search): bool
    {
        $candidates = is_array($search) ? $search : [$search];
        if ($candidates === []) {
            throw new InvalidArgumentException('Search candidates cannot be empty.');
        }

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                throw new InvalidArgumentException('Nested search arrays are not supported.');
            }

            if ($this->containsCandidate($candidate)) {
                return true;
            }
        }

        return false;
    }

    public function similarityScore(
        HtmlTag|Newline|Regex|Stringable|string|array $comparison,
        string $algorithm = 'github-style',
        array $options = []
    ): float {
        $normalized_algorithm = strtolower(trim($algorithm));
        if ($normalized_algorithm === '') {
            $normalized_algorithm = 'github-style';
        }

        $comparison_value = is_array($comparison)
            ? self::concatenateFragments($comparison)
            : self::normalizeFragment($comparison);

        return Similarity::compute(
            $this->value,
            $comparison_value,
            $normalized_algorithm,
            $options,
            $this->mode,
            $this->encoding
        );
    }

    public function toUpper(): self
    {
        $upper = function_exists('mb_strtoupper')
            ? mb_strtoupper($this->value, $this->encoding)
            : strtoupper($this->value);

        return new self($upper, $this->mode, $this->encoding);
    }

    public function toUpperCase(): self
    {
        return $this->toUpper();
    }

    public function toLower(): self
    {
        if (function_exists('mb_convert_case')) {
            $lower = mb_convert_case($this->value, MB_CASE_LOWER_SIMPLE, $this->encoding);
        } elseif (function_exists('mb_strtolower')) {
            $lower = mb_strtolower($this->value, $this->encoding);
        } else {
            $lower = strtolower($this->value);
        }

        return new self($lower, $this->mode, $this->encoding);
    }

    public function toLowerCase(): self
    {
        return $this->toLower();
    }

    public function ucfirst(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        if (function_exists('mb_substr')) {
            $first = mb_substr($this->value, 0, 1, $this->encoding);
            $rest = mb_substr($this->value, 1, null, $this->encoding);

            if ($first !== false && $rest !== false) {
                $upperFirst = function_exists('mb_strtoupper')
                    ? mb_strtoupper($first, $this->encoding)
                    : strtoupper($first);

                return new self($upperFirst . $rest, $this->mode, $this->encoding);
            }
        }

        $first = substr($this->value, 0, 1);
        $rest = substr($this->value, 1);

        return new self(strtoupper($first) . $rest, $this->mode, $this->encoding);
    }

    public function lcfirst(): self
    {
        if ($this->value === '') {
            return new self('', $this->mode, $this->encoding);
        }

        if (function_exists('mb_substr')) {
            $first = mb_substr($this->value, 0, 1, $this->encoding);
            $rest = mb_substr($this->value, 1, null, $this->encoding);

            if ($first !== false && $rest !== false) {
                $lowerFirst = function_exists('mb_strtolower')
                    ? mb_strtolower($first, $this->encoding)
                    : strtolower($first);

                return new self($lowerFirst . $rest, $this->mode, $this->encoding);
            }
        }

        $first = substr($this->value, 0, 1);
        $rest = substr($this->value, 1);

        return new self(strtolower($first) . $rest, $this->mode, $this->encoding);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $input_delimiter
     * @return array<int, string>
     */
    private function extractCaseWords(HtmlTag|Newline|Regex|Stringable|string|array $input_delimiter): array
    {
        $delimiters = is_array($input_delimiter) ? $input_delimiter : [$input_delimiter];
        if ($delimiters === []) {
            throw new InvalidArgumentException('Input delimiter cannot be empty.');
        }

        $normalized_delimiters = [];
        foreach ($delimiters as $delimiter) {
            $fragment = self::normalizeFragment($delimiter);
            if ($fragment === '') {
                throw new InvalidArgumentException('Input delimiters cannot be empty.');
            }

            $normalized_delimiters[$fragment] = true;
        }

        $normalized_delimiters = array_keys($normalized_delimiters);

        $working = $this->value;
        $patterns = [
            '/(?<=\p{Ll})(?=\p{Lu})/u',
            '/(?<=\p{L})(?=\p{Nd})/u',
            '/(?<=\p{Nd})(?=\p{L})/u',
            '/(?<=\p{Lu})(?=\p{Lu}\p{Ll})/u',
        ];

        foreach ($patterns as $pattern) {
            $next = preg_replace($pattern, ' ', $working);
            if ($next !== null) {
                $working = $next;
            }
        }

        if ($normalized_delimiters !== []) {
            $escaped_parts = array_map(
                static fn (string $value): string => preg_quote($value, '/'),
                $normalized_delimiters
            );
            $delimiter_pattern = '/(?:' . implode('|', $escaped_parts) . ')+/u';
            $next = preg_replace($delimiter_pattern, ' ', $working);
            if ($next !== null) {
                $working = $next;
            }
        }

        $segments = preg_split('/[\s_]+/u', $working);
        if ($segments === false) {
            $segments = preg_split('/_+/', $working);
        }

        $words = [];
        foreach ($segments as $segment) {
            if ($segment === '' || $segment === null) {
                continue;
            }

            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            $words[] = self::lowercaseString($segment, $this->encoding);
        }

        return $words;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string|array<int, HtmlTag|Newline|Regex|Stringable|string> $value
     * @return list<string>
     */
    /**
     * @return list<list<string>>
     */
    private static function normalizeSearchOptions(
        HtmlTag|Newline|Regex|Stringable|string|array $value,
        string $behavior
    ): array
    {
        $normalized_behavior = self::normalizeSearchBehavior($behavior);

        if ($normalized_behavior === 'or') {
            return self::normalizeSearchOptionsOrBehavior($value);
        }

        return self::normalizeSearchOptionsSequentialBehavior($value);
    }

    private static function normalizeSearchBehavior(string $behavior): string
    {
        $normalized = strtolower($behavior);
        if (!in_array($normalized, ['sequential', 'or'], true)) {
            throw new InvalidArgumentException('Search behavior must be either "sequential" or "or".');
        }

        return $normalized;
    }

    /**
     * @return list<list<string>>
     */
    private static function normalizeSearchOptionsOrBehavior(HtmlTag|Newline|Regex|Stringable|string|array $value): array
    {
        $items = is_array($value) ? $value : [$value];
        if ($items === []) {
            throw new InvalidArgumentException('Search sequence cannot be empty.');
        }

        $options = [];
        foreach ($items as $item) {
            $sequence_items = is_array($item) ? $item : [$item];
            if ($sequence_items === []) {
                throw new InvalidArgumentException('Search sequence cannot be empty.');
            }

            $sequence = [];
            foreach ($sequence_items as $fragment_value) {
                $fragment = self::normalizeSearchFragment($fragment_value);
                if ($fragment === '') {
                    throw new InvalidArgumentException('Search sequence values cannot be empty.');
                }

                $sequence[] = $fragment;
            }

            $options[] = $sequence;
        }

        return $options;
    }

    /**
     * @return list<list<string>>
     */
    private static function normalizeSearchOptionsSequentialBehavior(
        HtmlTag|Newline|Regex|Stringable|string|array $value
    ): array {
        if (!is_array($value)) {
            $fragment = self::normalizeSearchFragment($value);
            if ($fragment === '') {
                throw new InvalidArgumentException('Search sequence values cannot be empty.');
            }

            return [[$fragment]];
        }

        if ($value === []) {
            throw new InvalidArgumentException('Search sequence cannot be empty.');
        }

        $has_nested_arrays = false;
        foreach ($value as $item) {
            if (is_array($item)) {
                $has_nested_arrays = true;
                break;
            }
        }

        if (!$has_nested_arrays) {
            $sequence = [];
            foreach ($value as $fragment_value) {
                $fragment = self::normalizeSearchFragment($fragment_value);
                if ($fragment === '') {
                    throw new InvalidArgumentException('Search sequence values cannot be empty.');
                }

                $sequence[] = $fragment;
            }

            return [$sequence];
        }

        return self::normalizeSearchOptionsOrBehavior($value);
    }

    private static function normalizeSearchFragment(mixed $value): string
    {
        if ($value instanceof Newline) {
            return self::canonicalizeLineBreak((string) $value);
        }

        return self::normalizeFragment($value);
    }

    private static function canonicalizeLineBreak(string $line_break): string
    {
        if ($line_break === '') {
            return '';
        }

        return str_contains($line_break, "\n") ? "\n" : $line_break;
    }

    /**
     * @param list<list<string>> $options
     * @return array{start: int, end: int}|null
     */
    private static function findSequence(string $subject, array $options, int $offset): ?array
    {
        $best_match = null;

        foreach ($options as $sequence) {
            $current_offset = max(0, $offset);
            $start = null;

            foreach ($sequence as $fragment) {
                $position = strpos($subject, $fragment, $current_offset);
                if ($position === false) {
                    continue 2;
                }

                if ($start === null) {
                    $start = $position;
                }

                $current_offset = $position + strlen($fragment);
            }

            if ($start === null) {
                continue;
            }

            $match = [
                'start' => $start,
                'end' => $current_offset,
            ];

            if (
                $best_match === null
                || $match['start'] < $best_match['start']
                || ($match['start'] === $best_match['start'] && $match['end'] > $best_match['end'])
            ) {
                $best_match = $match;
            }
        }

        return $best_match;
    }

    /**
     * @param list<list<string>> $options
     * @return list<array{start: int, end: int}>
     */
    private static function findAllSequences(string $subject, array $options): array
    {
        $occurrences = [];
        $offset = 0;

        while (true) {
            $match = self::findSequence($subject, $options, $offset);
            if ($match === null) {
                break;
            }

            $occurrences[] = $match;
            $next_offset = $match['start'] + 1;
            if ($next_offset <= $offset) {
                $next_offset = $offset + 1;
            }

            $offset = $next_offset;
        }

        return $occurrences;
    }

    /**
     * @param list<list<string>> $options
     * @return array{start: int, end: int}|null
     */
    private static function findSequenceWithSkip(string $subject, array $options, int $offset, int $skip): ?array
    {
        $current_offset = max(0, $offset);
        $remaining = $skip;

        while (true) {
            $match = self::findSequence($subject, $options, $current_offset);
            if ($match === null) {
                return null;
            }

            if ($remaining === 0) {
                return $match;
            }

            $remaining--;
            $advance = max(1, $match['end'] - $match['start']);
            $current_offset = $match['start'] + $advance;
        }
    }

    private function collapseWhitespaceToReplacement(string $replacement): self
    {
        $normalized = $this->collapseWhitespace(space: true, tab: true, newline: true);
        $string = (string) $normalized;

        $search = ["\r\n", "\r", "\n", "\t", ' '];
        $converted = str_replace($search, $replacement, $string);

        $repeat_pattern = '/' . preg_quote($replacement, '/') . '+/u';
        $collapsed = preg_replace($repeat_pattern, $replacement, $converted);
        if ($collapsed !== null) {
            $converted = $collapsed;
        }

        return new self($converted, $this->mode, $this->encoding);
    }

    private static function fragmentEndsWithAbbreviation(string $fragment): bool
    {
        $trimmed = trim($fragment);
        if ($trimmed === '') {
            return false;
        }

        $trimmed = preg_replace('/["\'\)\]\}»”’]+$/u', '', $trimmed) ?? $trimmed;
        $parts = preg_split('/\s+/u', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts === false || $parts === []) {
            $lastWord = $trimmed;
        } else {
            $last = end($parts);
            $lastWord = is_string($last) ? $last : $trimmed;
        }

        $normalized = strtolower($lastWord);
        $abbreviations = [
            'mr.', 'mrs.', 'ms.', 'dr.', 'prof.', 'sr.', 'jr.', 'vs.', 'etc.', 'e.g.', 'i.e.', 'no.', 'fig.', 'st.', 'rd.',
            'th.', 'jan.', 'feb.', 'mar.', 'apr.', 'jun.', 'jul.', 'aug.', 'sep.', 'sept.', 'oct.', 'nov.', 'dec.',
        ];

        if (in_array($normalized, $abbreviations, true)) {
            return true;
        }

        return preg_match('/^\p{Lu}\.$/u', $lastWord) === 1;
    }

    /**
     * @param HtmlTag|Newline|Regex|Stringable|string $candidate
     */
    private function containsCandidate(HtmlTag|Newline|Regex|Stringable|string $candidate): bool
    {
        if ($candidate instanceof HtmlTag) {
            return self::findNextHtmlTagMatch($this->value, $candidate, 0) !== null;
        }

        if ($candidate instanceof Regex) {
            $pattern = (string) $candidate;
            $subject = $this->value;

            $result = self::withRegexErrorHandling(
                static function () use ($pattern, $subject) {
                    return preg_match($pattern, $subject);
                }
            );

            return is_int($result) && $result > 0;
        }

        if ($candidate instanceof Newline) {
            $fragment = (string) $candidate;
            $canonical = self::canonicalizeLineBreak($fragment);

            if ($fragment === '' && $canonical === '') {
                throw new InvalidArgumentException('Search value cannot be empty.');
            }

            if ($fragment !== '' && str_contains($this->value, $fragment)) {
                return true;
            }

            return $canonical !== '' && str_contains($this->value, $canonical);
        }

        $fragment = self::normalizeFragment($candidate);
        if ($fragment === '') {
            throw new InvalidArgumentException('Search value cannot be empty.');
        }

        return str_contains($this->value, $fragment);
    }

    private static function normalizeFragment(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        if ($value === null) {
            return '';
        }

        throw new InvalidArgumentException('Only strings and stringable values are supported.');
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string|null> $data
     */
    private static function concatenateFragments(array $data): string
    {
        $fragments = [];
        foreach ($data as $fragment) {
            $fragments[] = self::normalizeFragment($fragment);
        }

        return implode('', $fragments);
    }

    private static function removeIndentationPrefix(string $prefix, int $spaces, int $tabs): string
    {
        if ($prefix === '') {
            return '';
        }

        $remaining_spaces = $spaces;
        $remaining_tabs = $tabs;
        $length = strlen($prefix);
        $index = 0;

        while ($index < $length) {
            $char = $prefix[$index];

            if ($char === ' ' && $remaining_spaces > 0) {
                $remaining_spaces--;
                $index++;
                continue;
            }

            if ($char === "\t" && $remaining_tabs > 0) {
                $remaining_tabs--;
                $index++;
                continue;
            }

            break;
        }

        if ($index >= $length) {
            return '';
        }

        return substr($prefix, $index);
    }

    private static function buildPadding(string $pad_fragment, int $units, string $mode, string $encoding): string
    {
        if ($units <= 0) {
            return '';
        }

        $units_pool = self::splitByMode($pad_fragment, $mode, $encoding);
        if ($units_pool === []) {
            return '';
        }

        $pool_count = count($units_pool);
        $result = '';

        $full_repeats = intdiv($units, $pool_count);
        if ($full_repeats > 0) {
            $result .= str_repeat($pad_fragment, $full_repeats);
        }

        $remainder = $units % $pool_count;
        if ($remainder > 0) {
            $result .= implode('', array_slice($units_pool, 0, $remainder));
        }

        return $result;
    }

    private function trimInternal(bool $trim_left, bool $trim_right, bool $newline, bool $space, bool $tab): self
    {
        $mask = self::buildTrimMask($newline, $space, $tab);
        if ($mask === '') {
            return new self($this->value, $this->mode, $this->encoding);
        }

        $result = $this->value;
        if ($trim_left && $trim_right) {
            $result = trim($result, $mask);
        } elseif ($trim_left) {
            $result = ltrim($result, $mask);
        } elseif ($trim_right) {
            $result = rtrim($result, $mask);
        }

        return new self($result, $this->mode, $this->encoding);
    }

    private static function buildTrimMask(bool $newline, bool $space, bool $tab): string
    {
        $mask = '';

        if ($space) {
            $mask .= ' ';
        }

        if ($tab) {
            $mask .= "\t";
        }

        if ($newline) {
            $mask .= "\r\n";
        }

        return $mask;
    }

    private static function replaceFromStart(string $subject, string $search, string $replace, int &$remaining): string
    {
        $search_length = strlen($search);
        $offset = 0;

        while ($remaining > 0) {
            $position = strpos($subject, $search, $offset);
            if ($position === false) {
                break;
            }

            $subject = substr($subject, 0, $position)
                . $replace
                . substr($subject, $position + $search_length);

            $offset = $position + strlen($replace);
            $remaining--;
        }

        return $subject;
    }

    private static function replaceFromEnd(string $subject, string $search, string $replace, int &$remaining): string
    {
        $search_length = strlen($search);

        while ($remaining > 0) {
            $position = strrpos($subject, $search);
            if ($position === false) {
                break;
            }

            $subject = substr($subject, 0, $position)
                . $replace
                . substr($subject, $position + $search_length);

            $remaining--;
        }

        return $subject;
    }

    private static function replaceWithRegex(
        string $subject,
        string $pattern,
        string $replacement,
        int &$remaining,
        bool $reversed
    ): string {
        if ($remaining <= 0) {
            return $subject;
        }

        if ($reversed) {
            return self::replaceRegexFromEnd($subject, $pattern, $replacement, $remaining);
        }

        return self::replaceRegexFromStart($subject, $pattern, $replacement, $remaining);
    }

    private static function replaceRegexFromStart(string $subject, string $pattern, string $replace, int &$remaining): string
    {
        if ($remaining <= 0) {
            return $subject;
        }

        $unlimited = $remaining === PHP_INT_MAX;
        $limit = $unlimited ? -1 : $remaining;
        $count = 0;

        $result = self::withRegexErrorHandling(
            static function () use ($pattern, $replace, $subject, $limit, &$count) {
                return preg_replace($pattern, $replace, $subject, $limit, $count);
            }
        );

        if (!is_string($result)) {
            return $subject;
        }

        if (!$unlimited) {
            $remaining = max(0, $remaining - $count);
        }

        return $result;
    }

    private static function replaceRegexFromEnd(string $subject, string $pattern, string $replace, int &$remaining): string
    {
        if ($remaining <= 0) {
            return $subject;
        }

        $unlimited = $remaining === PHP_INT_MAX;
        $matches = [];

        $matchCount = self::withRegexErrorHandling(
            static function () use ($pattern, $subject, &$matches) {
                return preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
            }
        );

        if (!is_int($matchCount) || $matchCount === 0) {
            return $subject;
        }

        $fullMatches = $matches[0] ?? [];
        if (!is_array($fullMatches) || $fullMatches === []) {
            return $subject;
        }

        for ($index = count($fullMatches) - 1; $index >= 0; $index--) {
            if (!$unlimited && $remaining === 0) {
                break;
            }

            $entry = $fullMatches[$index];
            if (!is_array($entry) || count($entry) < 2) {
                continue;
            }

            [$matchedText, $offset] = $entry;
            if (!is_string($matchedText) || !is_int($offset)) {
                continue;
            }

            $replacementText = self::withRegexErrorHandling(
                static function () use ($pattern, $replace, $matchedText) {
                    return preg_replace($pattern, $replace, $matchedText, 1);
                }
            );

            if (!is_string($replacementText)) {
                continue;
            }

            $subject = substr($subject, 0, $offset)
                . $replacementText
                . substr($subject, $offset + strlen($matchedText));

            if (!$unlimited) {
                $remaining--;
            }
        }

        return $subject;
    }

    /**
     * @return mixed
     */
    private static function withRegexErrorHandling(callable $operation)
    {
        set_error_handler(
            static function (int $errno, string $errstr): bool {
                throw new ValueError($errstr);
            }
        );

        try {
            return $operation();
        } finally {
            restore_error_handler();
        }
    }

    private static function replaceLinesStartingWith(
        string $subject,
        Newline $newline,
        string $replacement,
        int &$remaining,
        bool $reversed
    ): string {
        if ($remaining <= 0) {
            return $subject;
        }

        $config = $newline->getStartsWithConfig();
        if ($config === null) {
            return $subject;
        }

        $line_break = (string) $newline;
        if ($line_break === '') {
            return $subject;
        }

        $prefix = $config['prefix'];
        $trim = $config['trim'];

        $split_break = $line_break;
        if (!str_contains($subject, $split_break)) {
            $canonical_break = self::canonicalizeLineBreak($line_break);
            if ($canonical_break !== '' && str_contains($subject, $canonical_break)) {
                $split_break = $canonical_break;
            }
        }

        $segments = explode($split_break, $subject);
        $has_trailing_break = ($split_break !== '' && str_ends_with($subject, $split_break))
            || ($line_break !== $split_break && str_ends_with($subject, $line_break));
        if ($has_trailing_break) {
            array_pop($segments);
        }

        $indexes = $reversed
            ? array_reverse(array_keys($segments))
            : array_keys($segments);

        foreach ($indexes as $index) {
            if ($remaining === 0) {
                break;
            }

            $line = $segments[$index];
            $comparison = $trim ? ltrim($line, " \t") : $line;

            if (!str_starts_with($comparison, $prefix)) {
                continue;
            }

            $segments[$index] = $replacement;
            $remaining--;
        }

        $result = implode($line_break, $segments);

        if ($has_trailing_break) {
            $result .= $line_break;
        }

        return $result;
    }

    private static function replaceHtmlTag(
        string $subject,
        HtmlTag $tag,
        string $replacement,
        int &$remaining,
        bool $reversed
    ): string {
        if ($remaining <= 0) {
            return $subject;
        }

        if ($reversed) {
            $matches = self::findAllHtmlTagMatches($subject, $tag);
            for ($index = count($matches) - 1; $index >= 0 && $remaining > 0; $index--) {
                $match = $matches[$index];
                $subject = substr($subject, 0, $match['offset'])
                    . $replacement
                    . substr($subject, $match['offset'] + $match['length']);
                $remaining--;
            }

            return $subject;
        }

        $offset = 0;
        while ($remaining > 0) {
            $match = self::findNextHtmlTagMatch($subject, $tag, $offset);
            if ($match === null) {
                break;
            }

            $subject = substr($subject, 0, $match['offset'])
                . $replacement
                . substr($subject, $match['offset'] + $match['length']);

            $offset = $match['offset'] + strlen($replacement);
            $remaining--;
        }

        return $subject;
    }

    /**
     * @return array<int, array{offset: int, length: int}>
     */
    private static function findAllHtmlTagMatches(string $subject, HtmlTag $tag): array
    {
        $matches = [];
        $offset = 0;

        while (true) {
            $match = self::findNextHtmlTagMatch($subject, $tag, $offset);
            if ($match === null) {
                break;
            }

            $matches[] = $match;
            $offset = $match['offset'] + $match['length'];
        }

        return $matches;
    }

    /**
     * @return array{offset: int, length: int}|null
     */
    private static function findNextHtmlTagMatch(string $subject, HtmlTag $tag, int $offset): ?array
    {
        $pattern = self::buildHtmlTagPattern($tag);
        $flags = $tag->isCaseSensitive() ? 'u' : 'iu';
        $regex = sprintf('/%s/%s', $pattern, $flags);

        $cursor = max(0, $offset);
        $length = strlen($subject);

        while ($cursor <= $length) {
            if (preg_match($regex, $subject, $matches, PREG_OFFSET_CAPTURE, $cursor) !== 1) {
                return null;
            }

            $match_string = $matches[0][0];
            $match_offset = $matches[0][1];

            if (!$tag->matchesFragment($match_string)) {
                $cursor = $match_offset + max(1, strlen($match_string));
                continue;
            }

            return [
                'offset' => $match_offset,
                'length' => strlen($match_string),
            ];
        }

        return null;
    }

    /**
     * @param array<int, HtmlTag|Newline|Regex|Stringable|string> $delimiters
     * @return array{start: int, end: int}|null
     */
    private static function findNextDelimiterMatch(string $subject, array $delimiters, int $offset): ?array
    {
        $best_match = null;

        foreach ($delimiters as $delimiter) {
            if ($delimiter instanceof HtmlTag) {
                $match = self::findNextHtmlTagMatch($subject, $delimiter, $offset);
                if ($match === null) {
                    continue;
                }

                $candidate = [
                    'start' => $match['offset'],
                    'end' => $match['offset'] + $match['length'],
                ];
            } elseif ($delimiter instanceof Regex) {
                $candidate = self::findNextRegexDelimiter($subject, (string) $delimiter, $offset);
                if ($candidate === null) {
                    continue;
                }
            } else {
                $fragment = $delimiter instanceof Newline
                    ? (string) $delimiter
                    : self::normalizeFragment($delimiter);

                if ($fragment === '') {
                    throw new InvalidArgumentException('Delimiter cannot be empty.');
                }

                $position = strpos($subject, $fragment, max(0, $offset));
                if ($position === false && $delimiter instanceof Newline) {
                    $canonical = self::canonicalizeLineBreak($fragment);
                    if ($canonical !== '' && $canonical !== $fragment) {
                        $position = strpos($subject, $canonical, max(0, $offset));
                        if ($position !== false) {
                            $fragment = $canonical;
                        }
                    }
                }

                if ($position === false) {
                    continue;
                }

                $candidate = [
                    'start' => $position,
                    'end' => $position + strlen($fragment),
                ];
            }

            if ($best_match === null
                || $candidate['start'] < $best_match['start']
                || ($candidate['start'] === $best_match['start'] && $candidate['end'] > $best_match['end'])
            ) {
                $best_match = $candidate;
            }
        }

        return $best_match;
    }

    /**
     * @return array{start: int, end: int}|null
     */
    private static function findNextRegexDelimiter(string $subject, string $pattern, int $offset): ?array
    {
        $matches = [];
        $result = self::withRegexErrorHandling(
            static function () use ($pattern, $subject, $offset, &$matches) {
                return preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE, max(0, $offset));
            }
        );

        if ($result !== 1) {
            return null;
        }

        $match_string = $matches[0][0];
        $match_offset = $matches[0][1];

        if ($match_string === '') {
            throw new InvalidArgumentException('Regex delimiter cannot match an empty string.');
        }

        return [
            'start' => $match_offset,
            'end' => $match_offset + strlen($match_string),
        ];
    }

    private static function buildHtmlTagPattern(HtmlTag $tag): string
    {
        $tag_name = preg_quote($tag->getRawTagName(), '/');

        if ($tag->isClosingTag()) {
            return sprintf('<\/%s\s*>', $tag_name);
        }

        return sprintf('<\s*%s\b[^>]*\/?\s*>', $tag_name);
    }

    /**
     * @return array<int, string>
     */
    private static function splitCharacters(string $characters): array
    {
        if ($characters === '') {
            return [];
        }

        if (preg_match('//u', $characters) === 1) {
            $parts = preg_split('//u', $characters, -1, PREG_SPLIT_NO_EMPTY);
            if ($parts !== false) {
                return $parts;
            }
        }

        return str_split($characters);
    }

    /**
     * @return list<string>
     */
    private static function splitByMode(string $value, string $mode, string $encoding): array
    {
        if ($value === '') {
            return [];
        }

        return match ($mode) {
            'bytes' => str_split($value),
            'codepoints' => self::splitCodepoints($value),
            default => self::splitGraphemes($value, $encoding),
        };
    }

    /**
     * @return list<string>
     */
    private static function splitCodepoints(string $value): array
    {
        if ($value === '') {
            return [];
        }

        if (preg_match('//u', $value) === 1) {
            $parts = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
            if ($parts !== false) {
                return $parts;
            }
        }

        return str_split($value);
    }

    /**
     * @return list<string>
     */
    private static function splitGraphemes(string $value, string $encoding): array
    {
        if ($value === '') {
            return [];
        }

        if (function_exists('grapheme_extract')) {
            $units = [];
            $offset = 0;
            $length = strlen($value);

            while ($offset < $length) {
                $cluster = grapheme_extract($value, 1, GRAPHEME_EXTR_COUNT, $offset, $next);
                if ($cluster === false || $cluster === '') {
                    $next = $offset + 1;
                    $cluster = substr($value, $offset, $next - $offset);
                }

                $units[] = $cluster;
                $offset = $next;
            }

            return $units;
        }

        $clusters = preg_split('/\\X/u', $value, -1, PREG_SPLIT_NO_EMPTY);
        if ($clusters !== false && $clusters !== []) {
            return $clusters;
        }

        $manualClusters = self::splitGraphemesManually($value);
        if ($manualClusters !== []) {
            return $manualClusters;
        }

        if (function_exists('mb_strlen')) {
            $units = [];
            $length = mb_strlen($value, $encoding);
            if ($length !== false) {
                for ($i = 0; $i < $length; $i++) {
                    $units[] = (string) mb_substr($value, $i, 1, $encoding);
                }

                return $units;
            }
        }

        return str_split($value);
    }

    /**
     * @return list<string>
     */
    private static function splitGraphemesManually(string $value): array
    {
        if ($value === '') {
            return [];
        }

        $codepoints = self::splitCodepoints($value);
        if ($codepoints === []) {
            return [];
        }

        $clusters = [];
        $joinNext = false;
        $regionalIndicatorRun = 0;

        foreach ($codepoints as $codepoint) {
            if (self::isRegionalIndicator($codepoint)) {
                if ($regionalIndicatorRun % 2 === 0) {
                    $clusters[] = $codepoint;
                } else {
                    $clusters[count($clusters) - 1] .= $codepoint;
                }

                $regionalIndicatorRun++;
                $joinNext = false;

                continue;
            }

            $regionalIndicatorRun = 0;

            if ($joinNext) {
                $clusters[count($clusters) - 1] .= $codepoint;
                $joinNext = self::isZeroWidthJoiner($codepoint);

                continue;
            }

            if ($clusters !== [] && self::shouldExtendCluster($codepoint)) {
                $clusters[count($clusters) - 1] .= $codepoint;

                continue;
            }

            if ($clusters !== [] && self::isZeroWidthJoiner($codepoint)) {
                $clusters[count($clusters) - 1] .= $codepoint;
                $joinNext = true;

                continue;
            }

            $clusters[] = $codepoint;
            $joinNext = self::isZeroWidthJoiner($codepoint);
        }

        return $clusters;
    }

    private static function shouldExtendCluster(string $codepoint): bool
    {
        return self::isCombiningMark($codepoint)
            || self::isEmojiModifier($codepoint)
            || self::isVariationSelector($codepoint);
    }

    private static function isCombiningMark(string $codepoint): bool
    {
        return preg_match('/^\p{M}$/u', $codepoint) === 1;
    }

    private static function isEmojiModifier(string $codepoint): bool
    {
        return preg_match('/^[\x{1F3FB}-\x{1F3FF}]$/u', $codepoint) === 1;
    }

    private static function isVariationSelector(string $codepoint): bool
    {
        return preg_match('/^[\x{FE00}-\x{FE0F}\x{E0100}-\x{E01EF}]$/u', $codepoint) === 1;
    }

    private static function isZeroWidthJoiner(string $codepoint): bool
    {
        return $codepoint === "\u{200D}";
    }

    private static function isRegionalIndicator(string $codepoint): bool
    {
        return preg_match('/^[\x{1F1E6}-\x{1F1FF}]$/u', $codepoint) === 1;
    }

    private static function toAsciiForSlug(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        $originalValue = $value;

        if (class_exists('\\Transliterator')) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove; NFC');
            if ($transliterator !== null) {
                $transliterated = $transliterator->transliterate($value);
                if ($transliterated !== null) {
                    $value = $transliterated;
                }
            }
        }

        if (function_exists('iconv')) {
            $converted = @iconv($encoding, 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        if (self::shouldApplyAsciiFallback($value, $originalValue)) {
            $fallback = self::asciiTransliterationFallback($originalValue, $encoding);
            if ($fallback !== '') {
                $value = $fallback;
            }
        }

        return $value;
    }

    private static function shouldApplyAsciiFallback(string $value, string $original): bool
    {
        if ($value === '') {
            return false;
        }

        if (preg_match('/[^\x00-\x7F]/', $value) === 1) {
            return true;
        }

        foreach (["'", '"', '`', '^', '~', '?'] as $marker) {
            if (substr_count($value, $marker) > substr_count($original, $marker)) {
                return true;
            }
        }

        return false;
    }

    private static function asciiTransliterationFallback(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        $entities = htmlentities($value, ENT_NOQUOTES, $encoding);
        if ($entities === false) {
            return $value;
        }

        if ($entities === '') {
            return '';
        }

        $entities = preg_replace(
            '/&([a-zA-Z]+?)(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);/u',
            '$1',
            $entities,
        );
        if ($entities === null) {
            return $value;
        }

        $entities = preg_replace('/&[^;]+;/', '', $entities);
        if ($entities === null) {
            return $value;
        }

        $decoded = html_entity_decode($entities, ENT_NOQUOTES, 'UTF-8');
        if ($decoded === '') {
            return $value;
        }

        return $decoded;
    }

    private static function uppercaseFirst(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        if (function_exists('mb_substr')) {
            $first = mb_substr($value, 0, 1, $encoding);
            $rest = mb_substr($value, 1, null, $encoding);

            if ($first !== false && $rest !== false) {
                $upper = function_exists('mb_strtoupper')
                    ? mb_strtoupper($first, $encoding)
                    : strtoupper($first);

                return $upper . $rest;
            }
        }

        $first = substr($value, 0, 1);
        $rest = substr($value, 1);

        return strtoupper($first) . $rest;
    }

    private static function lowercaseString(string $value, string $encoding): string
    {
        if ($value === '') {
            return '';
        }

        if (function_exists('mb_strtolower')) {
            return mb_strtolower($value, $encoding);
        }

        return strtolower($value);
    }

    private static function normalizeMode(string $mode): string
    {
        $normalized = strtolower($mode);
        if (!in_array($normalized, self::VALID_MODES, true)) {
            throw new InvalidArgumentException('Invalid mode provided.');
        }

        return $normalized;
    }

    private static function normalizeEncoding(string $encoding): string
    {
        $normalized = trim($encoding);
        if ($normalized === '') {
            throw new InvalidArgumentException('Encoding cannot be empty.');
        }

        return $normalized;
    }

    private function graphemeLengthOrFallback(): int
    {
        if (function_exists('grapheme_strlen')) {
            $length = grapheme_strlen($this->value);
            if ($length !== false) {
                return $length;
            }
        }

        $grapheme_count = preg_match_all('/\\X/u', $this->value);
        if ($grapheme_count !== false) {
            return $grapheme_count;
        }

        if (function_exists('mb_strlen')) {
            return mb_strlen($this->value, $this->encoding);
        }

        return strlen($this->value);
    }

    private static function generateUuid(int $version, ?string $namespace, ?string $name): string
    {
        return match ($version) {
            1 => self::uuidV1(),
            3 => self::uuidFromHash(self::namespaceAndNameHash($namespace, $name, 3), 3),
            4 => self::uuidFromRandomBytes(random_bytes(16), 4),
            5 => self::uuidFromHash(self::namespaceAndNameHash($namespace, $name, 5), 5),
            default => throw new InvalidArgumentException('Unsupported UUID version. Use 1, 3, 4, or 5.'),
        };
    }

    private static function uuidV1(): string
    {
        $timestamp = (int) floor(microtime(true) * 10000000) + 0x01B21DD213814000;

        $timeLow = $timestamp & 0xffffffff;
        $timeMid = ($timestamp >> 32) & 0xffff;
        $timeHi = ($timestamp >> 48) & 0x0fff;
        $timeHi |= 0x1000;

        $clockSeq = random_int(0, 0x3fff);
        $clockSeqHi = (($clockSeq >> 8) & 0x3f) | 0x80;
        $clockSeqLow = $clockSeq & 0xff;

        $node = random_bytes(6);
        $node[0] = chr(ord($node[0]) | 0x01);

        return sprintf(
            '%08x-%04x-%04x-%02x%02x-%s',
            $timeLow,
            $timeMid,
            $timeHi,
            $clockSeqHi,
            $clockSeqLow,
            bin2hex($node)
        );
    }

    private static function namespaceAndNameHash(?string $namespace, ?string $name, int $version): string
    {
        if ($namespace === null || $namespace === '') {
            throw new InvalidArgumentException('Namespace UUID is required for this UUID version.');
        }

        if ($name === null || $name === '') {
            throw new InvalidArgumentException('Name is required for this UUID version.');
        }

        $namespaceBytes = self::uuidToBytes($namespace);

        return match ($version) {
            3 => md5($namespaceBytes . $name, true),
            5 => substr(sha1($namespaceBytes . $name, true), 0, 16),
            default => throw new InvalidArgumentException('Invalid UUID hashing version.'),
        };
    }

    private static function uuidToBytes(string $uuid): string
    {
        $normalized = strtolower(str_replace(['{', '}', '-'], '', $uuid));

        if (!preg_match('/^[0-9a-f]{32}$/', $normalized)) {
            throw new InvalidArgumentException('Namespace must be a valid UUID string.');
        }

        $bytes = hex2bin($normalized);
        if ($bytes === false) {
            throw new InvalidArgumentException('Namespace must be a valid UUID string.');
        }

        return $bytes;
    }

    private static function uuidFromHash(string $hash, int $version): string
    {
        return self::formatUuid(self::applyVersionAndVariant($hash, $version));
    }

    private static function uuidFromRandomBytes(string $bytes, int $version): string
    {
        return self::formatUuid(self::applyVersionAndVariant($bytes, $version));
    }

    private static function applyVersionAndVariant(string $bytes, int $version): string
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('UUID generation requires 16 bytes of data.');
        }

        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | ($version << 4));
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return $bytes;
    }

    private static function formatUuid(string $bytes): string
    {
        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }
}
