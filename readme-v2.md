# XString

## Requirements
- **PHP Versions:** 8.2, 8.3, 8.4
- **Extensions:** `mbstring`, `intl` (Normalizer + grapheme functions), `iconv`
- **Dev tools:** PHPUnit ^11, Psalm ^5, PHPStan ^1.11, Infection (optional), Composer
- **OS:** Linux/macOS/Windows. Docker optional.

## TODO's
- [ ] Think about issues, improvements and advice and put it in the section below (`Issues, improvements and advice`): about this readme and about the code that we will create using what's written in `# Concept`. Some questions to ask (but by far not a complete list): 
  - Can I create the source code the way it should work described here? 
  - Can I propose to change something that will make the code easier to test?
  - Can I propose changes that will make it easier to understand for the user, while still carrying the same functionality?
  - Does what's described/coded have inconsistencies or contradictions?
- [ ] Create a detailed readme (other file than this) which an AI will use to create the project and/or be guided. The readme must reflect everything what's put in the `# Concept` below. 
- [ ] Create a roadmap and put it in `# Roadmap` to split up the steps that need to be done that lead up to the finished code/product. Max 12 steps. Keep in mind that testing must be extensively, so already consider splitting up code etc. when giving specifics in the roadmap. The roadmap will be applied after all TODO's in this list are done, keep that in mind. There might already be steps in the roadmap, you can add steps before, after and/or in-between them. You can change the steps already provided in the Roadmap if needed, but don't remove anything that might be useful later (ex. all test categories: unit, integration, ...)

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

- When using `string` instead of `Multibyte`/`Byte`/`Grapheme`, it defaults to `Grapheme` mode (single-byte).

### Setup

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/x-string/methods/new.md) | 1.0 | `public static function new(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $data = ''): self`<br>Create a new instance of XString. You can provide a string, an array of strings, a Newline object, a Regex object, a Multibyte object or a Grapheme object. If an array is provided, it will be joined into a single string. If no data is provided, it defaults to an empty string. |

### Generation

Will throw if internal string is not empty (new($data) with $data not empty.)

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`rand`](docs/x-string/methods/rand.md) | 1.0 | `public static function rand(int $length, Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): self`<br>Create a random string of a given length using the provided characters. |
| [`randInt`](docs/x-string/methods/rand-int.md) | 1.0 | `public static function randInt(int $length, int $int_min = 0, int $int_max = 9): self`<br>Create a random integer between the specified minimum and maximum values (inclusive). |
| [`randLower`](docs/x-string/methods/rand-lower.md) | 1.0 | `public static function randLower(int $length, bool $include_numbers = false): self`<br>Create a random lowercase string of a given length. If $include_numbers is true, numbers will be included in the string. |
| [`randUpper`](docs/x-string/methods/rand-upper.md) | 1.0 | `public static function randUpper(int $length, bool $include_numbers = false): self`<br>Create a random uppercase string of a given length. If $include_numbers is true, numbers will be included in the string. |
| [`randAlpha`](docs/x-string/methods/rand-alpha.md) | 1.0 | `public static function randAlpha(int $length): self`<br>Create a random alphabetic string of a given length (both uppercase and lowercase). |
| [`randHex`](docs/x-string/methods/rand-hex.md) | 1.0 | `public static function randHex(int $length): self`<br>Create a random hexadecimal string of a given length. |
| [`randBase64`](docs/x-string/methods/rand-base64.md) | 1.0 | `public static function randBase64(int $length): self`<br>Create a random Base64 string of a given length. |
| [`randBase62`](docs/x-string/methods/rand-base62.md) | 1.0 | `public static function randBase62(int $length): self`<br>Create a random Base62 string of a given length. |
| [`uuid`](docs/x-string/methods/uuid.md) | 1.0 | `public static function uuid(int $version = 4, ?string $namespace = null, ?string $name = null): self`<br>Create a UUID (Universally Unique Identifier) of the specified version (1, 3, 4, or 5). For v3/v5, **$namespace** and **$name** are required and validated. |
| [`implode`](docs/x-string/methods/implode.md) | 1.0 | `public static function implode(array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $data, string $glue = ''): self`<br>Join array elements into a single string with an optional glue string between elements. |
| [`join`](docs/x-string/methods/join.md) | 1.0 | `public static function join(array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $data, string $glue = ''): self`<br>Alias for implode(). |
| [`fromFile`](docs/x-string/methods/from-file.md) | 1.0 | `public static function fromFile(string $file_path, null \| int $length = null, null \| int $offset = 0, $use_multibyte = false, $use_grapheme = false): self`<br>Create a new instance of XString from the contents of a file. You can specify the length and offset to read from the file. If $use_multibyte is true, it will use multibyte string functions. If $use_grapheme is true, it will use grapheme string functions. Note: if both are true, grapheme functions will be used. |

