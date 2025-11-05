# XString

XString is an immutable, fluent string manipulation toolkit for PHP. It exposes a
single value object that keeps track of the underlying string, the logical
length "mode" (bytes, codepoints, or graphemes), and the active encoding. Every
operation returns a new instance so that strings can be safely composed without
side effects.

## TODO

- [ ] remove Regex where it doesn't make sense
- [ ] add Stringable where it makes sense
- [x] html entity encode and decode methods:
  - [x] encodeHtmlEntities()
  - [x] decodeHtmlEntities()
- [x] toInt(), toFloat(), toBool() methods, tobool translates 'true', 'yes', 'ok', ... and '1', '2', ... to true . And 'false', 'no', 'failed', ... and '0', '-1', ... to false.
- [x] methods to safely make folfder/file names for various filesystems (windows, linux, macos):
  - [x] toWindowsFileName() // Also escapes reserved names like CON, PRN, AUX, NUL, COM1, LPT1, etc. and . and ..
  - [x] toWindowsFolderName()
  - [x] toWindowsPath()
  - [x] toUnixFileName()
  - [x] toUnixFolderName()
  - [x] toUnixPath()
  - [x] toMacOSFileName()
  - [x] toMacOSFolderName()
  - [x] toMacOSPath()
  - [x] toSafeFileName() // generic safe file name
  - [x] toSafeFolderName() // generic safe folder name
  - [x] toSafePath() // generic safe path

## Requirements

- PHP 8.1 or higher.
- The library works without extensions, but it takes advantage of the following
  when they are available:
  - `ext-mbstring` for multibyte-safe string operations.
  - `ext-intl` (Normalizer + grapheme functions) for Unicode normalization.
  - `ext-iconv` as an additional encoding conversion fallback.
  - `ext-openssl` for AES-256-GCM encryption/decryption helpers.

## Installation

```bash
composer require orryv/x-string
```

XString ships as a regular Composer library. All classes live in the `Orryv` or
`Orryv\\XString` namespaces and are autoloaded through PSR-4.

## Quick start

```php
use Orryv\\XString;

$xstring = XString::new('  Grüß Gott!  ')
    ->trim()
    ->normalize()
    ->toAscii()
    ->toUpper();

echo (string) $xstring; // outputs "GRUSS GOTT!"
```

Instance methods can also be invoked statically by providing the initial value as
the first argument. This is useful when you only need a single operation:

```php
echo (string) XString::repeat('#', 3); // outputs "###"
```

The fluent API covers common text tasks: casing, normalization, substring
operations, tokenisation, searching, replacing, cryptographic helpers,
formatting, and more. Each operation honours the configured mode so you can work
with multibyte characters and grapheme clusters reliably.

## Documentation

Comprehensive documentation for every public method lives in the `docs/`
folder. Each markdown file describes behaviour, edge cases, and contains tested
examples (prefixed with `<!-- test:... -->`) that are automatically converted
into PHPUnit tests. Start with `docs/examples/basic.md` for an overview and use
the per-method pages under `docs/x-string/` for reference material.

Helper types such as `Newline`, `Regex`, and `HtmlTag` are documented in their
respective subdirectories. The `XStringType` factory shortcuts mirror the
"type" helpers described throughout the docs.

## Testing

Run the full test suite (documentation examples + PHPUnit suites) with:

```bash
composer test
```

If you add or modify documentation examples, regenerate the corresponding tests
with `composer compose-docs-tests` before running PHPUnit.

## Contributing

Issues and pull requests are welcome. Please keep the API immutable, document
new features in the `docs/` folder, and include representative examples so that
new behaviour is exercised by the generated tests.

## License

The library is open-source software licensed under the [MIT license](LICENSE).

## API reference

The tables below are used to generate documentation-based tests. Each entry
links to the detailed markdown page in the `docs/` directory.

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/x-string/methods/new.md) | 1.0 | `public static function new(Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string> $data = ''): self`<br>Create a new instance of XString. You can provide a string, an array of strings, a Newline object, an HtmlTag object, or a Regex object. If an array is provided, it will be joined into a single string. If no data is provided, it defaults to an empty string. |
| [`withMode`](docs/x-string/methods/withMode.md) | 1.0 | `public function withMode(string $mode = 'graphemes', string $encoding = 'UTF-8'): self`<br>Create a new instance of XString with the specified **mode** (`'bytes'`, `'codepoints'`, or `'graphemes'`) and **encoding** (default `'UTF-8'`). |
| [`asBytes`](docs/x-string/methods/asBytes.md) | 1.0 | `public function asBytes(string $encoding = 'UTF-8'): self`<br>Alias for `withMode('bytes', $encoding)`. |
| [`asCodepoints`](docs/x-string/methods/asCodepoints.md) | 1.0 | `public function asCodepoints(string $encoding = 'UTF-8'): self`<br>Alias for `withMode('codepoints', $encoding)`. |
| [`asGraphemes`](docs/x-string/methods/asGraphemes.md) | 1.0 | `public function asGraphemes(string $encoding = 'UTF-8'): self`<br>Alias for `withMode('graphemes', $encoding)`. |

### Generation

