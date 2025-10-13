# XString::br2nl()

## Table of Contents
- [XString::br2nl()](#xstringbr2nl)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Convert simple <br> tags into newlines](#convert-simple-br-tags-into-newlines)
    - [Handle self-closing and uppercase variations](#handle-self-closing-and-uppercase-variations)
    - [Consecutive breaks turn into multiple newlines](#consecutive-breaks-turn-into-multiple-newlines)
    - [Trailing breaks produce trailing newlines](#trailing-breaks-produce-trailing-newlines)
    - [Mode changes still work as expected](#mode-changes-still-work-as-expected)
    - [Empty strings stay empty](#empty-strings-stay-empty-4)
    - [Original instance remains unchanged](#original-instance-remains-unchanged-4)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function br2nl(): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Performs the inverse of `nl2br()` by converting HTML `<br>` tags (including self-closing variants) into platform-specific newline sequences. The resulting string is wrapped in a new immutable `XString` instance.

## Important notes and considerations

- **Case-insensitive matching.** Recognises `<br>`, `<BR>`, `<br/>`, and `<br />` forms.
- **Whitespace tolerant.** Optional whitespace before the closing slash is ignored.
- **Immutable transformation.** The original string is never modified.

## Parameters

This method does not accept parameters.

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `XString` where `<br>` tags are replaced by newline characters. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Convert simple <br> tags into newlines

<!-- test:br2nl-basic -->
```php
use Orryv\XString;

$result = XString::new('Line 1<br>Line 2')->br2nl();

#Test: self::assertSame('Line 1' . PHP_EOL . 'Line 2', (string) $result);
```

### Handle self-closing and uppercase variations

<!-- test:br2nl-variations -->
```php
use Orryv\XString;

$result = XString::new('One<br />Two<BR/>Three')->br2nl();

#Test: self::assertSame('One' . PHP_EOL . 'Two' . PHP_EOL . 'Three', (string) $result);
```

### Consecutive breaks turn into multiple newlines

<!-- test:br2nl-multiple -->
```php
use Orryv\XString;

$result = XString::new('A<br><br>B')->br2nl();

#Test: self::assertSame('A' . PHP_EOL . PHP_EOL . 'B', (string) $result);
```

### Trailing breaks produce trailing newlines

<!-- test:br2nl-trailing -->
```php
use Orryv\XString;

$result = XString::new('End<br>')->br2nl();

#Test: self::assertSame('End' . PHP_EOL, (string) $result);
```

### Mode changes still work as expected

<!-- test:br2nl-mode -->
```php
use Orryv\XString;

$value = XString::new('Plain text')->withMode('graphemes');
$result = $value->br2nl();

#Test: self::assertSame('Plain text', (string) $result);
```

### Empty strings stay empty

<!-- test:br2nl-empty -->
```php
use Orryv\XString;

$result = XString::new('')->br2nl();

#Test: self::assertSame('', (string) $result);
```

### Original instance remains unchanged

<!-- test:br2nl-immutable -->
```php
use Orryv\XString;

$value = XString::new('Keep<br>Original');
$value->br2nl();

#Test: self::assertSame('Keep<br>Original', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::br2nl` | `public function br2nl(): self` — Replace `<br>` tags (case-insensitive, including self-closing forms) with newline characters without altering the source instance. |