### Manipulation

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`append`](docs/x-string/methods/append.md) | 1.0 | `public function append(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $data): self`<br>Append a string to the end of the current string. |
| [`prepend`](docs/x-string/methods/prepend.md) | 1.0 | `public function prepend(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $data): self`<br>Prepend a string to the beginning of the current string. |
| [`toUpper`](docs/x-string/methods/to-upper.md) | 1.0 | `public function toUpper(): self`<br>Convert the string to upper case. *(Alias: `toUpperCase()`)* |
| [`toUpperCase`](docs/x-string/methods/to-upper-case.md) | 1.0 | `public function toUpperCase(): self`<br>Alias for `toUpper()`. |
| [`ucfirst`](docs/x-string/methods/ucfirst.md) | 1.0 | `public function ucfirst(): self`<br>Convert the first character of the string to upper case. |
| [`lcfirst`](docs/x-string/methods/lcfirst.md) | 1.0 | `public function lcfirst(): self`<br>Convert the first character of the string to lower case. |
| [`toLower`](docs/x-string/methods/to-lower.md) | 1.0 | `public function toLower(): self`<br>Convert the string to lower case. *(Alias: `toLowerCase()`)* |
| [`toLowerCase`](docs/x-string/methods/to-lower-case.md) | 1.0 | `public function toLowerCase(): self`<br>Alias for `toLower()`. |
| [`trim`](docs/x-string/methods/trim.md) | 1.0 | `public function trim($newline = true, $space = true, $tab = true): self`<br>Trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`ltrim`](docs/x-string/methods/ltrim.md) | 1.0 | `public function ltrim($newline = true, $space = true, $tab = true): self`<br>Left trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`rtrim`](docs/x-string/methods/rtrim.md) | 1.0 | `public function rtrim($newline = true, $space = true, $tab = true): self`<br>Right trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`replace`](docs/x-string/methods/replace.md) | 1.0 | `public function replace(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search, Newline\|Multibyte\|Byte\|Grapheme\|string $replace, null\|int $limit = null, $reversed = false): self`<br>Replace occurrences of a string with another string. By default it replaces all occurrences, but you can limit the number of replacements by setting the $limit parameter. If $reversed is true, it replaces from the end of the string. |
| [`replaceFirst`](docs/x-string/methods/replace-first.md) | 1.0 | `public function replaceFirst(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search, Newline\|Multibyte\|Byte\|Grapheme\|string $replace): self`<br>Replace the first occurrence of a string with another string. |
| [`replaceLast`](docs/x-string/methods/replace-last.md) | 1.0 | `public function replaceLast(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search, Newline\|Multibyte\|Byte\|Grapheme\|string $replace): self`<br>Replace the last occurrence of a string with another string. |
| [`substr`](docs/x-string/methods/substring.md) | 1.0 | `public function substr(int $start, null \| int $length = null): self`<br>Get a substring of the string. If $length is not provided, it returns the substring from $start to the end of the string. *(Default mode for indexing/length is **graphemes**.)* |
| [`repeat`](docs/x-string/methods/repeat.md) | 1.0 | `public function repeat(int $times): self`<br>Repeat the string a number of times. |
| [`reverse`](docs/x-string/methods/reverse.md) | 1.0 | `public function reverse(): self`<br>Reverse the string. *(Default mode **graphemes**.)* |
| [`shuffle`](docs/x-string/methods/shuffle.md) | 1.0 | `public function shuffle(): self`<br>Shuffle the characters in the string randomly. |
| [`slug`](docs/x-string/methods/slug.md) | 1.0 | `public function slug(Newline\|Multibyte\|Byte\|Grapheme\|string $separator = '-'): self`<br>Convert the string to a URL-friendly "slug". Replaces spaces and special characters with the specified separator (default is '-'). |
| [`insertAtInterval`](docs/x-string/methods/insert-at-interval.md) | 1.0 | `public function insertAtInterval(Newline\|Multibyte\|Byte\|Grapheme\|string $insert, int $interval): self`<br>Insert a string at regular intervals in the current string. *(Default counting mode **graphemes**.)* |
| [`wrap`](docs/x-string/methods/wrap.md) | 1.0 | `public function wrap(Newline\|Multibyte\|Byte\|Grapheme\|string $before, Newline\|Multibyte\|Byte\|Grapheme\|string $after = null): self`<br>Wrap the string with the specified before and after strings. If $after is not provided, it uses the same value as $before. |
| [`indent`](docs/x-string/methods/indent.md) | 1.0 | `public function indent(int $spaces = 2, int $tabs = 0, int $lines = 0): self`<br>Indent the string by adding spaces and/or tabs at the beginning of each line. You can specify the number of lines to indent too. |
| [`outdent`](docs/x-string/methods/outdent.md) | 1.0 | `public function outdent(int $spaces = 2, int $tabs = 0, int $lines = 0): self`<br>Outdent the string by removing spaces and/or tabs from the beginning of each line. You can specify the number of lines to outdent too. |
| [`normalize`](docs/x-string/methods/normalize.md) | 1.0 | `public function normalize(int $form = Normalizer::FORM_C): self`<br>Normalize the string to a specific Unicode normalization form. Default is Normalizer::FORM_C. |
| [`pad`](docs/x-string/methods/pad.md) | 1.0 | `public function pad(int $length, Newline\|Multibyte\|Byte\|Grapheme\|string $pad_string = ' ', bool $left = true, bool $right = false): self`<br>Pad the string to a certain length with another string. You can specify whether to pad on the left, right, or both sides. Default is right padding. |
| [`center`](docs/x-string/methods/center.md) | 1.0 | `public function center(int $length, Newline\|Multibyte\|Byte\|Grapheme\|string $pad_string = ' '): self`<br>Center the string within a certain length by padding it on both sides with another string. |
| [`mask`](docs/x-string/methods/mask.md) | 1.0 | `public function mask(Newline\|Multibyte\|Byte\|Grapheme\|string $mask, Newline\|Multibyte\|Byte\|Grapheme\|string $mask_char = '#'): self`<br>Mask the string using a specified mask pattern. The mask pattern uses a special character (default is '#') to indicate where characters from the original string should be placed. |
| [`collapseWhitespace`](docs/x-string/methods/collapse-whitespace.md) | 1.0 | `public function collapseWhitespace($space = true, $tab = true, $newline = false): self`<br>Collapse consecutive whitespace characters. **By default, newlines are not collapsed.** You can enable/disable collapsing individually. |
| [`between`](docs/x-string/methods/between.md) | 1.0 | `public function between(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $start, Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $end, $reversed = false, int $skip_start = 0, int $skip_end = 0): self`<br>Get the substring between two strings. If $reversed is true, it searches from the end of the string. You can skip a number of occurrences of the start and end strings by setting $skip_start and $skip_end. If an array is provided for `$start` and/or `$end`, it will search for the first value, then starting from that index, search for the next one, etc. |
| [`before`](docs/x-string/methods/before.md) | 1.0 | `public function before(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search, $reversed = false, int $skip = 0): self`<br>Get the substring before a specific string. If $reversed is true, it searches from the end of the string. You can skip a number of occurrences of the search string by setting $skip. |
| [`after`](docs/x-string/methods/after.md) | 1.0 | `public function after(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search, $reversed = false, int $skip = 0): self`<br>Get the substring after a specific string. If $reversed is true, it searches from the end of the string. You can skip a number of occurrences of the search string by setting $skip. |
| [`toSnake`](docs/x-string/methods/to-snake.md) | 1.0 | `public function toSnake(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string $input_delimiter = ' '): self`<br>Convert the string to snake_case. **The parameter specifies the input delimiter**; e.g. when `' '` is given, spaces are converted to `'_'`. Output always uses underscores. |
| [`toKebab`](docs/x-string/methods/to-kebab.md) | 1.0 | `public function toKebab(): self`<br>Convert the string to kebab-case (lowercase words separated by hyphens). |
| [`toCamel`](docs/x-string/methods/to-camel.md) | 1.0 | `public function toCamel(bool $capitalize_first = false): self`<br>Convert the string to camelCase. If $capitalize_first is true, it converts to PascalCase (first letter capitalized). |
| [`toTitle`](docs/x-string/methods/to-title.md) | 1.0 | `public function toTitle(): self`<br>Convert the string to Title Case (first letter of each word capitalized). |
| [`toPascal`](docs/x-string/methods/to-pascal.md) | 1.0 | `public function toPascal(): self`<br>Convert the string to PascalCase (first letter capitalized, no spaces). |
| [`match`](docs/x-string/methods/match.md) | 1.0 | `public function match(Regex\|array<Regex> $pattern): null \| array`<br>Match the string against a regex pattern. Returns an array of matches or null if no match is found. |