Will throw if internal string is not empty (new($data) with $data not empty.)

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`rand`](docs/x-string/methods/rand.md) | 1.0 | `public static function rand(int $length, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): self`<br>Create a random string of a given length using the provided characters. |
| [`randInt`](docs/x-string/methods/randInt.md) | 1.0 | `public static function randInt(int $length, int $int_min = 0, int $int_max = 9): self`<br>Create a random integer between the specified minimum and maximum values (inclusive). |
| [`randLower`](docs/x-string/methods/randLower.md) | 1.0 | `public static function randLower(int $length, bool $include_numbers = false): self`<br>Create a random lowercase string of a given length. If $include_numbers is true, numbers will be included in the string. |
| [`randUpper`](docs/x-string/methods/randUpper.md) | 1.0 | `public static function randUpper(int $length, bool $include_numbers = false): self`<br>Create a random uppercase string of a given length. If $include_numbers is true, numbers will be included in the string. |
| [`randAlpha`](docs/x-string/methods/randAlpha.md) | 1.0 | `public static function randAlpha(int $length): self`<br>Create a random alphabetic string of a given length (both uppercase and lowercase). |
| [`randHex`](docs/x-string/methods/randHex.md) | 1.0 | `public static function randHex(int $length): self`<br>Create a random hexadecimal string of a given length. |
| [`randBase64`](docs/x-string/methods/randBase64.md) | 1.0 | `public static function randBase64(int $length): self`<br>Create a random Base64 string of a given length. |
| [`randBase62`](docs/x-string/methods/randBase62.md) | 1.0 | `public static function randBase62(int $length): self`<br>Create a random Base62 string of a given length. |
| [`uuid`](docs/x-string/methods/uuid.md) | 1.0 | `public static function uuid(int $version = 4, ?string $namespace = null, ?string $name = null): self`<br>Create a UUID (Universally Unique Identifier) of the specified version (1, 3, 4, or 5). For v3/v5, **$namespace** and **$name** are required and validated. |
| [`implode`](docs/x-string/methods/implode.md) | 1.0 | `public static function implode(array<Newline\|HtmlTag\|Regex\|Stringable\|string> $data, string $glue = ''): self`<br>Join array elements into a single string with an optional glue string between elements. |
| [`join`](docs/x-string/methods/join.md) | 1.0 | `public static function join(array<Newline\|HtmlTag\|Regex\|Stringable\|string> $data, string $glue = ''): self`<br>Alias for implode(). |
| [`fromFile`](docs/x-string/methods/fromFile.md) | 1.0 | `public static function fromFile(string $file_path, null \| int $length = null, null \| int $offset = 0, string $encoding = 'UTF-8'): self`<br>Create a new instance of XString from the contents of a file. You can specify the length and offset to read from the file and the **encoding** label. Use `withMode()` afterwards if you need a different logical mode. |

