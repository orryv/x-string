# XString

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

A class to manipulate strings. Uses __toString() to convert to string when needed.

### Setup

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/x-array/methods/new.md) | 1.0 | `public static function new(string $data = '', $use_multibyte = false, $use_grapheme = false): self`<br>Create a new instance of XString. If $use_multibyte is true, it will use multibyte string functions. If $use_grapheme is true, it will use grapheme string functions. Note: if both are true, grapheme functions will be used. |

### Generation

Will throw if internal string is not empty (new($data) with $data not empty.)

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`rand`](docs/x-array/methods/rand.md) | 1.0 | `public static function rand(int $length, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): self`<br>Create a random string of a given length using the provided characters. |
| [`randNumber`](docs/x-array/methods/rand-int.md) | 1.0 | `public static function randInt(int $length, int $int_min = 0, int $int_max = 9): self`<br>Create a random integer between the specified minimum and maximum values (inclusive). |
| [`randLower`](docs/x-array/methods/rand-lower.md) | 1.0 | `public static function randLower(int $length, $include_numbers = false): self`<br>Create a random lowercase string of a given length. If $include_numbers is true, numbers will be included in the string. |
| [`randUpper`](docs/x-array/methods/rand-upper.md) | 1.0 | `public static function randUpper(int $length, $include_numbers = false): self`<br>Create a random uppercase string of a given length. If $include_numbers is true, numbers will be included in the string. |
| [`randAlpha`](docs/x-array/methods/rand-alpha.md) | 1.0 | `public static function randAlpha(int $length): self`<br>Create a random alphabetic string of a given length (both uppercase and lowercase). |
| [`randHex`](docs/x-array/methods/rand-hex.md) | 1.0 | `public static function randHex(int $length): self`<br>Create a random hexadecimal string of a given length. |
| [`randBase64`](docs/x-array/methods/rand-base64.md) | 1.0 | `public static function randBase64(int $length): self`<br>Create a random Base64 string of a given length. |
| [`randBase62`](docs/x-array/methods/rand-base62.md) | 1.0 | `public static function randBase62(int $length): self`<br>Create a random Base62 string of a given length. |
| [`uuid`](docs/x-array/methods/uuid.md) | 1.0 | `public static function uuid($version = 4): self`<br>Create a UUID (Universally Unique Identifier) of the specified version (1, 3, 4, or 5). |
| ['implode'](docs/x-array/methods/implode.md) | 1.0 | `public static function implode(array $data, string $glue = ''): self`<br>Join array elements into a single string with an optional glue string between elements. |
| [`join`](docs/x-array/methods/join.md) | 1.0 | `public static function join(array $data, string $glue = ''): self`<br>Alias for implode(). |
| [`fromFile`](docs/x-array/methods/from-file.md) | 1.0 | `public static function fromFile(string $file_path, null \| int $length = null, null \| int $offset = 0, $use_multibyte = false, $use_grapheme = false): self`<br>Create a new instance of XString from the contents of a file. You can specify the length and offset to read from the file. If $use_multibyte is true, it will use multibyte string functions. If $use_grapheme is true, it will use grapheme string functions. Note: if both are true, grapheme functions will be used. |