### Strip / Remove

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`strip`](docs/x-string/methods/strip.md) | 1.0 | `public function strip(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search, null\|int $limit = null, $reversed = false): self`<br>Remove all occurrences of a specific string from the current string. By default it removes all occurrences, but you can limit the number of removals by setting the $limit parameter. If $reversed is true, it removes from the end of the string. |
| [`stripEmojis`](docs/x-string/methods/strip-emojis.md) | 1.0 | `public function stripEmojis(): self`<br>Remove all emoji characters from the string. |
| [`stripTags`](docs/x-string/methods/strip-tags.md) | 1.0 | `public function stripTags(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $allowed_tags = ''): self`<br>Strip HTML and PHP tags from the string. You can specify tags that should not be stripped by providing them in the $allowed_tags parameter. |
| [`stripAccents`](docs/x-string/methods/strip-accents.md) | 1.0 | `public function stripAccents(): self`<br>Remove accents from characters in the string. (e.g. é -> e, ñ -> n) |

### Affixing

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`ensurePrefix`](docs/x-string/methods/ensure-prefix.md) | 1.0 | `public function ensurePrefix(Newline\|Multibyte\|Byte\|Grapheme\|string $prefix): self`<br>Ensure the string starts with the specified prefix. If it doesn't, the prefix is added. |
| [`ensureSuffix`](docs/x-string/methods/ensure-suffix.md) | 1.0 | `public function ensureSuffix(Newline\|Multibyte\|Byte\|Grapheme\|string $suffix): self`<br>Ensure the string ends with the specified suffix. If it doesn't, the suffix is added. |
| [`removePrefix`](docs/x-string/methods/remove-prefix.md) | 1.0 | `public function removePrefix(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $prefix): self`<br>Remove the specified prefix from the string if it exists. If an array is provided it will act as an OR. |
| [`removeSuffix`](docs/x-string/methods/remove-suffix.md) | 1.0 | `public function removeSuffix(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $suffix): self`<br>Remove the specified suffix from the string if it exists. If an array is provided it will act as an OR. |
| [`togglePrefix`](docs/x-string/methods/toggle-prefix.md) | 1.0 | `public function togglePrefix(Newline\|Multibyte\|Byte\|Grapheme\|string $prefix): self`<br>Toggle the specified prefix on the string. If the string starts with the prefix, it is removed. If it doesn't, the prefix is added. |
| [`toggleSuffix`](docs/x-string/methods/toggle-suffix.md) | 1.0 | `public function toggleSuffix(Newline\|Multibyte\|Byte\|Grapheme\|string $suffix): self`<br>Toggle the specified suffix on the string. If the string ends with the suffix, it is removed. If it doesn't, the suffix is added. |
| [`hasPrefix`](docs/x-string/methods/has-prefix.md) | 1.0 | `public function hasPrefix(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $prefix): bool`<br>Check if the string starts with the specified prefix. If an array is provided it will act as an OR. |
| [`hasSuffix`](docs/x-string/methods/has-suffix.md) | 1.0 | `public function hasSuffix(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $suffix): bool`<br>Check if the string ends with the specified suffix. If an array is provided it will act as an OR. |

