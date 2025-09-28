# XArray

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
| [`new`](docs/x-array/methods/new.md) | 1.0 | `public static function new(string $data = '', $use_multibyte = false): self`<br>Create a new XString instance. |

### Generation

Will throw if internal string is not empty (new($data) with $data not empty.)

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`rand`](docs/x-array/methods/rand.md) | 1.0 | `public static function rand(int $length, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): self`<br>Create a random string of a given length using the provided characters. |
| [`uuid`](docs/x-array/methods/uuid.md) | 1.0 | `public static function uuid($version = 4): self`<br>Create a UUID (Universally Unique Identifier) of the specified version (1, 3, 4, or 5). |

### Manipulation

| Method | Version | Signature & Description |
| --- | --- | --- |
| [`append`](docs/x-array/methods/append.md) | 1.0 | `public function append(array\|string $data): self`<br>Append a string to the end of the current string. |
| [`prepend`](docs/x-array/methods/prepend.md) | 1.0 | `public function prepend(array\|string $data): self`<br>Prepend a string to the beginning of the current string. |
| [`toUpper`](docs/x-array/methods/to-upper.md) | 1.0 | `public function toUpperCase(): self`<br>Convert the string to upper case. |
| [`toLower`](docs/x-array/methods/to-lower.md) | 1.0 | `public function toLowerCase(): self`<br>Convert the string to lower case. |
| [`trim`](docs/x-array/methods/trim.md) | 1.0 | `public function trim($newline = true, $space = true, $tab = true): self`<br>Trim the string. By default it trims newlines, spaces and tabs. You can disable trimming of any of these by setting the relevant parameter to false. |
| [`replace`](docs/x-array/methods/replace.md) | 1.0 | `public function replace(string $search, string $replace, null \|int $limit = null, $reversed = false): self`<br>Replace occurrences of a string with another string. By default it replaces all occurrences, but you can limit the number of replacements by setting the $limit parameter. If $reversed is true, it replaces from the end of the string. |
| [`substr`](docs/x-array/methods/substring.md) | 1.0 | `public function substr(int $start, null \| int $length = null): self`<br>Get a substring of the string. If $length is not provided, it returns the substring from $start to the end of the string. |
| [`repeat`](docs/x-array/methods/repeat.md) | 1.0 | `public function repeat(int $times): self`<br>Repeat the string a number of times. |
| [`reverse`](docs/x-array/methods/reverse.md) | 1.0 | `public function reverse(): self`<br>Reverse the string. |
| [`shuffle`](docs/x-array/methods/shuffle.md) | 1.0 | `public function shuffle(): self`<br>Shuffle the characters in the string randomly. |
| [`slug`](docs/x-array/methods/slug.md) | 1.0 | `public function slug(string $separator = '-'): self`<br>Convert the string to a URL-friendly "slug". Replaces spaces and special characters with the specified separator (default is '-'). |
| [`insertAtInterval`](docs/x-array/methods/insert-at-interval.md) | 1.0 | `public function insertAtInterval(string $insert, int $interval): self`<br>Insert a string at regular intervals in the current string. |
| [`between`](docs/x-array/methods/between.md) | 1.0 | `public function between(string $start, string $end, $reversed = false): self`<br>Get the substring between two strings. If $reversed is true, it searches from the end of the string. |

### Other methods
| [`split`](docs/x-array/methods/split.md) | 1.0 | `public function split(string $delimiter, null \| int $limit = null): array`<br>Split the string into an array using the specified delimiter. If $limit is provided, it limits the number of splits. |
| [`betweenAll`](docs/x-array/methods/between-all.md) | 1.0 | `public function betweenAll(string $start, string $end, $reversed = false): array`<br>Get all substrings between two strings. If $reversed is true, it searches from the end of the string. |
| [`length`](docs/x-array/methods/length.md) | 1.0 | `public function length(): int`<br>Get the length of the string. |
| [`contains`](docs/x-array/methods/contains.md) | 1.0 | `public function contains(string $search): bool`<br>Check if the string contains a specific substring. |
| [`indexOf`](docs/x-array/methods/index-of.md) | 1.0 | `public function indexOf(string $search, $reversed = false): int`<br>Get the index of the first occurrence of a substring. If $reversed is true, it searches from the end of the string. Returns -1 if not found. |
| [`isEmpty`](docs/x-array/methods/is-empty.md) | 1.0 | `public function isEmpty($newline = true, $space = true, $tab = true): bool`<br>Check if the string is empty. By default it considers newlines, spaces and tabs as empty characters. You can disable checking for any of these by setting the relevant parameter to false. |