### Manipulation

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`append`](docs/x-string/methods/append.md) | 1.0 | `public function append(Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string> $data): self`<br>Append a string to the end of the current string. |
| [`prepend`](docs/x-string/methods/prepend.md) | 1.0 | `public function prepend(Newline\|HtmlTag\|Regex\|Stringable\|string\|array<Newline\|HtmlTag\|Regex\|Stringable\|string> $data): self`<br>Prepend a string to the beginning of the current string. |
| [`toUpper`](docs/x-string/methods/toUpper.md) | 1.0 | `public function toUpper(): self`<br>Convert the string to upper case. *(Alias: `toUpperCase()`)* |
| [`toUpperCase`](docs/x-string/methods/toUpperCase.md) | 1.0 | `public function toUpperCase(): self`<br>Alias for `toUpper()`. |
| [`ucfirst`](docs/x-string/methods/ucfirst.md) | 1.0 | `public function ucfirst(): self`<br>Convert the first character of the string to upper case. |
| [`lcfirst`](docs/x-string/methods/lcfirst.md) | 1.0 | `public function lcfirst(): self`<br>Convert the first character of the string to lower case. |
| [`toLower`](docs/x-string/methods/toLower.md) | 1.0 | `public function toLower(): self`<br>Convert the string to lower case. *(Alias: `toLowerCase()`)* |
| [`toLowerCase`](docs/x-string/methods/toLowerCase.md) | 1.0 | `public function toLowerCase(): self`<br>Alias for `toLower()`. |
| [`trim`](docs/x-string/methods/trim.md) | 1.0 | `public function trim($newline = true, $space = true, $tab = true): self`<br>Trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`ltrim`](docs/x-string/methods/ltrim.md) | 1.0 | `public function ltrim($newline = true, $space = true, $tab = true): self`<br>Left trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`rtrim`](docs/x-string/methods/rtrim.md) | 1.0 | `public function rtrim($newline = true, $space = true, $tab = true): self`<br>Right trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`replace`](docs/x-string/methods/replace.md) | 1.0 | `public function replace(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search, Newline\|HtmlTag\|Regex\|string $replace, null\|int $limit = null, $reversed = false): self`<br>Replace occurrences of a string with another string. By default it replaces all occurrences, but you can limit the number of replacements by setting the $limit parameter. If $reversed is true, it replaces from the end of the string. |
| [`replaceFirst`](docs/x-string/methods/replaceFirst.md) | 1.0 | `public function replaceFirst(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search, Newline\|HtmlTag\|Regex\|string $replace): self`<br>Replace the first occurrence of a string with another string. |
| [`replaceLast`](docs/x-string/methods/replaceLast.md) | 1.0 | `public function replaceLast(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search, Newline\|HtmlTag\|Regex\|string $replace): self`<br>Replace the last occurrence of a string with another string. |
| [`substr`](docs/x-string/methods/substr.md) | 1.0 | `public function substr(int $start, null \| int $length = null): self`<br>Get a substring of the string. If $length is not provided, it returns the substring from $start to the end of the string. *(Default mode for indexing/length is **graphemes**.)* |
| [`limit`](docs/x-string/methods/limit.md) | 1.0 | `public function limit(int $length, HtmlTag\|Newline\|Stringable\|string $append_string = '...'): self`<br>Truncate the string to a maximum length using the current mode and only append the suffix when truncation occurs. |
| [`repeat`](docs/x-string/methods/repeat.md) | 1.0 | `public function repeat(int $times): self`<br>Repeat the string a number of times. |
| [`reverse`](docs/x-string/methods/reverse.md) | 1.0 | `public function reverse(): self`<br>Reverse the string. *(Default mode **graphemes**.)* |
| [`shuffle`](docs/x-string/methods/shuffle.md) | 1.0 | `public function shuffle(): self`<br>Shuffle the characters in the string randomly. |
| [`slug`](docs/x-string/methods/slug.md) | 1.0 | `public function slug(Newline\|HtmlTag\|string $separator = '-'): self`<br>Convert the string to a URL-friendly "slug". Replaces spaces and special characters with the specified separator (default is '-'). |
| [`fileNameSlug`](docs/x-string/methods/fileNameSlug.md) | 1.0 | `public function fileNameSlug(Newline\|HtmlTag\|string $separator = '-'): self`<br>Generate a filesystem-safe filename, preserving extensions while replacing invalid characters with the separator. |
| [`insertAtInterval`](docs/x-string/methods/insertAtInterval.md) | 1.0 | `public function insertAtInterval(Newline\|HtmlTag\|Regex\|string $insert, int $interval): self`<br>Insert a string at regular intervals in the current string. *(Default counting mode **graphemes**.)* |
| [`wrap`](docs/x-string/methods/wrap.md) | 1.0 | `public function wrap(Newline\|HtmlTag\|Regex\|string $before, Newline\|HtmlTag\|Regex\|string $after = null): self`<br>Wrap the string with the specified before and after strings. If $after is not provided, it uses the same value as $before. |
| [`indent`](docs/x-string/methods/indent.md) | 1.0 | `public function indent(int $spaces = 2, int $tabs = 0, int $lines = 0): self`<br>Indent the string by adding spaces and/or tabs at the beginning of each line. Positive `$lines` count from the top; negative values count from the bottom. |
| [`outdent`](docs/x-string/methods/outdent.md) | 1.0 | `public function outdent(int $spaces = 2, int $tabs = 0, int $lines = 0): self`<br>Outdent the string by removing spaces and/or tabs from the beginning of each line. Positive `$lines` count from the top; negative values count from the bottom. |
| [`normalize`](docs/x-string/methods/normalize.md) | 1.0 | `public function normalize(int $form = Normalizer::FORM_C): self`<br>Normalize the string to a specific Unicode normalization form. Default is Normalizer::FORM_C. |
| [`pad`](docs/x-string/methods/pad.md) | 1.0 | `public function pad(int $length, Newline\|HtmlTag\|Regex\|string $pad_string = ' ', bool $left = true, bool $right = true): self`<br>Pad the string to a certain length with another string. You can specify whether to pad on the left, right, or both sides. By default it pads on both sides. |
| [`lpad`](docs/x-string/methods/lpad.md) | 1.0 | `public function lpad(int $length, Newline\|HtmlTag\|Regex\|string $pad_string = ' '): self`<br>Convenience wrapper around `pad()` that pads only on the left side. |
| [`rpad`](docs/x-string/methods/rpad.md) | 1.0 | `public function rpad(int $length, Newline\|HtmlTag\|Regex\|string $pad_string = ' '): self`<br>Convenience wrapper around `pad()` that pads only on the right side. |
| [`center`](docs/x-string/methods/center.md) | 1.0 | `public function center(int $length, Newline\|HtmlTag\|Regex\|string $pad_string = ' '): self`<br>Center the string within a certain length by padding it on both sides with another string. |
| [`mask`](docs/x-string/methods/mask.md) | 1.0 | `public function mask(Newline\|HtmlTag\|Regex\|string $mask, Newline\|HtmlTag\|Regex\|string $mask_char = '#', bool $reversed = false): self`<br>Mask the string using a specified mask pattern. The mask pattern uses a special character (default is '#') to indicate where characters from the original string should be placed, and can optionally align placeholders from the end. |
| [`collapseWhitespace`](docs/x-string/methods/collapseWhitespace.md) | 1.0 | `public function collapseWhitespace($space = true, $tab = true, $newline = false): self`<br>Collapse consecutive whitespace characters. **By default, newlines are not collapsed.** You can enable/disable collapsing individually. |
| [`collapseWhitespaceToSpace`](docs/x-string/methods/collapseWhitespaceToSpace.md) | 1.0 | `public function collapseWhitespaceToSpace(): self`<br>Replace every run of whitespace with a single regular space. |
| [`collapseWhitespaceToTab`](docs/x-string/methods/collapseWhitespaceToTab.md) | 1.0 | `public function collapseWhitespaceToTab(): self`<br>Replace every run of whitespace with a single tab character. |
| [`collapseWhitespaceToNewline`](docs/x-string/methods/collapseWhitespaceToNewline.md) | 1.0 | `public function collapseWhitespaceToNewline(): self`<br>Replace every run of whitespace with a single newline character. |
| [`between`](docs/x-string/methods/between.md) | 1.0 | `public function between(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $start, Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $end, $last_occurence = false, int $skip_start = 0, int $skip_end = 0): self`<br>Get the substring between two strings. If $last_occurence is true, it searches from the end of the string. You can skip a number of occurrences of the start and end strings by setting $skip_start and $skip_end. If an array is provided for `$start` and/or `$end`, it will search for the first value, then starting from that index, search for the next one, etc. |
| [`before`](docs/x-string/methods/before.md) | 1.0 | `public function before(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search, $last_occurence = false, int $skip = 0): self`<br>Get the substring before a specific string. If $last_occurence is true, it searches from the end of the string. You can skip a number of occurrences of the search string by setting $skip. |
| [`after`](docs/x-string/methods/after.md) | 1.0 | `public function after(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search, $last_occurence = false, int $skip = 0): self`<br>Get the substring after a specific string. If $last_occurence is true, it searches from the end of the string. You can skip a number of occurrences of the search string by setting $skip. |
| [`toSnake`](docs/x-string/methods/toSnake.md) | 1.0 | `public function toSnake(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $input_delimiter = ' '): self`<br>Convert the string to snake_case. **The parameter specifies the input delimiter or delimiters**; e.g. when `' '` is given, spaces are converted to `'_'`. Output always uses underscores. |
| [`toKebab`](docs/x-string/methods/toKebab.md) | 1.0 | `public function toKebab(): self`<br>Convert the string to kebab-case (lowercase words separated by hyphens). |
| [`toCamel`](docs/x-string/methods/toCamel.md) | 1.0 | `public function toCamel(bool $capitalize_first = false): self`<br>Convert the string to camelCase. If $capitalize_first is true, it converts to PascalCase (first letter capitalized). |
| [`toTitle`](docs/x-string/methods/toTitle.md) | 1.0 | `public function toTitle(): self`<br>Convert the string to Title Case (first letter of each word capitalized). |
| [`toPascal`](docs/x-string/methods/toPascal.md) | 1.0 | `public function toPascal(): self`<br>Convert the string to PascalCase (first letter capitalized, no spaces). |
| [`match`](docs/x-string/methods/match.md) | 1.0 | `public function match(Regex\|array<Regex> $pattern, int $offset = 0): null \| XString`<br>Match the string against one or more regex patterns starting at an optional offset. Returns the earliest matched substring as a new `XString`, or null if none are found. |

