# XString

## Requirements
- **PHP Versions:** 8.2, 8.3, 8.4
- **Extensions:** `mbstring`, `intl` (Normalizer + grapheme functions), `iconv`
- **Dev tools:** PHPUnit ^11, Psalm ^5, PHPStan ^1.11, Infection (optional), Composer
- **OS:** Linux/macOS/Windows. Docker optional.

## TODO's
- [ ] trim(), ltrim(), rtrim(): must also have a parameter to trim other characters than space, tab, newline. (ex. comma, dot, ...)
- [ ] think about a method that does the reverse as trim: it makes sure a character (or multiple characters) is at the start and/or end of the string. (ex. make sure a string starts with / and ends with /)

# Roadmap
- [ ] Setup the project
  - Create composer.json with all needed dependencies:
     - PHPUnit
  - Add scripts to dockerfile (if relevant):
    - Docker scripts (if docker is used in this project)
      - `docker:up`: start docker containers
      - `docker:down`: stop docker containers
      - `docker:reset`: remove docker containers and images and start up again
      - `docker:restart`: helpful if changes made to dockerfile or docker-compose.
    - Testing:
      - `test`: tests everything
      - `test:no-docker`: all tests except ones that depend on docker
      - `test:unit` / `test:unit:no-docker`: unit tests
      - `test:integration` / `test:integration:no-docker`: integration tests
      - `test:contract` / `test:contract:no-docker`
      - `test:snapshot` / `test:snapshot:no-docker`
      - `test:end-to-end` / `test:end-to-end:no-docker`
      - `test:performance` / `test:performance:no-docker`
  - If docker is needed for this project: add all needed dockerfiles in docker/
  - Create .gitignore and add relevant files and paths
  - Add github CI pipelines:
    - PHPUnit on PHP 8.2
    - PHPUnit on PHP 8.3
    - PHPUnit on PHP 8.4
- [ ] Create tests for everything that is testable. Split up/refactor code if needed for better testing (without losing the exact functionalities it accomplishes). Make sure composer `test:no-docker` works as expected (should run every test that doesn't need docker.) Tests are (create the folders in tests/ and put .gitkeep if empty):
  - Unit
  - Integration
  - Contract
  - Snapshot
  - End-To-End
  - Performance

# Post-TODO's

- [ ] Make sure all parameters, variables, etc. are snake_case.
- [ ] Make sure every method, function and class has docblock, containing a description, the arguments, what it returns, what it throws (exceptions, if any.)

# Issues, improvements and advice

# Documentation

Documentation follows a specific design, but here are some things you must know:

- Use `<!-- test -->` before code blocks to reference that the code in the code snippet should be used in tests (they are a special test in PHPUnit: Documentation tests.) Write `#Test: ` before the PHPUnit assertions inside the code block.

# Concept

A class to manipulate strings. Uses __toString() to convert to string when needed. **XString is immutable** and stores a single internal **string**, a **mode** (`bytes`|`codepoints`|`graphemes`) and an **encoding** (default **UTF‑8**). Default length/iteration **mode is graphemes**.

### Important notes and considerations

- All inputs are plain **string** or adapters (`Newline`, `HtmlTag`, `Regex`). **Mode** controls how positions/lengths are interpreted; default is **graphemes**.

### Setup

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
| [`pad`](docs/x-string/methods/pad.md) | 1.0 | `public function pad(int $length, Newline\|HtmlTag\|Regex\|string $pad_string = ' ', bool $left = true, bool $right = false): self`<br>Pad the string to a certain length with another string. You can specify whether to pad on the left, right, or both sides. Default is right padding. |
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
| [`stripTags`](docs/x-string/methods/strip-tags.md) | 1.0 | `public function stripTags(Newline\|HtmlTag\|Regex\|string\|array<Newline\|HtmlTag\|Regex\|string> $allowed_tags = ''): self`<br>Strip HTML and PHP tags from the string. You can specify tags that should not be stripped by providing them in the $allowed_tags parameter. |
| [`stripAccents`](docs/x-string/methods/strip-accents.md) | 1.0 | `public function stripAccents(): self`<br>Remove accents from characters in the string. (e.g. é -> e, ñ -> n) |

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
| [`urlEncode`](docs/x-string/methods/urlEncode.md) | 1.0 | `public function urlEncode(bool $raw = false): self`<br>URL-encode the string. If $raw is true, it uses rawurlencode(). |
| [`urlDecode`](docs/x-string/methods/urlDecode.md) | 1.0 | `public function urlDecode(bool $raw = false): self`<br>URL-decode the string. If $raw is true, it uses rawurldecode(). |
| [`nl2br`](docs/x-string/methods/nl2br.md) | 1.0 | `public function nl2br(bool $is_xhtml = true): self`<br>Convert newlines to HTML `<br>` tags. If $is_xhtml is true, it uses <br /> for XHTML compliance. |
| [`br2nl`](docs/x-string/methods/br2nl.md) | 1.0 | `public function br2nl(): self`<br>Convert HTML `<br>` tags to newlines. |


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
| [`XStringType::newline()`](docs/x-string-type/methods/newline.md) | 1.0 | `public static function newline(null\|string $newline = null): Newline`<br>Create a new Newline instance. Default newline is *any*. |
| [`XStringType::regex()`](docs/x-string-type/methods/regex.md) | 1.0 | `public static function regex(string $pattern, int $modifiers = 0): Regex`<br>Create a new Regex instance. $modifiers is a bitmask of regex modifiers (ex. Pattern::MODIFIER_CASE_INSENSITIVE). |
| [`XStringType::htmlTag()`](docs/x-string-type/methods/htmlTag.md) | 1.0 | `public static function htmlTag(string $tag_name, bool $self_closing = false, bool $case_sensitive = false): HtmlTag`<br>Create a new HtmlTag instance. |
| [`XStringType::htmlCloseTag()`](docs/x-string-type/methods/htmlCloseTag.md) | 1.0 | `public static function htmlCloseTag(string $tag_name, bool $case_sensitive = false): HtmlTag`<br>Create a new HtmlTag instance that matches a closing tag. |




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