# Newline::new()

## Table of Contents
- [Newline::new()](#newlinenew)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Default newline uses the platform EOL](#default-newline-uses-the-platform-eol)
    - [Explicit Unix newline](#explicit-unix-newline)
    - [Explicit Windows newline](#explicit-windows-newline)
    - [Inject a newline fragment into XString::new()](#inject-a-newline-fragment-into-xstringnew)
    - [Empty string newline is allowed](#empty-string-newline-is-allowed)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function new(?string $newline = null): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Static factory | ✓ | Public |

## Description

Creates a `Newline` value object representing a specific end-of-line sequence. When used as part of `XString::new()` or other APIs that accept `Newline` fragments, it emits the requested newline exactly as provided. Omitting the argument defaults to the platform-specific newline (`PHP_EOL`).

## Important notes and considerations

- **Value object.** Instances are immutable; each call to `new()` yields a separate object encapsulating the chosen newline string.
- **Convenient defaults.** Falling back to `PHP_EOL` keeps behaviour portable across operating systems.
- **Works with XString composition.** Pass `Newline` instances to `XString::new()` or concatenation helpers to inject consistent line endings.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$newline` | `null\|string` | `null` | Custom newline sequence. When `null`, the platform default (`PHP_EOL`) is used. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | New `Newline` instance wrapping the requested newline characters. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Default newline uses the platform EOL

<!-- test:newline-new-default -->
```php
use Orryv\XString\Newline;

$newline = Newline::new();

#Test: self::assertSame(PHP_EOL, (string) $newline);
```

### Explicit Unix newline

<!-- test:newline-new-unix -->
```php
use Orryv\XString\Newline;

$newline = Newline::new("\n");

#Test: self::assertSame("\n", (string) $newline);
```

### Explicit Windows newline

<!-- test:newline-new-windows -->
```php
use Orryv\XString\Newline;

$newline = Newline::new("\r\n");

#Test: self::assertSame("\r\n", (string) $newline);
```

### Inject a newline fragment into XString::new()

<!-- test:newline-new-xstring -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$result = XString::new(['Header', Newline::new("\r\n"), 'Body']);

#Test: self::assertSame("Header\r\nBody", (string) $result);
```

### Empty string newline is allowed

<!-- test:newline-new-empty -->
```php
use Orryv\XString\Newline;

$newline = Newline::new('');

#Test: self::assertSame('', (string) $newline);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `Newline::new` | `public static function new(?string $newline = null): self` — Create an immutable newline value object defaulting to `PHP_EOL` but accepting any custom sequence. |