### Strip / Remove

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`strip`](docs/x-string/methods/strip.md) | 1.0 | `public function strip(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search, null\|int $limit = null, $reversed = false): self`<br>Remove all occurrences of a specific string from the current string. By default it removes all occurrences, but you can limit the number of removals by setting the $limit parameter. If $reversed is true, it removes from the end of the string. |
| [`stripEmojis`](docs/x-string/methods/stripEmojis.md) | 1.0 | `public function stripEmojis(): self`<br>Remove all emoji characters from the string. |
| [`stripTags`](docs/x-string/methods/stripTags.md) | 1.0 | `public function stripTags(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $allowed_tags = ''): self`<br>Strip HTML and PHP tags from the string. You can specify tags that should not be stripped by providing them in the $allowed_tags parameter. |
| [`stripAccents`](docs/x-string/methods/stripAccents.md) | 1.0 | `public function stripAccents(): self`<br>Remove accents from characters in the string. (e.g. é -> e, ñ -> n) |

### Affixing

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`ensurePrefix`](docs/x-string/methods/ensurePrefix.md) | 1.0 | `public function ensurePrefix(Newline\|HtmlTag\|string $prefix): self`<br>Ensure the string starts with the specified prefix. If it doesn't, the prefix is added. |
| [`ensureSuffix`](docs/x-string/methods/ensureSuffix.md) | 1.0 | `public function ensureSuffix(Newline\|HtmlTag\|string $suffix): self`<br>Ensure the string ends with the specified suffix. If it doesn't, the suffix is added. |
| [`removePrefix`](docs/x-string/methods/removePrefix.md) | 1.0 | `public function removePrefix(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $prefix): self`<br>Remove the specified prefix from the string if it exists. If an array is provided it will act as an OR. |
| [`removeSuffix`](docs/x-string/methods/removeSuffix.md) | 1.0 | `public function removeSuffix(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $suffix): self`<br>Remove the specified suffix from the string if it exists. If an array is provided it will act as an OR. |
| [`togglePrefix`](docs/x-string/methods/togglePrefix.md) | 1.0 | `public function togglePrefix(Newline\|HtmlTag\|string $prefix): self`<br>Toggle the specified prefix on the string. If the string starts with the prefix, it is removed. If it doesn't, the prefix is added. |
| [`toggleSuffix`](docs/x-string/methods/toggleSuffix.md) | 1.0 | `public function toggleSuffix(Newline\|HtmlTag\|string $suffix): self`<br>Toggle the specified suffix on the string. If the string ends with the suffix, it is removed. If it doesn't, the suffix is added. |
| [`hasPrefix`](docs/x-string/methods/hasPrefix.md) | 1.0 | `public function hasPrefix(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $prefix): bool`<br>Check if the string starts with the specified prefix. If an array is provided it will act as an OR. |
| [`hasSuffix`](docs/x-string/methods/hasSuffix.md) | 1.0 | `public function hasSuffix(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $suffix): bool`<br>Check if the string ends with the specified suffix. If an array is provided it will act as an OR. |

