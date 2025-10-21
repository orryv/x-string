# XString::toBool()

## Table of Contents
- [XString::toBool()](#xstringtobool)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Interpret affirmative words](#interpret-affirmative-words)
    - [Interpret negative words](#interpret-negative-words)
    - [Interpret numeric strings](#interpret-numeric-strings)
    - [Empty strings resolve to false](#empty-strings-resolve-to-false)
    - [Reject ambiguous input](#reject-ambiguous-input)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function toBool(): bool
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Instance | ✗ | Public |

## Description

Interpret the string as a boolean value. Recognises common affirmative/negative words and numeric strings. Positive numeric
values map to `true`, zero/negative numbers map to `false`, and empty strings default to `false`.

## Important notes and considerations

- **Extensive vocabulary.** Words such as `"true"`, `"yes"`, `"ok"`, `"success"`, `"no"`, `"failed"`, `"off"`, and variants are recognised (case-insensitive).
- **Numeric semantics.** Numbers greater than zero evaluate to `true`; zero or negatives evaluate to `false`.
- **Ambiguity guarded.** Unknown tokens raise an `InvalidValueConversionException` instead of guessing.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `bool` | ✗ | Boolean interpretation of the string. |

## Thrown exceptions

| Exception | Reason |
| --- | --- |
| `Orryv\\XString\\Exceptions\\InvalidValueConversionException` | The value cannot be confidently interpreted as boolean. |

## Examples

### Interpret affirmative words

<!-- test:to-bool-affirmative -->
```php
use Orryv\XString;

$value = XString::new('YeS');

#Test: self::assertTrue($value->toBool());
```

### Interpret negative words

<!-- test:to-bool-negative -->
```php
use Orryv\XString;

$value = XString::new('failed');

#Test: self::assertFalse($value->toBool());
```

### Interpret numeric strings

<!-- test:to-bool-numeric -->
```php
use Orryv\XString;

$positive = XString::new('2');
$negative = XString::new('-1');

#Test: self::assertTrue($positive->toBool());
#Test: self::assertFalse($negative->toBool());
```

### Empty strings resolve to false

<!-- test:to-bool-empty -->
```php
use Orryv\XString;

$value = XString::new("   ");

#Test: self::assertFalse($value->toBool());
```

### Reject ambiguous input

<!-- test:to-bool-ambiguous -->
```php
use Orryv\XString;
use Orryv\XString\Exceptions\InvalidValueConversionException;

$value = XString::new('perhaps');

#Test: $this->expectException(InvalidValueConversionException::class);
$value->toBool();
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::toBool` | `public function toBool(): bool` — Interpret the string as a boolean using a rich vocabulary and numeric semantics, throwing when the intent is unclear. |
