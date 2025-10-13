# Newline::startsWith()

## Table of Contents
- [Newline::startsWith()](#newlinestartswith)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Replace lines that start with a prefix](#replace-lines-that-start-with-a-prefix)
    - [Trim indentation when matching](#trim-indentation-when-matching)
    - [Respect indentation when trim is disabled](#respect-indentation-when-trim-is-disabled)
    - [Immutable newline adapters carry configuration](#immutable-newline-adapters-carry-configuration)
    - [Reject empty prefixes](#reject-empty-prefixes)
    - [Match Unix text with a Windows newline adapter](#match-unix-text-with-a-windows-newline-adapter)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function startsWith(null|string $string, bool $trim = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Create a new `Newline` adapter that filters entire lines based on their leading text. The adapter keeps the underlying
newline sequence (e.g. `"\n"`, `"\r\n"`) but attaches a **starts with** constraint that other `XString` methods honour
when iterating over lines. Use the optional `$trim` flag to ignore leading spaces and tabs when evaluating the prefix.

## Important notes and considerations

- **Immutable helper.** Calling `startsWith()` returns a *new* `Newline` instance that carries the constraint; the original
  instance remains unchanged.
- **Works with line-aware operations.** Methods such as `replace()`, `before()`, `after()`, `between()`, etc. accept the
  configured adapter to target whole lines.
- **Whitespace trimming is shallow.** Only regular spaces (`' '`) and tabs (`"\t"`) are stripped when `$trim` is `true`.
- **Prefix must be meaningful.** After optional trimming, an empty prefix triggers an `InvalidArgumentException`.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$string` | `null\|string` | — | Text the line must start with. Cannot be empty after optional trimming. `null` is treated as an empty string. |
| `$trim` | `bool` | `false` | When `true`, ignore leading spaces and tabs before comparing the prefix. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A fresh `Newline` adapter carrying the start-of-line constraint. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$prefix` is empty (after trimming when `$trim` is `true`). |

## Examples

### Replace lines that start with a prefix

<!-- test:newline-startswith-replace -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$log = <<<LOG
ERROR: Disk full
INFO: Recovery complete
ERROR: Backup failed
LOG;

$result = XString::new($log)
    ->replace(Newline::new("\n")->startsWith('ERROR:'), '[redacted]');

#Test: self::assertSame("[redacted]\nINFO: Recovery complete\n[redacted]", (string) $result);
#Test: self::assertSame($log, XString::new($log)->__toString());
```

### Trim indentation when matching

<!-- test:newline-startswith-trim -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$list = "  - apple\n\t- banana\nsummary";

$result = XString::new($list)
    ->replace(Newline::new("\n")->startsWith('-', trim: true), '<item>');

#Test: self::assertSame("<item>\n<item>\nsummary", (string) $result);
```

### Respect indentation when trim is disabled

<!-- test:newline-startswith-no-trim -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$tasks = "Task: root\n  Task: child\nTask: tail";

$result = XString::new($tasks)
    ->replace(Newline::new("\n")->startsWith('Task:'), '[done]');

#Test: self::assertSame("[done]\n  Task: child\n[done]", (string) $result);
```

### Immutable newline adapters carry configuration

<!-- test:newline-startswith-immutability -->
```php
use Orryv\XString\Newline;

$base = Newline::new("\n");
$matcher = $base->startsWith('Item:');

$constraint = $matcher->getLineConstraint();

#Test: self::assertNull($base->getLineConstraint());
#Test: self::assertSame(['type' => 'starts_with', 'needle' => 'Item:', 'trim' => false], $constraint);
#Test: self::assertSame("\n", (string) $base);
#Test: self::assertSame("\n", (string) $matcher);
```

### Reject empty prefixes

<!-- test:newline-startswith-empty -->
```php
use InvalidArgumentException;
use Orryv\XString\Newline;

$this->expectException(InvalidArgumentException::class);
Newline::new("\n")->startsWith("   \t  ", trim: true);
```

### Match Unix text with a Windows newline adapter

<!-- test:newline-startswith-cross-platform -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$notes = "alpha\nbeta\n";

$result = XString::new($notes)
    ->replace(Newline::new("\r\n")->startsWith('beta'), 'ROW');

#Test: self::assertSame("alpha\r\nROW\r\n", (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `Newline::startsWith` | `public function startsWith(string $prefix, bool $trim = false): self` — Return a newline adapter whose lines must begin with the given prefix, optionally trimming leading spaces and tabs. |