### Other methods

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`split`](docs/x-string/methods/split.md) | 1.0 | `public function split(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $delimiter, null\| int $limit = null): array`<br>Split the string into an array using the specified delimiter. If $limit is provided, it limits the number of splits. If an array is provided it will act as an OR. |
| [`explode`](docs/x-string/methods/explode.md) | 1.0 | `public function explode(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $delimiter, null \| int $limit = null): array`<br>Alias for split(). |
| [`lines`](docs/x-string/methods/lines.md) | 1.0 | `public function lines(bool $trim = false, null\|int $limit = null): array`<br>Split the string into an array of lines. If $trim is true, it trims each line. If $limit is provided, it limits the number of lines returned. |
| [`words`](docs/x-string/methods/words.md) | 1.0 | `public function words(bool $trim = false, null\|int $limit = null): array`<br>Split the string into an array of words. If $trim is true, it trims each word. |
| [`betweenAll`](docs/x-string/methods/between-all.md) | 1.0 | `public function betweenAll(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $start, Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string $end, $reversed = false): array`<br>Get all substrings between two strings. If $reversed is true, it searches from the end of the string. If arrays are provided for `$start` and/or `$end`, it will search for the first value, then starting from that index, search for the next one, etc. when `$reversed` it searches backwards. |
| [`length`](docs/x-string/methods/length.md) | 1.0 | `public function length(): int`<br>Get the length of the string. *(Default **graphemes**.)* |
| [`byteLength`](docs/x-string/methods/byte-length.md) | 1.0 | `public function byteLength(): int`<br>Get the byte length of the string. |
| [`graphemeLength`](docs/x-string/methods/grapheme-length.md) | 1.0 | `public function graphemeLength(): int`<br>Get the grapheme length of the string. |
| [`wordCount`](docs/x-string/methods/word-count.md) | 1.0 | `public function wordCount(): int`<br>Get the number of words in the string. |
| [`lineCount`](docs/x-string/methods/line-count.md) | 1.0 | `public function lineCount(): int`<br>Get the number of lines in the string. |
| [`sentenceCount`](docs/x-string/methods/sentence-count.md) | 1.0 | `public function sentenceCount(): int`<br>Get the number of sentences in the string. |
| [`charAt`](docs/x-string/methods/char-at.md) | 1.0 | `public function charAt(int $index): string`<br>Get the character at a specific index in the string. *(Default **graphemes**.)* |
| [`contains`](docs/x-string/methods/contains.md) | 1.0 | `public function contains(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search): bool`<br>Check if the string contains a specific substring. If an array is provided it will act as an OR. |
| [`indexOf`](docs/x-string/methods/index-of.md) | 1.0 | `public function indexOf(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search, $reversed = false): false\|int`<br>Get the index of the first occurrence of a substring. If $reversed is true, it searches from the end of the string. If an array is provided it will act as an OR. **Returns the first (lowest) index found** among all candidates, or false if not found. |
| [`isEmpty`](docs/x-string/methods/is-empty.md) | 1.0 | `public function isEmpty($newline = true, $space = true, $tab = true): bool`<br>Check if the string is empty. By default it considers newlines, spaces and tabs as empty characters. You can disable checking for any of these by setting the relevant parameter to false. |
| [`startsWith`](docs/x-string/methods/starts-with.md) | 1.0 | `public function startsWith(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search): bool`<br>Check if the string starts with the specified substring. If an array is provided it will act as an OR. |
| [`endsWith`](docs/x-string/methods/ends-with.md) | 1.0 | `public function endsWith(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search): bool`<br>Check if the string ends with the specified substring. If an array is provided it will act as an OR. |
| [`equals`](docs/x-string/methods/equals.md) | 1.0 | `public function equals(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $string, bool $case_sensitive = true): bool`<br>Check if the string is equal to another string. You can specify whether the comparison should be case-sensitive. Default is true. If an array is provided it will act as an OR. |
| [`countOccurrences`](docs/x-string/methods/count-occurrences.md) | 1.0 | `public function countOccurrences(Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string\|array<Newline\|Regex\|Multibyte\|Byte\|Grapheme\|string> $search): int`<br>Count the number of occurrences of a substring in the string. If an array is provided it will act as an OR. |
| [`matchAll`](docs/x-string/methods/match-all.md) | 1.0 | `public function matchAll(Regex\|array<Regex> $pattern, false\|int $limit = false, array\|int\|null $flags = PREG_PATTERN_ORDER): array`<br>Match all occurrences of a regex pattern in the string. You can limit the number of matches by setting $limit. The $flags parameter determines the format of the returned array (default is PREG_PATTERN_ORDER). |