### Other methods

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`split`](docs/x-string/methods/split.md) | 1.0 | `public function split(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $delimiter, null\| int $limit = null): array`<br>Split the string into an array using the specified delimiter. If $limit is provided, it limits the number of splits. If an array is provided it will act as an OR. |
| [`explode`](docs/x-string/methods/explode.md) | 1.0 | `public function explode(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $delimiter, null \| int $limit = null): array`<br>Alias for split(). |
| [`lines`](docs/x-string/methods/lines.md) | 1.0 | `public function lines(bool $trim = false, null\|int $limit = null): array`<br>Split the string into an array of lines. If $trim is true, it trims each line. If $limit is provided, it limits the number of lines returned. |
| [`words`](docs/x-string/methods/words.md) | 1.0 | `public function words(bool $trim = false, null\|int $limit = null): array`<br>Split the string into an array of words. If $trim is true, it trims each word. |
| [`betweenAll`](docs/x-string/methods/betweenAll.md) | 1.0 | `public function betweenAll(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $start, Newline\|HtmlTag\|Regex\|string $end, $reversed = false): array`<br>Get all substrings between two strings. If $reversed is true, it searches from the end of the string. If arrays are provided for `$start` and/or `$end`, it will search for the first value, then starting from that index, search for the next one, etc. when `$reversed` it searches backwards. |
| [`length`](docs/x-string/methods/length.md) | 1.0 | `public function length(): int`<br>Get the length of the string. *(Default **graphemes**.)* |
| [`byteLength`](docs/x-string/methods/byteLength.md) | 1.0 | `public function byteLength(): int`<br>Get the byte length of the string. |
| [`graphemeLength`](docs/x-string/methods/graphemeLength.md) | 1.0 | `public function graphemeLength(): int`<br>Get the grapheme length of the string. |
| [`wordCount`](docs/x-string/methods/wordCount.md) | 1.0 | `public function wordCount(): int`<br>Get the number of words in the string. |
| [`lineCount`](docs/x-string/methods/lineCount.md) | 1.0 | `public function lineCount(): int`<br>Get the number of lines in the string. |
| [`sentenceCount`](docs/x-string/methods/sentenceCount.md) | 1.0 | `public function sentenceCount(): int`<br>Get the number of sentences in the string. |
| [`charAt`](docs/x-string/methods/charAt.md) | 1.0 | `public function charAt(int $index): string`<br>Get the character at a specific index in the string. *(Default **graphemes**.)* |
| [`contains`](docs/x-string/methods/contains.md) | 1.0 | `public function contains(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search): bool`<br>Check if the string contains a specific substring. If an array is provided it will act as an OR. |
| [`indexOf`](docs/x-string/methods/indexOf.md) | 1.0 | `public function indexOf(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search, $reversed = false): false\|int`<br>Get the index of the first occurrence of a substring. If $reversed is true, it searches from the end of the string. If an array is provided it will act as an OR. **Returns the first (lowest) index found** among all candidates, or false if not found. |
| [`isEmpty`](docs/x-string/methods/isEmpty.md) | 1.0 | `public function isEmpty($newline = true, $space = true, $tab = true): bool`<br>Check if the string is empty. By default it considers newlines, spaces and tabs as empty characters. You can disable checking for any of these by setting the relevant parameter to false. |
| [`startsWith`](docs/x-string/methods/startsWith.md) | 1.0 | `public function startsWith(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search): bool`<br>Check if the string starts with the specified substring. If an array is provided it will act as an OR. |
| [`endsWith`](docs/x-string/methods/endsWith.md) | 1.0 | `public function endsWith(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search): bool`<br>Check if the string ends with the specified substring. If an array is provided it will act as an OR. |
| [`equals`](docs/x-string/methods/equals.md) | 1.0 | `public function equals(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $string, bool $case_sensitive = true): bool`<br>Check if the string is equal to another string. You can specify whether the comparison should be case-sensitive. Default is true. If an array is provided it will act as an OR. |
| [`countOccurrences`](docs/x-string/methods/countOccurrences.md) | 1.0 | `public function countOccurrences(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $search): int`<br>Count the number of occurrences of a substring in the string. If an array is provided it will act as an OR. |
| [`matchAll`](docs/x-string/methods/matchAll.md) | 1.0 | `public function matchAll(Regex\|array<Regex> $pattern, false\|int $limit = false, array\|int\|null $flags = PREG_PATTERN_ORDER): array`<br>Match all occurrences of a regex pattern in the string. You can limit the number of matches by setting $limit. The $flags parameter determines the format of the returned array (default is PREG_PATTERN_ORDER). |
| [`similarityScore`](docs/x-string/methods/similarityScore.md) | 1.0 | `public function similarityScore(Newline\|HtmlTag\|Regex\|string $comparison, string $algorithm = 'github-style', array $options = []): float`<br>Calculate a normalized similarity score between this string and another. Returns a ratio in **[0.0, 1.0]**.<br><br>**Algorithms:**<br>`levenshtein` — Minimum single‑char edits; normalized by max length (great for typos).<br>`damerau-levenshtein` — Levenshtein where adjacent transposition counts as one edit.<br>`jaro-winkler` — Rewards matching order and common prefixes; ideal for short strings/names.<br>`lcs-myers` — Longest Common Subsequence (Myers). Score = `2·LCS/(\|A\|+\|B\|)`; feels like diffs.<br>`ratcliff-obershelp` — “Gestalt”/SequenceMatcher-style recursive common-substring ratio.<br>`jaccard` — Token set overlap; ignores order and duplicates.<br>`sorensen-dice` — Token overlap coefficient; slightly more forgiving than Jaccard.<br>`cosine-ngrams` — Cosine similarity over character/word n‑grams (supports TF, TF‑IDF, etc.).<br>`monge-elkan` — Soft token matching using a secondary metric per token pair.<br>`soft-tfidf` — TF‑IDF weighted tokens with soft equality via a secondary metric and threshold.<br>`github-style` — Token‑level LCS ratio with a small common‑prefix boost for a diff‑like feel.<br><br>**Options:**<br>`granularity`: `token` \| `word` \| `character` (default: `token`; “token” = whitespace/punct split)<br>`case_sensitive`: `true` \| `false` (default: `false`)<br>`threshold`: float `0..1` (default: `0.0`; post‑filter on the final score)<br>`n`: int for n‑grams (default: `3`; used by `cosine-ngrams`)<br>`weighting`: `binary` \| `tf` \| `log` \| `augmented` \| `double-normalization-0.5` \| `tfidf` (default: `binary`; applies to `cosine-ngrams`/`soft-tfidf`)<br>`tokenizer`: callable to tokenize input (default: internal tokenizer based on `granularity`)<br>`secondary_metric`: algorithm used inside `monge-elkan`/`soft-tfidf` (default: `jaro-winkler`)<br>`tau`: float `0..1` soft-match threshold for `soft-tfidf`/`monge-elkan` (default: `0.9`)<br>`normalize_whitespace`: `true` \| `false` (default: `true`)<br>`strip_punctuation`: `true` \| `false` (default: `true` when `granularity` ≠ `character`) |
| [`toInt`](docs/x-string/methods/toInt.md) | 1.0 | `public function toInt(): int`<br>Convert the current value to an integer, accepting optional underscores and floating-point notation (truncated toward zero) with range validation. |
| [`toFloat`](docs/x-string/methods/toFloat.md) | 1.0 | `public function toFloat(): float`<br>Convert the current value to a finite floating-point number with underscore support. |
| [`toBool`](docs/x-string/methods/toBool.md) | 1.0 | `public function toBool(): bool`<br>Interpret the string as a boolean using common affirmative/negative tokens and numeric semantics. |

### Encoding methods

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`transliterate`](docs/x-string/methods/transliterate.md) | 1.0 | `public function transliterate(string $to = 'ASCII//TRANSLIT'): self`<br>Transliterate the string to a different character set. Default is 'ASCII//TRANSLIT'. |
| [`toEncoding`](docs/x-string/methods/toEncoding.md) | 1.0 | `public function toEncoding(string $to_encoding, null\|string $from_encoding = null): self`<br>Convert the string to a different encoding. If $from_encoding is not provided, it tries to detect the current encoding. |
| [`detectEncoding`](docs/x-string/methods/detectEncoding.md) | 1.0 | `public function detectEncoding(array $encodings = ['UTF-8', 'ISO-8859-1', 'ASCII']): string\|false`<br>Detect the encoding of the string from a list of possible encodings. Returns the detected encoding or false if none matched. |
| [`isValidEncoding`](docs/x-string/methods/isValidEncoding.md) | 1.0 | `public function isValidEncoding(null\|string $encoding = null): bool`<br>Check if the string is valid in the specified encoding. If $encoding is not provided, it uses the current encoding of the string. |
| [`isAscii`](docs/x-string/methods/isAscii.md) | 1.0 | `public function isAscii(): bool`<br>Check if the string contains only ASCII characters. |
| [`isUtf8`](docs/x-string/methods/isUtf8.md) | 1.0 | `public function isUtf8(): bool`<br>Check if the string is valid UTF-8. |
| [`toUtf8`](docs/x-string/methods/toUtf8.md) | 1.0 | `public function toUtf8(nul \|string $from_encoding = null): self`<br>Convert the string to UTF-8 encoding. If $from_encoding is not provided, it tries to detect the current encoding. |
| [`toAscii`](docs/x-string/methods/toAscii.md) | 1.0 | `public function toAscii(null\|string $from_encoding = null): self`<br>Convert the string to ASCII encoding. If $from_encoding is not provided, it tries to detect the current encoding. |

