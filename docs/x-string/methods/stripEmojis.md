# XString::stripEmojis()

## Table of Contents
- [XString::stripEmojis()](#xstringstripemojis)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Remove standalone emojis](#remove-standalone-emojis)
    - [Remove zero-width-joiner emoji sequences](#remove-zero-width-joiner-emoji-sequences)
    - [Remove regional-indicator flag emojis](#remove-regional-indicator-flag-emojis)
    - [Remove keycap emojis](#remove-keycap-emojis)
    - [Plain text remains untouched and original is immutable](#plain-text-remains-untouched-and-original-is-immutable)
    - [Empty strings stay empty](#empty-strings-stay-empty)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function stripEmojis(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✓ | Public |

## Description

Removes all emoji grapheme clusters from the current string. The method analyses the string as grapheme clusters so that
multi-codepoint emoji such as ZWJ sequences, skin-tone variants, regional-indicator flags, and keycap emojis are removed in one
step without leaving combining marks behind.

## Important notes and considerations

- **Cluster aware.** Emoji detection happens per grapheme cluster, ensuring entire sequences (e.g. 👩‍💻 or family emojis) are
  removed together.
- **Keycap & flag support.** Regional-indicator flags and keycap combinations like `1️⃣` are treated as emoji clusters.
- **Text is preserved.** Non-emoji characters remain unchanged, making the method useful for sanitising user-facing strings.
- **Immutable.** A new `XString` instance is returned; the original object is never modified.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` with all emoji grapheme clusters removed. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Remove standalone emojis

<!-- test:strip-emojis-basic -->
```php
use Orryv\XString;

$value = XString::new('Launch 🚀 success 🎉');
$result = $value->stripEmojis();

#Test: self::assertSame('Launch  success ', (string) $result);
```

### Remove zero-width-joiner emoji sequences

<!-- test:strip-emojis-zwj -->
```php
use Orryv\XString;

$value = XString::new('Developers 👩‍💻 collaborate 👨‍💻 daily');
$result = $value->stripEmojis();

#Test: self::assertSame('Developers  collaborate  daily', (string) $result);
```

### Remove regional-indicator flag emojis

<!-- test:strip-emojis-flags -->
```php
use Orryv\XString;

$value = XString::new('Teams 🇺🇸 vs 🇯🇵 in finals');
$result = $value->stripEmojis();

#Test: self::assertSame('Teams  vs  in finals', (string) $result);
```

### Remove keycap emojis

<!-- test:strip-emojis-keycap -->
```php
use Orryv\XString;

$value = XString::new('Press 1️⃣ to continue, 0️⃣ to exit.');
$result = $value->stripEmojis();

#Test: self::assertSame('Press  to continue,  to exit.', (string) $result);
```

### Plain text remains untouched and original is immutable

<!-- test:strip-emojis-immutable -->
```php
use Orryv\XString;

$original = XString::new('Important notice #42');
$processed = $original->stripEmojis();

#Test: self::assertSame('Important notice #42', (string) $processed);
#Test: self::assertSame('Important notice #42', (string) $original);
```

### Empty strings stay empty

<!-- test:strip-emojis-empty -->
```php
use Orryv\XString;

$result = XString::new('')->stripEmojis();

#Test: self::assertSame('', (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::stripEmojis` | `public function stripEmojis(): self` — Remove all emoji grapheme clusters while leaving non-emoji text intact. |