### Encoding methods

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`transliterate`](docs/x-string/methods/transliterate.md) | 1.0 | `public function transliterate(string $to = 'ASCII//TRANSLIT'): self`<br>Transliterate the string to a different character set. Default is 'ASCII//TRANSLIT'. |
| [`toEncoding`](docs/x-string/methods/to-encoding.md) | 1.0 | `public function toEncoding(string $to_encoding, null\|string $from_encoding = null): self`<br>Convert the string to a different encoding. If $from_encoding is not provided, it tries to detect the current encoding. |
| [`detectEncoding`](docs/x-string/methods/detect-encoding.md) | 1.0 | `public function detectEncoding(array $encodings = ['UTF-8', 'ISO-8859-1', 'ASCII']): string\|false`<br>Detect the encoding of the string from a list of possible encodings. Returns the detected encoding or false if none matched. |
| [`isValidEncoding`](docs/x-string/methods/is-valid-encoding.md) | 1.0 | `public function isValidEncoding(null\|string $encoding = null): bool`<br>Check if the string is valid in the specified encoding. If $encoding is not provided, it uses the current encoding of the string. |
| [`isAscii`](docs/x-string/methods/is-ascii.md) | 1.0 | `public function isAscii(): bool`<br>Check if the string contains only ASCII characters. |
| [`isUtf8`](docs/x-string/methods/is-utf8.md) | 1.0 | `public function isUtf8(): bool`<br>Check if the string is valid UTF-8. |
| [`toUtf8`](docs/x-string/methods/to-utf8.md) | 1.0 | `public function toUtf8(nul \|string $from_encoding = null): self`<br>Convert the string to UTF-8 encoding. If $from_encoding is not provided, it tries to detect the current encoding. |
| [`toAscii`](docs/x-string/methods/to-ascii.md) | 1.0 | `public function toAscii(null\|string $from_encoding = null): self`<br>Convert the string to ASCII encoding. If $from_encoding is not provided, it tries to detect the current encoding. |