### Encryption and Hashing

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`base64Encode`](docs/x-string/methods/base64Encode.md) | 1.0 | `public function base64Encode(): self`<br>Base64-encode the string. |
| [`base64Decode`](docs/x-string/methods/base64Decode.md) | 1.0 | `public function base64Decode(): self`<br>Base64-decode the string. |
| [`md5`](docs/x-string/methods/md5.md) | 1.0 | `public function md5(bool $raw_output = false): self`<br>Get the MD5 hash of the string. If $raw_output is true, it returns the raw binary format. |
| [`crc32`](docs/x-string/methods/crc32.md) | 1.0 | `public function crc32(bool $raw_output = false): self`<br>Get the CRC32B checksum of the string. If $raw_output is true, it returns the raw binary format. |
| [`sha1`](docs/x-string/methods/sha1.md) | 1.0 | `public function sha1(bool $raw_output = false): self`<br>Get the SHA-1 hash of the string. If $raw_output is true, it returns the raw binary format. |
| [`sha256`](docs/x-string/methods/sha256.md) | 1.0 | `public function sha256(bool $raw_output = false): self`<br>Get the SHA-256 hash of the string. If $raw_output is true, it returns the raw binary format. |
| [`crypt`](docs/x-string/methods/crypt.md) | 1.0 | `public function crypt(string $salt): self`<br>Hash the string using the crypt() function with the specified salt. |
| [`passwordHash`](docs/x-string/methods/passwordHash.md) | 1.0 | `public function passwordHash(int $algo = PASSWORD_BCRYPT, array $options = []): self`<br>Hash the string using password_hash() with the specified algorithm and options. Default is PASSWORD_BCRYPT. |
| [`passwordVerify`](docs/x-string/methods/passwordVerify.md) | 1.0 | `public function passwordVerify(string $hash): bool`<br>Verify the string against a given hash using password_verify(). Returns true if the string matches the hash, false otherwise. |
| [`encrypt`](docs/x-string/methods/encrypt.md) | 1.0 | `public function encrypt(string $password, string $cipher = 'sodium_xchacha20'): self`<br>Encrypt the string using authenticated encryption (AEAD). Requires libsodium for the default XChaCha20-Poly1305 path; pass `'aes-256-gcm'` to use the OpenSSL backend. Returns a versioned envelope (salt + nonce + tag + algorithm id + ciphertext) encoded as a string. |
| [`decrypt`](docs/x-string/methods/decrypt.md) | 1.0 | `public function decrypt(string $password, string $cipher = 'sodium_xchacha20'): self`<br>Decrypt a ciphertext produced by `encrypt()`. Verifies integrity (auth tag). Supports `sodium_xchacha20` (libsodium required) or `aes-256-gcm` (OpenSSL). Throws on invalid password, tampering, or unsupported version. |

### Codecs

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`htmlEscape`](docs/x-string/methods/htmlEscape.md) | 1.0 | `public function htmlEscape(int $flags = ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML5, string $encoding = 'UTF-8'): self`<br>Escape HTML special characters in the string. You can specify flags and encoding. |
| [`htmlUnescape`](docs/x-string/methods/htmlUnescape.md) | 1.0 | `public function htmlUnescape(): self`<br>Unescape HTML special characters in the string. |
| [`encodeHtmlEntities`](docs/x-string/methods/encodeHtmlEntities.md) | 1.0 | `public function encodeHtmlEntities(int $flags = ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML401, ?string $encoding = null, bool $double_encode = false): self`<br>Encode the string with `htmlentities()`, allowing custom flags, encoding, and optional double encoding (disabled by default). |
| [`decodeHtmlEntities`](docs/x-string/methods/decodeHtmlEntities.md) | 1.0 | `public function decodeHtmlEntities(int $flags = ENT_QUOTES \| ENT_HTML401, ?string $encoding = null): self`<br>Decode HTML entities back to characters while respecting flags and encoding. |
| [`urlEncode`](docs/x-string/methods/urlEncode.md) | 1.0 | `public function urlEncode(bool $raw = false): self`<br>URL-encode the string. If $raw is true, it uses rawurlencode(). |
| [`urlDecode`](docs/x-string/methods/urlDecode.md) | 1.0 | `public function urlDecode(bool $raw = false): self`<br>URL-decode the string. If $raw is true, it uses rawurldecode(). |
| [`nl2br`](docs/x-string/methods/nl2br.md) | 1.0 | `public function nl2br(bool $is_xhtml = true): self`<br>Convert newlines to HTML `<br>` tags. If $is_xhtml is true, it uses <br /> for XHTML compliance. |
| [`br2nl`](docs/x-string/methods/br2nl.md) | 1.0 | `public function br2nl(): self`<br>Convert HTML `<br>` tags to newlines. |

