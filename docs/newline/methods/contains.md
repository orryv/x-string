# Newline::contains()

## Table of Contents
- [Newline::contains()](#newlinecontains)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Remove lines that contain sensitive words](#remove-lines-that-contain-sensitive-words)
    - [Detect log entries mentioning an error keyword](#detect-log-entries-mentioning-an-error-keyword)
    - [Combine with startsWith() for line-aware checks](#combine-with-startswith-for-line-aware-checks)
    - [Immutable helpers carry their constraint](#immutable-helpers-carry-their-constraint)
    - [Reject empty needles](#reject-empty-needles)
    - [Match Unix text with a Windows newline adapter](#match-unix-text-with-a-windows-newline-adapter)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function contains(null|string $string): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Create a newline adapter that targets lines containing a specific substring. When passed
into `XString` helpers such as `contains()`, `replace()`, `startsWith()` or `endsWith()`,
the adapter ensures only the lines with the configured needle are considered.

## Important notes and considerations

- **Immutable helper.** Each call returns a brand-new `Newline` instance carrying the
  constraint.
- **Case-sensitive matching.** The comparison is performed with the original casing of
  both the line and the needle.
- **Needle cannot be empty.** Passing an empty string (or `null`) raises an
  `InvalidArgumentException` because every line would match.
- **Canonical newline support.** Mixed newline inputs (`"\n"` vs `"\r\n"`) are handled by
  normalising the subject before testing the lines.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$string` | `null\|string` | — | Substring that must appear in the line. Cannot be empty. `null` is treated as an empty string. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `Newline` adapter that filters lines containing the provided substring. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$string` is empty (`''` or `null`). |

## Examples

### Remove lines that contain sensitive words

<!-- test:newline-contains-redact -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$notes = <<<TEXT
user: alice
password: hunter2
remember: never share passwords
TEXT;

$result = XString::new($notes)
    ->replace(Newline::new("\n")->contains('password'), '[redacted]');

#Test: self::assertSame("user: alice\n[redacted]\n[redacted]", (string) $result);
```

### Detect log entries mentioning an error keyword

<!-- test:newline-contains-detect -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$log = XString::new("INFO ready\nWARN: disk slow\nERROR: backup failed\n");

#Test: self::assertTrue($log->contains(Newline::new("\n")->contains('ERROR: ')));
#Test: self::assertFalse($log->contains(Newline::new("\n")->contains('CRITICAL: ')));
```

### Combine with startsWith() for line-aware checks

<!-- test:newline-contains-startswith -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$script = <<<SH
#!/bin/bash
echo "Start"
echo "Process complete"
SH;

$result = XString::new($script)
    ->contains(Newline::new("\n")->contains('Start'))
    && XString::new($script)
        ->startsWith(Newline::new("\n")->contains('Start'));

#Test: self::assertTrue($result);
#Test: self::assertFalse(XString::new($script)->startsWith(Newline::new("\n")->contains('Process')));
```

### Immutable helpers carry their constraint

<!-- test:newline-contains-immutability -->
```php
use Orryv\XString\Newline;

$base = Newline::new("\n");
$matcher = $base->contains('TODO');

$constraint = $matcher->getLineConstraint();

#Test: self::assertNull($base->getLineConstraint());
#Test: self::assertSame(['type' => 'contains', 'needle' => 'TODO'], $constraint);
#Test: self::assertSame("\n", (string) $base);
#Test: self::assertSame("\n", (string) $matcher);
```

### Reject empty needles

<!-- test:newline-contains-empty -->
```php
use InvalidArgumentException;
use Orryv\XString\Newline;

$this->expectException(InvalidArgumentException::class);
Newline::new("\n")->contains('');
```

### Match Unix text with a Windows newline adapter

<!-- test:newline-contains-cross-platform -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$body = "alpha\nbeta\ngamma";

#Test: self::assertTrue(XString::new($body)->contains(Newline::new("\r\n")->contains('beta')));
#Test: self::assertFalse(XString::new($body)->contains(Newline::new("\r\n")->contains('delta')));
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `Newline::contains` | `public function contains(null\|string $string): self` — Return a newline adapter whose lines must contain the provided substring. |