### Encryption and Hashing

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`base64Encode`](docs/x-string/methods/base64-encode.md) | 1.0 | `public function base64Encode(): self`<br>Base64-encode the string. |
| [`base64Decode`](docs/x-string/methods/base64-decode.md) | 1.0 | `public function base64Decode(): self`<br>Base64-decode the string. |
| [`md5`](docs/x-string/methods/md5.md) | 1.0 | `public function md5(bool $raw_output = false): self`<br>Get the MD5 hash of the string. If $raw_output is true, it returns the raw binary format. |
| [`sha1`](docs/x-string/methods/sha1.md) | 1.0 | `public function sha1(bool $raw_output = false): self`<br>Get the SHA-1 hash of the string. If $raw_output is true, it returns the raw binary format. |
| [`sha256`](docs/x-string/methods/sha256.md) | 1.0 | `public function sha256(bool $raw_output = false): self`<br>Get the SHA-256 hash of the string. If $raw_output is true, it returns the raw binary format. |
| [`crypt`](docs/x-string/methods/crypt.md) | 1.0 | `public function crypt(string $salt): self`<br>Hash the string using the crypt() function with the specified salt. |
| [`passwordHash`](docs/x-string/methods/password-hash.md) | 1.0 | `public function passwordHash(int $algo = PASSWORD_BCRYPT, array $options = []): self`<br>Hash the string using password_hash() with the specified algorithm and options. Default is PASSWORD_BCRYPT. |
| [`passwordVerify`](docs/x-string/methods/password-verify.md) | 1.0 | `public function passwordVerify(string $hash): bool`<br>Verify the string against a given hash using password_verify(). Returns true if the string matches the hash, false otherwise. |
| [`encrypt`](docs/x-string/methods/encrypt.md) | 1.0 | `public function encrypt(string $password, string $cipher = 'sodium_xchacha20'): self`<br>Encrypt the string using authenticated encryption (AEAD). If $cipher === `'sodium_xchacha20'` and libsodium is available, use `XChaCha20-Poly1305` with Argon2id key derivation; otherwise use OpenSSL AES-256-GCM. Returns a versioned envelope (salt + nonce + tag + algorithm id + ciphertext) encoded as a string.. |
| [`decrypt`](docs/x-string/methods/decrypt.md) | 1.0 | `public function decrypt(string $password, string $cipher = 'sodium_xchacha20'): self`<br>Decrypt a ciphertext produced by `encrypt()`. Verifies integrity (auth tag). Supports `sodium_xchacha20` (libsodium) or `aes-256-gcm` (OpenSSL). Throws on invalid password, tampering, or unsupported version. |