### Filesystem helpers

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`encodeWindowsFileName`](docs/x-string/methods/encodeWindowsFileName.md) | 1.0 | `public function encodeWindowsFileName(bool $double_encode = false): self`<br>Percent-encode characters that Windows forbids in filenames so decoding is lossless. |
| [`decodeWindowsFileName`](docs/x-string/methods/decodeWindowsFileName.md) | 1.0 | `public function decodeWindowsFileName(): self`<br>Decode `%XX` sequences in Windows filenames produced by `encodeWindowsFileName()`. |
| [`encodeWindowsFolderName`](docs/x-string/methods/encodeWindowsFolderName.md) | 1.0 | `public function encodeWindowsFolderName(bool $double_encode = false): self`<br>Percent-encode forbidden characters and reserved device names inside Windows folder names. |
| [`decodeWindowsFolderName`](docs/x-string/methods/decodeWindowsFolderName.md) | 1.0 | `public function decodeWindowsFolderName(): self`<br>Restore Windows folder names by decoding escapes generated by `encodeWindowsFolderName()`. |
| [`encodeWindowsPath`](docs/x-string/methods/encodeWindowsPath.md) | 1.0 | `public function encodeWindowsPath(bool $double_encode = false): self`<br>Percent-encode unsafe characters within Windows path segments while preserving drive and UNC prefixes. |
| [`decodeWindowsPath`](docs/x-string/methods/decodeWindowsPath.md) | 1.0 | `public function decodeWindowsPath(): self`<br>Decode Windows path strings that were encoded with `encodeWindowsPath()`. |
| [`toWindowsFileName`](docs/x-string/methods/toWindowsFileName.md) | 1.0 | `public function toWindowsFileName(): self`<br>Sanitise the value into a Windows-compatible filename, escaping reserved device names and forbidden characters. |
| [`toWindowsFolderName`](docs/x-string/methods/toWindowsFolderName.md) | 1.0 | `public function toWindowsFolderName(): self`<br>Generate a Windows-safe folder name by replacing forbidden characters, trimming trailing dots/spaces, and prefixing reserved names. |
| [`toWindowsPath`](docs/x-string/methods/toWindowsPath.md) | 1.0 | `public function toWindowsPath(): self`<br>Normalise the string into a Windows path with safe segments, preserving drive letters and UNC prefixes. |
| [`encodeUnixFileName`](docs/x-string/methods/encodeUnixFileName.md) | 1.0 | `public function encodeUnixFileName(bool $double_encode = false): self`<br>Percent-encode `/`, `%`, and null bytes so Unix filenames can be restored exactly. |
| [`decodeUnixFileName`](docs/x-string/methods/decodeUnixFileName.md) | 1.0 | `public function decodeUnixFileName(): self`<br>Decode `%XX` escapes created by `encodeUnixFileName()`. |
| [`encodeUnixFolderName`](docs/x-string/methods/encodeUnixFolderName.md) | 1.0 | `public function encodeUnixFolderName(bool $double_encode = false): self`<br>Percent-encode Unix-forbidden characters inside folder names while leaving everything else untouched. |
| [`decodeUnixFolderName`](docs/x-string/methods/decodeUnixFolderName.md) | 1.0 | `public function decodeUnixFolderName(): self`<br>Reverse the escapes created by `encodeUnixFolderName()`. |
| [`encodeUnixPath`](docs/x-string/methods/encodeUnixPath.md) | 1.0 | `public function encodeUnixPath(bool $double_encode = false): self`<br>Percent-encode forbidden characters within Unix path segments while keeping `/` separators. |
| [`decodeUnixPath`](docs/x-string/methods/decodeUnixPath.md) | 1.0 | `public function decodeUnixPath(): self`<br>Decode Unix path strings generated by `encodeUnixPath()`. |
| [`toUnixFileName`](docs/x-string/methods/toUnixFileName.md) | 1.0 | `public function toUnixFileName(): self`<br>Produce a Unix-safe filename by removing control bytes and replacing forbidden separators. |
| [`toUnixFolderName`](docs/x-string/methods/toUnixFolderName.md) | 1.0 | `public function toUnixFolderName(): self`<br>Produce a Unix-safe folder name by stripping control bytes, replacing slashes, and collapsing special names. |
| [`toUnixPath`](docs/x-string/methods/toUnixPath.md) | 1.0 | `public function toUnixPath(): self`<br>Convert the value to a Unix path using cleaned segments joined by forward slashes. |
| [`encodeMacOSFileName`](docs/x-string/methods/encodeMacOSFileName.md) | 1.0 | `public function encodeMacOSFileName(bool $double_encode = false): self`<br>Percent-encode `/`, `:`, `%`, and null bytes in macOS filenames while leaving other characters intact. |
| [`decodeMacOSFileName`](docs/x-string/methods/decodeMacOSFileName.md) | 1.0 | `public function decodeMacOSFileName(): self`<br>Decode `%XX` escapes created by `encodeMacOSFileName()`. |
| [`encodeMacOSFolderName`](docs/x-string/methods/encodeMacOSFolderName.md) | 1.0 | `public function encodeMacOSFolderName(bool $double_encode = false): self`<br>Percent-encode macOS-forbidden characters inside folder names for lossless decoding. |
| [`decodeMacOSFolderName`](docs/x-string/methods/decodeMacOSFolderName.md) | 1.0 | `public function decodeMacOSFolderName(): self`<br>Decode escapes produced by `encodeMacOSFolderName()`. |
| [`encodeMacOSPath`](docs/x-string/methods/encodeMacOSPath.md) | 1.0 | `public function encodeMacOSPath(bool $double_encode = false): self`<br>Percent-encode forbidden characters within macOS path segments while preserving `/` separators. |
| [`decodeMacOSPath`](docs/x-string/methods/decodeMacOSPath.md) | 1.0 | `public function decodeMacOSPath(): self`<br>Decode macOS path strings generated by `encodeMacOSPath()`. |
| [`toMacOSFileName`](docs/x-string/methods/toMacOSFileName.md) | 1.0 | `public function toMacOSFileName(): self`<br>Return a macOS-friendly filename by replacing colons/slashes and collapsing special names. |
| [`toMacOSFolderName`](docs/x-string/methods/toMacOSFolderName.md) | 1.0 | `public function toMacOSFolderName(): self`<br>Return a macOS-friendly folder name by replacing colons/slashes, stripping control characters, and normalising special names. |
| [`toMacOSPath`](docs/x-string/methods/toMacOSPath.md) | 1.0 | `public function toMacOSPath(): self`<br>Normalise the value to a macOS path with cleaned segments and `/` separators. |
| [`encodeSafeFileName`](docs/x-string/methods/encodeSafeFileName.md) | 1.0 | `public function encodeSafeFileName(bool $double_encode = false): self`<br>Percent-encode filesystem-forbidden characters for maximum cross-platform safety. |
| [`decodeSafeFileName`](docs/x-string/methods/decodeSafeFileName.md) | 1.0 | `public function decodeSafeFileName(): self`<br>Decode `%XX` escapes created by `encodeSafeFileName()`. |
| [`encodeSafeFolderName`](docs/x-string/methods/encodeSafeFolderName.md) | 1.0 | `public function encodeSafeFolderName(bool $double_encode = false): self`<br>Percent-encode unsafe characters in folder names according to the strictest platform rules. |
| [`decodeSafeFolderName`](docs/x-string/methods/decodeSafeFolderName.md) | 1.0 | `public function decodeSafeFolderName(): self`<br>Decode escapes produced by `encodeSafeFolderName()`. |
| [`encodeSafePath`](docs/x-string/methods/encodeSafePath.md) | 1.0 | `public function encodeSafePath(bool $double_encode = false): self`<br>Percent-encode forbidden characters in each path segment while normalising separators to `/`. |
| [`decodeSafePath`](docs/x-string/methods/decodeSafePath.md) | 1.0 | `public function decodeSafePath(): self`<br>Decode portable paths that were encoded with `encodeSafePath()`. |
| [`toSafeFileName`](docs/x-string/methods/toSafeFileName.md) | 1.0 | `public function toSafeFileName(): self`<br>Generate a conservative filename safe across Windows, Unix-like systems, and macOS. |
| [`toSafeFolderName`](docs/x-string/methods/toSafeFolderName.md) | 1.0 | `public function toSafeFolderName(): self`<br>Generate a conservative folder name safe across Windows, Unix-like systems, and macOS. |
| [`toSafePath`](docs/x-string/methods/toSafePath.md) | 1.0 | `public function toSafePath(): self`<br>Produce a portable path by sanitising each segment and joining with forward slashes. |