### Manipulation

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`append`](docs/x-array/methods/append.md) | 1.0 | `public function append(array\|string $data): self`<br>Append a string to the end of the current string. |
| [`prepend`](docs/x-array/methods/prepend.md) | 1.0 | `public function prepend(array\|string $data): self`<br>Prepend a string to the beginning of the current string. |
| [`toUpper`](docs/x-array/methods/to-upper.md) | 1.0 | `public function toUpperCase(): self`<br>Convert the string to upper case. |
| [`ucfirst`](docs/x-array/methods/ucfirst.md) | 1.0 | `public function ucfirst(): self`<br>Convert the first character of the string to upper case. |
| [`lcfirst`](docs/x-array/methods/lcfirst.md) | 1.0 | `public function lcfirst(): self`<br>Convert the first character of the string to lower case. |
| [`toLower`](docs/x-array/methods/to-lower.md) | 1.0 | `public function toLowerCase(): self`<br>Convert the string to lower case. |
| [`trim`](docs/x-array/methods/trim.md) | 1.0 | `public function trim($newline = true, $space = true, $tab = true): self`<br>Trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`ltrim`](docs/x-array/methods/ltrim.md) | 1.0 | `public function ltrim($newline = true, $space = true, $tab = true): self`<br>Left trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`rtrim`](docs/x-array/methods/rtrim.md) | 1.0 | `public function rtrim($newline = true, $space = true, $tab = true): self`<br>Right trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`replace`](docs/x-array/methods/replace.md) | 1.0 | `public function replace(Regex\|NewLine\|string $search, string $replace, null \|int $limit = null, $reversed = false): self`<br>Replace occurrences of a string with another string. By default it replaces all occurrences, but you can limit the number of replacements by setting the $limit parameter. If $reversed is true, it replaces from the end of the string. |
| [`replaceFirst`](docs/x-array/methods/replace-first.md) | 1.0 | `public function replaceFirst(Regex\|NewLine\|string $search, string $replace): self`<br>Replace the first occurrence of a string with another string. |
| [`replaceLast`](docs/x-array/methods/replace-last.md) | 1.0 | `public function replaceLast(Regex\|NewLine\|string $search, string $replace): self`<br>Replace the last occurrence of a string with another string. |
| [`substr`](docs/x-array/methods/substring.md) | 1.0 | `public function substr(int $start, null \| int $length = null): self`<br>Get a substring of the string. If $length is not provided, it returns the substring from $start to the end of the string. |
| [`repeat`](docs/x-array/methods/repeat.md) | 1.0 | `public function repeat(int $times): self`<br>Repeat the string a number of times. |
| [`reverse`](docs/x-array/methods/reverse.md) | 1.0 | `public function reverse(): self`<br>Reverse the string. |
| [`shuffle`](docs/x-array/methods/shuffle.md) | 1.0 | `public function shuffle(): self`<br>Shuffle the characters in the string randomly. |
| [`slug`](docs/x-array/methods/slug.md) | 1.0 | `public function slug(string $separator = '-'): self`<br>Convert the string to a URL-friendly "slug". Replaces spaces and special characters with the specified separator (default is '-'). |
| [`insertAtInterval`](docs/x-array/methods/insert-at-interval.md) | 1.0 | `public function insertAtInterval(string $insert, int $interval): self`<br>Insert a string at regular intervals in the current string. |
| [`wrap`](docs/x-array/methods/wrap.md) | 1.0 | `public function wrap(string $before, string $after = null): self`<br>Wrap the string with the specified before and after strings. If $after is not provided, it uses the same value as $before. |
| [`indent`](docs/x-array/methods/indent.md) | 1.0 | `public function indent(int $spaces = 2, int $tabs = 0, int $lines = 0): self`<br>Indent the string by adding spaces and/or tabs at the beginning of each line. You can specify the number of lines to indent too. |
| ['outdent'](docs/x-array/methods/outdent.md) | 1.0 | `public function outdent(int $spaces = 2, int $tabs = 0, int $lines = 0): self`<br>Outdent the string by removing spaces and/or tabs from the beginning of each line. You can specify the number of lines to outdent too. |
| [`normalize`](docs/x-array/methods/normalize.md) | 1.0 | `public function normalize(int $form = Normalizer::FORM_C): self`<br>Normalize the string to a specific Unicode normalization form. Default is Normalizer::FORM_C. |
| [`pad`](docs/x-array/methods/pad.md) | 1.0 | `public function pad(int $length, string $pad_string = ' ', bool $left = true, bool $right = false): self`<br>Pad the string to a certain length with another string. You can specify whether to pad on the left, right, or both sides. Default is right padding. |
| [`center`](docs/x-array/methods/center.md) | 1.0 | `public function center(int $length, string $pad_string = ' '): self`<br>Center the string within a certain length by padding it on both sides with another string. |
| [`mask`](docs/x-array/methods/mask.md) | 1.0 | `public function mask(string $mask, string $mask_char = '#'): self`<br>Mask the string using a specified mask pattern. The mask pattern uses a special character (default is '#') to indicate where characters from the original string should be placed. |
| [`stripAccents`](docs/x-array/methods/strip-accents.md) | 1.0 | `public function stripAccents(): self`<br>Remove accents from characters in the string. |
| [`collapseWhitespace`](docs/x-array/methods/collapse-whitespace.md) | 1.0 | `public function collapseWhitespace($space = true, $tab = true, $newline = false): self`<br>Collapse consecutive whitespace characters into a single space. By default it collapses spaces, tabs and newlines. You can disable collapsing of any of these by setting the relevant parameter to false. |
| ['stripTags'](docs/x-array/methods/strip-tags.md) | 1.0 | `public function stripTags(string $allowed_tags = ''): self`<br>Strip HTML and PHP tags from the string. You can specify tags that should not be stripped by providing them in the $allowed_tags parameter. |
| [`between`](docs/x-array/methods/between.md) | 1.0 | `public function between(Regex\|NewLine\|string $start, NewLine\|string $end, $reversed = false, int $skip_start = 0, int $skip_end = 0): self`<br>Get the substring between two strings. If $reversed is true, it searches from the end of the string. You can skip a number of occurrences of the start and end strings by setting $skip_start and $skip_end. |
| [`before`](docs/x-array/methods/before.md) | 1.0 | `public function before(Regex\|NewLine\|string $search, $reversed = false, int $skip = 0): self`<br>Get the substring before a specific string. If $reversed is true, it searches from the end of the string. You can skip a number of occurrences of the search string by setting $skip. |
| [`after`](docs/x-array/methods/after.md) | 1.0 | `public function after(Regex\|NewLine\|string $search, $reversed = false, int $skip = 0): self`<br>Get the substring after a specific string. If $reversed is true, it searches from the end of the string. You can skip a number of occurrences of the search string by setting $skip. |
| [`toSnake](docs/x-array/methods/to-snake.md) | 1.0 | `public function toSnake(string $delimiter = ' '): self`<br>Convert the string to snake_case using the specified delimiter (default is ' '). |
| [`toKebab`](docs/x-array/methods/to-kebab.md) | 1.0 | `public function toKebab(): self`<br>Convert the string to kebab-case (lowercase words separated by hyphens). |
| [`toCamel`](docs/x-array/methods/to-camel.md) | 1.0 | `public function toCamel(bool $capitalize_first = false): self`<br>Convert the string to camelCase. If $capitalize_first is true, it converts to PascalCase (first letter capitalized). |
| [`toTitle`](docs/x-array/methods/to-title.md) | 1.0 | `public function toTitle(): self`<br>Convert the string to Title Case (first letter of each word capitalized). |
| [`toPascal`](docs/x-array/methods/to-pascal.md) | 1.0 | `public function toPascal(): self`<br>Convert the string to PascalCase (first letter of each word capitalized, no spaces). |
| [`match`](docs/x-array/methods/match.md) | 1.0 | `public function match(Regex $pattern): null \| array`<br>Match the string against a regex pattern. Returns an array of matches or null if no match is found. |
| [`removeEmojis`](docs/x-array/methods/remove-emojis.md) | 1.0 | `public function removeEmojis(): self`<br>Remove all emoji characters from the string. |

### Affixing

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`ensurePrefix`](docs/x-array/methods/ensure-prefix.md) | 1.0 | `public function ensurePrefix(NewLine\|string $prefix): self`<br>Ensure the string starts with the specified prefix. If it doesn't, the prefix is added. |
| [`ensureSuffix`](docs/x-array/methods/ensure-suffix.md) | | 1.0 | `public function ensureSuffix(NewLine\|string $suffix): self`<br>Ensure the string ends with the specified suffix. If it doesn't, the suffix is added. |
| [`removePrefix`](docs/x-array/methods/remove-prefix.md) | 1.0 | `public function removePrefix(Regex\|NewLine\|string $prefix): self`<br>Remove the specified prefix from the string if it exists. |
| [`removeSuffix`](docs/x-array/methods/remove-suffix.md) | 1.0 | `public function removeSuffix(Regex\|NewLine\|string $suffix): self`<br>Remove the specified suffix from the string if it exists. |
| [`togglePrefix`](docs/x-array/methods/toggle-prefix.md) | 1.0 | `public function togglePrefix(NewLine\|string $prefix): self`<br>Toggle the specified prefix on the string. If the string starts with the prefix, it is removed. If it doesn't, the prefix is added. |
| [`toggleSuffix`](docs/x-array/methods/toggle-suffix.md) | 1.0 | `public function toggleSuffix(NewLine\|string $suffix): self`<br>Toggle the specified suffix on the string. If the string ends with the suffix, it is removed. If it doesn't, the suffix is added. |

### Other methods

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`split`](docs/x-array/methods/split.md) | 1.0 | `public function split(Regex\|NewLine\|string $delimiter, null \| int $limit = null): array`<br>Split the string into an array using the specified delimiter. If $limit is provided, it limits the number of splits. |
| [`explode`](docs/x-array/methods/explode.md) | 1.0 | `public function explode(Regex\|NewLine\|string $delimiter, null \| int $limit = null): array`<br>Alias for split(). |
| [`lines`](docs/x-array/methods/lines.md) | 1.0 | `public function lines(bool $trim = false, null\|int $limit = null): array`<br>Split the string into an array of lines. If $trim is true, it trims each line. If $limit is provided, it limits the number of lines returned. |
| [`words`](docs/x-array/methods/words.md) | 1.0 | `public function words(bool $trim = false, null\|int $limit = null): array`<br>Split the string into an array of words. If $trim is true, it trims each word. If $limit is provided, it limits the number of words returned. |
| [`betweenAll`](docs/x-array/methods/between-all.md) | 1.0 | `public function betweenAll(Regex\|NewLine\|string $start, NewLine\|string $end, $reversed = false): array`<br>Get all substrings between two strings. If $reversed is true, it searches from the end of the string. |
| [`length`](docs/x-array/methods/length.md) | 1.0 | `public function length(): int`<br>Get the length of the string. |
| [`byteLength`](docs/x-array/methods/byte-length.md) | 1.0 | `public function byteLength(): int`<br>Get the byte length of the string. |
| [`graphemeLength`](docs/x-array/methods/grapheme-length.md) | 1.0 | `public function graphemeLength(): int`<br>Get the grapheme length of the string. |
| [`wordCount`](docs/x-array/methods/word-count.md) | 1.0 | `public function wordCount(): int`<br>Get the number of words in the string. |
| [`lineCount`](docs/x-array/methods/line-count.md) | 1.0 | `public function lineCount(): int`<br>Get the number of lines in the string. |
| [`sentenceCount`](docs/x-array/methods/sentence-count.md) | 1.0 | `public function sentenceCount(): int`<br>Get the number of sentences in the string. | |
| [`charAt`](docs/x-array/methods/char-at.md) | 1.0 | `public function charAt(int $index): string`<br>Get the character at a specific index in the string. |
| [`contains`](docs/x-array/methods/contains.md) | 1.0 | `public function contains(Regex\|NewLine\|array\|string $search): bool`<br>Check if the string contains a specific substring. |
| [`indexOf`](docs/x-array/methods/index-of.md) | 1.0 | `public function indexOf(Regex\|NewLine\|string $search, $reversed = false): int`<br>Get the index of the first occurrence of a substring. If $reversed is true, it searches from the end of the string. Returns -1 if not found. |
| [`isEmpty`](docs/x-array/methods/is-empty.md) | 1.0 | `public function isEmpty($newline = true, $space = true, $tab = true): bool`<br>Check if the string is empty. By default it considers newlines, spaces and tabs as empty characters. You can disable checking for any of these by setting the relevant parameter to false. |
| ['startsWith'](docs/x-array/methods/starts-with.md) | 1.0 | `public function startsWith(Regex\|NewLine\|string $search): bool`<br>Check if the string starts with the specified substring. |
| ['endsWith'](docs/x-array/methods/ends-with.md) | 1.0 | `public function endsWith(Regex\|NewLine\|string $search): bool`<br>Check if the string ends with the specified substring. |
| ['equals'](docs/x-array/methods/equals.md) | 1.0 | `public function equals(Regex\|NewLine\|string $string, bool $case_sensitive = true): bool`<br>Check if the string is equal to another string. You can specify whether the comparison should be case-sensitive. Default is true. |
| ['countOccurrences'](docs/x-array/methods/count-occurrences.md) | 1.0 | `public function countOccurrences(Regex\|NewLine\|string $search): int`<br>Count the number of occurrences of a substring in the string. |
| [`matchAll`](docs/x-array/methods/match-all.md) | 1.0 | `public function matchAll(Regex $pattern, false\|int $limit = false, array\|int\|null $flags = PREG_PATTERN_ORDER): array`<br>Match all occurrences of a regex pattern in the string. You can limit the number of matches by setting $limit. The $flags parameter determines the format of the returned array (default is PREG_PATTERN_ORDER). |

### Encoding methods

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`transliterate`](docs/x-array/methods/transliterate.md) | 1.0 | `public function transliterate(string $to = 'ASCII//TRANSLIT'): self`<br>Transliterate the string to a different character set. Default is 'ASCII//TRANSLIT'. |
| [`toEncoding`](docs/x-array/methods/to-encoding.md) | 1.0 | `public function toEncoding(string $to_encoding, null \| string $from_encoding = null): self`<br>Convert the string to a different encoding. If $from_encoding is not provided, it tries to detect the current encoding. |
| [`detectEncoding`](docs/x-array/methods/detect-encoding.md) | 1.0 | `public function detectEncoding(array $encodings = ['UTF-8', 'ISO-8859-1', 'ASCII']): string\|false`<br>Detect the encoding of the string from a list of possible encodings. Returns the detected encoding or false if none matched. |
| [`isValidEncoding`](docs/x-array/methods/is-valid-encoding.md) | 1.0 | `public function isValidEncoding(null \| string $encoding = null): bool`<br>Check if the string is valid in the specified encoding. If $encoding is not provided, it uses the current encoding of the string. |
| [`isAscii`](docs/x-array/methods/is-ascii.md) | 1.0 | `public function isAscii(): bool`<br>Check if the string contains only ASCII characters. |
| [`isUtf8`](docs/x-array/methods/is-utf8.md) | 1.0 | `public function isUtf8(): bool`<br>Check if the string is valid UTF-8. |
| [`toUtf8`](docs/x-array/methods/to-utf8.md) | 1.0 | `public function toUtf8(null \| string $from_encoding = null): self`<br>Convert the string to UTF-8 encoding. If $from_encoding is not provided, it tries to detect the current encoding. |
| [`toAscii`](docs/x-array/methods/to-ascii.md) | 1.0 | `public function toAscii(null \| string $from_encoding = null): self`<br>Convert the string to ASCII encoding. If $from_encoding is not provided, it tries to detect the current encoding. |

### Encryption and Hashing

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`base64Encode`](docs/x-array/methods/base64-encode.md) | 1.0 | `public function base64Encode(): self`<br>Base64-encode the string. |
| [`base64Decode`](docs/x-array/methods/base64-decode.md) | | 1.0 | `public function base64Decode(): self`<br>Base64-decode the string. |
| [`md5`](docs/x-array/methods/md5.md) | 1.0 | `public function md5(bool $raw_output = false): self`<br>Get the MD5 hash of the string. If $raw_output is true, it returns the raw binary format. |
| [`sha1`](docs/x-array/methods/sha1.md) | 1.0 | `public function sha1(bool $raw_output = false): self`<br>Get the SHA-1 hash of the string. If $raw_output is true, it returns the raw binary format. |
| [`sha256`](docs/x-array/methods/sha256.md) | 1.0 | `public function sha256(bool $raw_output = false): self`<br>Get the SHA-256 hash of the string. If $raw_output is true, it returns the raw binary format. |
| [`crypt`](docs/x-array/methods/crypt.md) | 1.0 | `public function crypt(string $salt): self`<br>Hash the string using the crypt() function with the specified salt. |
| [`passwordHash`](docs/x-array/methods/password-hash.md) | 1.0 | `public function passwordHash(int $algo = PASSWORD_BCRYPT, array $options = []): self`<br>Hash the string using password_hash() with the specified algorithm and options. Default is PASSWORD_BCRYPT. |
| [`passwordVerify`](docs/x-array/methods/password-verify.md) | 1.0 | `public function passwordVerify(string $hash): bool`<br>Verify the string against a given hash using password_verify(). Returns true if the string matches the hash, false otherwise. |

### Codecs

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`htmlEscape`](docs/x-array/methods/html-escape.md) | 1.0 | `public function htmlEscape(int $flags = ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML5, string $encoding = 'UTF-8'): self`<br>Escape HTML special characters in the string. You can specify flags and encoding. |
| [`htmlUnescape`](docs/x-array/methods/html-unescape.md) | | 1.0 | `public function htmlUnescape(): self`<br>Unescape HTML special characters in the string. |
| [`urlEncode`](docs/x-array/methods/url-encode.md) | 1.0 | `public function urlEncode(bool $raw = false): self`<br>URL-encode the string. If $raw is true, it uses rawurlencode(). |
| [`urlDecode`](docs/x-array/methods/url-decode.md) | 1.0 | `public function urlDecode(bool $raw = false): self`<br>URL-decode the string. If $raw is true, it uses rawurldecode(). |
| [`nl2br`](docs/x-array/methods/nl2br.md) | 1.0 | `public function nl2br(bool $is_xhtml = true): self`<br>Convert newlines to HTML <br> tags. If $is_xhtml is true, it uses <br /> for XHTML compliance. |
| [`br2nl`](docs/x-array/methods/br2nl.md) | 1.0 | `public function br2nl(): self`<br>Convert HTML <br> tags to newlines. |


## NewLine class methods (all static)

Used to tell search arguments in other methods that you want to search for newlines.

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`startsWith`](docs/x-array/methods/newline/starts-with.md) | 1.0 | `public static function startsWith(string $string): self`<br>Check if the newline starts with the specified string. |
| [`endsWith`](docs/x-array/methods/newline/ends-with.md) | 1.0 | `public static function endsWith(string $string): self`<br>Check if the newline ends with the specified string. |
| [`contains`](docs/x-array/methods/newline/contains.md) | 1.0 | `public static function contains(string $string): self`<br>Check if the newline contains the specified string. |
| [`equals`](docs/x-array/methods/newline/equals.md) | 1.0 | `public static function equals(string $string): self`<br>Check if the newline is equal to the specified string. |

## Regex class methods (all static)

Used to tell search arguments in other methods that you want to search for a regex pattern.

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`new`](docs/x-array/methods/regex/new.md) | 1.0 | `public static function new(string $pattern, int $modifiers = 0): self`<br>Create a new Pattern instance. $modifiers is a bitmask of regex modifiers (ex. Pattern::MODIFIER_CASE_INSENSITIVE). |