### Codecs

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`htmlEscape`](docs/x-string/methods/html-escape.md) | 1.0 | `public function htmlEscape(int $flags = ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML5, string $encoding = 'UTF-8'): self`<br>Escape HTML special characters in the string. You can specify flags and encoding. |
| [`htmlUnescape`](docs/x-string/methods/html-unescape.md) | 1.0 | `public function htmlUnescape(): self`<br>Unescape HTML special characters in the string. |
| [`urlEncode`](docs/x-string/methods/url-encode.md) | 1.0 | `public function urlEncode(bool $raw = false): self`<br>URL-encode the string. If $raw is true, it uses rawurlencode(). |
| [`urlDecode`](docs/x-string/methods/url-decode.md) | 1.0 | `public function urlDecode(bool $raw = false): self`<br>URL-decode the string. If $raw is true, it uses rawurldecode(). |
| [`nl2br`](docs/x-string/methods/nl2br.md) | 1.0 | `public function nl2br(bool $is_xhtml = true): self`<br>Convert newlines to HTML `<br>` tags. If $is_xhtml is true, it uses <br /> for XHTML compliance. |
| [`br2nl`](docs/x-string/methods/br2nl.md) | 1.0 | `public function br2nl(): self`<br>Convert HTML `<br>` tags to newlines. |


## Newline class methods (all static)

Used to tell search arguments in other methods that you want to search for newlines.

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/newline/methods/new.md) | 1.0 | `public static function new(null\|Multibyte\|Byte\|Grapheme\|string $newline = null): self`<br>Create a new Newline instance. Default newline is *any*. |
| [`startsWith`](docs/newline/methods/starts-with.md) | 1.0 | `public function startsWith(null\|Multibyte\|Byte\|Grapheme\|string $string, bool $trim = false): self`<br>Creates a newline that starts with `$string`. Can be used to check if the newline starts with the specified string. |
| [`endsWith`](docs/newline/methods/ends-with.md) | 1.0 | `public function endsWith(null\|Multibyte\|Byte\|Grapheme\|string $string, bool $trim = false): self`<br>Creates a newline that ends with `$string`. Can be used to check if the newline ends with the specified string. |
| [`contains`](docs/newline/methods/contains.md) | 1.0 | `public function contains(null\|Multibyte\|Byte\|Grapheme\|string $string): self`<br>Used to check if a newline contains the specified string. |
| [`equals`](docs/newline/methods/equals.md) | 1.0 | `public function equals(null\|Multibyte\|Byte\|Grapheme\|string $string): self`<br>Used to check if a newline is equal to the specified string. |