## Newline class methods

Used to tell search arguments in other methods that you want to search for newlines.

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/newline/methods/new.md) | 1.0 | `public static function new(null\|string $newline = null): self`<br>Create a new Newline instance. Default newline is *any*. |
| [`startsWith`](docs/newline/methods/startsWith.md) | 1.0 | `public function startsWith(null\|string $string, bool $trim = false): self`<br>Creates a newline that starts with `$string`. Can be used to check if the newline starts with the specified string. |
| [`endsWith`](docs/newline/methods/endsWith.md) | 1.0 | `public function endsWith(null\|string $string, bool $trim = false): self`<br>Creates a newline that ends with `$string`. Can be used to check if the newline ends with the specified string. |
| [`contains`](docs/newline/methods/contains.md) | 1.0 | `public function contains(null\|string $string): self`<br>Used to check if a newline contains the specified string. |
| [`equals`](docs/newline/methods/equals.md) | 1.0 | `public function equals(null\|string $string): self`<br>Used to check if a newline is equal to the specified string. |

## Regex class methods (all static)

Used to tell search arguments in other methods that you want to search for a regex pattern.

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/regex/methods/new.md) | 1.0 | `public static function new(string $pattern, int $modifiers = 0): self`<br>Create a new Pattern instance. $modifiers is a bitmask of regex modifiers (ex. Pattern::MODIFIER_CASE_INSENSITIVE). |

## HtmlTag class methods

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/html-tag/methods/new.md) | 1.0 | `public static function new(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): self`<br>Create a new HtmlTag instance. |
| [`closeTag`](docs/html-tag/methods/closeTag.md) | 1.0 | `public static function closeTag(string $tag_name, bool $case_sensitive = false): self`<br>Create a new HtmlTag instance that matches a closing tag. |
| [`withClass`](docs/html-tag/methods/withClass.md) | 1.0 | `public function withClass(string\|array<string> ...$class_name): self`<br>Add one or more class name conditions to the HtmlTag instance. |
| [`withId`](docs/html-tag/methods/withId.md) | 1.0 | `public function withId(string $id): self`<br>Add an ID condition to the HtmlTag instance. The tag must have this ID to match. |
| [`withAttribute`](docs/html-tag/methods/withAttribute.md) | 1.0 | `public function withAttribute(string $attr_name, null\|string $attr_value = null, bool $case_sensitive = false): self`<br>Add an attribute condition to the HtmlTag instance. The tag must have this attribute (and value if provided) to match. |
| [`withBody`](docs/html-tag/methods/withBody.md) | 1.0 | `public function withBody(HtmlTag\|Newline\|Regex\|Stringable\|string\|array $body): self`<br>Append body fragments to the opening tag. |
| [`withEndTag`](docs/html-tag/methods/withEndTag.md) | 1.0 | `public function withEndTag(bool $append_newline = true): self`<br>Emit the matching closing tag, optionally inserting a trailing newline. |

## XStringType (factory) class

<!-- method-list -->
| Method | Version | Signature & Description |
| --- | --- | --- |
| [`newline`](docs/x-string-type/methods/newline.md) | 1.0 | `public static function newline(null\|string $newline = null): Newline`<br>Create a new `Newline` instance via `XStringType::newline()`. |
| [`regex`](docs/x-string-type/methods/regex.md) | 1.0 | `public static function regex(string $pattern): Regex`<br>Create a new `Regex` instance via `XStringType::regex()`. |
| [`htmlTag`](docs/x-string-type/methods/htmlTag.md) | 1.0 | `public static function htmlTag(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): HtmlTag`<br>Create a new `HtmlTag` instance via `XStringType::htmlTag()`. |
| [`htmlCloseTag`](docs/x-string-type/methods/htmlCloseTag.md) | 1.0 | `public static function htmlCloseTag(string $tag_name, bool $case_sensitive = false): HtmlTag`<br>Create a new `HtmlTag` instance that matches a closing tag via `XStringType::htmlCloseTag()`. |




# Examples

Here are some examples of how to use the `XString` class:

## Basic Usage

<!-- test:basic -->
```php
use Orryv\XString;

// Create a new XString instance
$str = XString::new(" Hello, World! \n");
#Test: self::assertTrue($str instanceof XString);
#Test: self::assertEquals(" Hello, World! \n", (string)$str);

// Trim whitespace
$trimmed = $str->trim();
#Test: self::assertEquals("Hello, World!", (string)$trimmed);
```

## Newlines

<!-- test:newlines -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$str = ' Line1 - blabla' . PHP_EOL . 'Hello, World!';

$string = XString::new($str);
#Test: self::assertEquals($str, (string)$string);

// Remove first line (one way to do it)
$string = $string->after(Newline::new()->startsWith('Line1', trim:true));
//Same as: $string->after(XStringType::newline()->startsWith('Line1', trim:true));
#Test: self::assertEquals("Hello, World!", (string)$string);
```