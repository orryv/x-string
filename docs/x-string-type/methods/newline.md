# XStringType::newline()

## Table of Contents
- [XStringType::newline()](#xstringtypenewline)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Create a newline adapter with the default separator](#create-a-newline-adapter-with-the-default-separator)
    - [Use a custom newline sequence](#use-a-custom-newline-sequence)
    - [Chain newline constraints](#chain-newline-constraints)
    - [Combine with `XString::new()`](#combine-with-xstringnew)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function newline(?string $newline = null): Newline
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv` | Static factory | ✓ | Public |

## Description

Create a new immutable `Newline` adapter instance. The helper mirrors `Newline::new()` while providing a concise factory that
keeps fluent pipelines readable when working with the type-aware `XString` helpers.

## Important notes and considerations

- **Delegates to `Newline::new()`.** All behaviour is provided by the underlying `Newline` factory.
- **Default separator.** When no argument is provided, the adapter represents the platform newline (`PHP_EOL`).
- **Immutable adapters.** Any chained constraints (`startsWith()`, `contains()`, etc.) return new `Newline` instances without
  mutating earlier references.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$newline` | `null` | `null\|string` | Optional newline sequence. When `null`, defaults to `PHP_EOL`. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `Newline` | ✓ | Fresh `Newline` adapter configured with the requested separator. |

## Thrown exceptions

This method does not throw additional exceptions.

## Examples

### Create a newline adapter with the default separator

<!-- test:xstring-type-newline-default -->
```php
use Orryv\XStringType;
use Orryv\XString\Newline;

$newline = XStringType::newline();
#Test: self::assertInstanceOf(Newline::class, $newline);
#Test: self::assertSame(PHP_EOL, (string) $newline);
```

### Use a custom newline sequence

<!-- test:xstring-type-newline-custom -->
```php
use Orryv\XStringType;

$windows = XStringType::newline("\r\n");
#Test: self::assertSame("\r\n", (string) $windows);
```

### Chain newline constraints

<!-- test:xstring-type-newline-constraints -->
```php
use Orryv\XStringType;

$newline = XStringType::newline()->startsWith('  Line1', trim: true);
$config = $newline->getLineConstraint();
#Test: self::assertSame(['type' => 'starts_with', 'needle' => 'Line1', 'trim' => true], $config);
```

### Combine with `XString::new()`

<!-- test:xstring-type-newline-combine -->
```php
use Orryv\XString;
use Orryv\XStringType;

$value = XString::new([
    'Header',
    XStringType::newline(),
    'Body',
]);
#Test: self::assertSame('Header' . PHP_EOL . 'Body', (string) $value);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XStringType::newline` | `public static function newline(?string $newline = null): Newline` — Create a `Newline` adapter using the provided separator (defaults to `PHP_EOL`). |