## Regex class methods (all static)

Used to tell search arguments in other methods that you want to search for a regex pattern.

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/regex/methods/new.md) | 1.0 | `public static function new(string $pattern, int $modifiers = 0): self`<br>Create a new Pattern instance. $modifiers is a bitmask of regex modifiers (ex. Pattern::MODIFIER_CASE_INSENSITIVE). |

## Byte class methods (all static)

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/byte/methods/new.md) | 1.0 | `public static function new(string $pattern = '', int $modifiers = 0): self`<br>Create a new Byte instance. $modifiers is a bitmask of byte string modifiers (ex. Byte::MODIFIER_CASE_INSENSITIVE). |

## Multibyte (codepoint) class methods (all static)

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/multibyte/methods/new.md) | 1.0 | `public static function new(string $pattern = '', int $modifiers = 0): self`<br>Create a new Multibyte instance. $modifiers is a bitmask of multibyte string modifiers (ex. Multibyte::MODIFIER_CASE_INSENSITIVE). |

## Grapheme class methods (all static)

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/grapheme/methods/new.md) | 1.0 | `public static function new(string $pattern = '', int $modifiers = 0): self`<br>Create a new Grapheme instance. $modifiers is a bitmask of graph modifiers (ex. Grapheme::MODIFIER_CASE_INSENSITIVE). |

## XStringType (factory) class

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`XStringType::newline()`](docs/x-string-type/methods/newline.md) | 1.0 | `public static function newline(null\|Multibyte\|Byte\|Grapheme\|string $newline = null): Newline`<br>Create a new Newline instance. Default newline is *any*. |
| [`XStringType::regex()`](docs/x-string-type/methods/regex.md) | 1.0 | `public static function regex(string $pattern, int $modifiers = 0): Regex`<br>Create a new Regex instance. $modifiers is a bitmask of regex modifiers (ex. Pattern::MODIFIER_CASE_INSENSITIVE). |
| [`XStringType::byte()`](docs/x-string-type/methods/byte.md) | 1.0 | `public static function byte(string $pattern = '', int $modifiers = 0): Byte`<br>(default behavior) Create a new Byte instance. $modifiers is a bitmask of byte string modifiers (ex. Byte::MODIFIER_CASE_INSENSITIVE). |
| [`XStringType::multibyte()`](docs/x-string-type/methods/multibyte.md) | 1.0 | `public static function multibyte(string $pattern = '', int $modifiers = 0): Multibyte`<br>Create a new Multibyte instance. $modifiers is a bitmask of multibyte string modifiers (ex. Multibyte::MODIFIER_CASE_INSENSITIVE). |
| [`XStringType::grapheme()`](docs/x-string-type/methods/grapheme.md) | 1.0 | `public static function grapheme(string $pattern = '', int $modifiers = 0): Grapheme`<br>Create a new Grapheme instance. $modifiers is a bitmask of graph modifiers (ex. Grapheme::MODIFIER_CASE_INSENSITIVE). |

# Examples

Here are some examples of how to use the `XString` class:

## Basic Usage

<!-- test:basic -->
```php
use Orryv\XString;

// Create a new XString instance
$str = new XString(" Hello, World! \n");
#Test: self::assertTrue($str instanceof XString);
#Test: self::assertEquals(" Hello, World! \n", (string)$str);

// Trim whitespace
$trimmed = $str->trim();
echo $trimmed; // Outputs: "Hello, World!"
#Test: self::assertEquals("Hello, World!", (string)$trimmed);
```

## Newlines

<!-- test:newlines -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$str = <<<EOT
 Line1 - blabla
Hello, World!
EOT;

$string = new XString($str);
#Test: self::assertEquals($str, (string)$string);

// Remove first line (one way to do it)
$string->after(Newline::new()->startsWith('Line1', trim:true));
//Same as: $string->after(XStringType::newline()->startsWith('Line1', trim:true));
echo $string; // Outputs: "Hello, World!"
#Test: self::assertEquals("Hello, World!", (string)$string);
```