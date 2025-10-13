# Newline::endsWith()

## Table of Contents
- [Newline::endsWith()](#newlineendswith)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Replace lines that end with a suffix](#replace-lines-that-end-with-a-suffix)
    - [Trim trailing spaces and tabs before matching](#trim-trailing-spaces-and-tabs-before-matching)
    - [Inspect logs for warnings at the end of a line](#inspect-logs-for-warnings-at-the-end-of-a-line)
    - [Immutable helpers carry their constraint](#immutable-helpers-carry-their-constraint)
    - [Reject empty suffixes after trimming](#reject-empty-suffixes-after-trimming)
    - [Adapt Unix data with a Windows newline adapter](#adapt-unix-data-with-a-windows-newline-adapter)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function endsWith(null|string $string, bool $trim = false): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Attach an **ends with** constraint to a newline adapter. When the configured adapter is
used with methods such as `replace()`, `contains()`, `startsWith()` or `endsWith()`, it
will match only the lines whose trailing text finishes with the provided suffix. Enable
`$trim` to ignore spaces and tabs on the right-hand side before testing the suffix.

## Important notes and considerations

- **Immutable helper.** Calling `endsWith()` returns a *new* `Newline` instance. The
  original object remains unchanged.
- **Whitespace trimming is shallow.** When `$trim` is `true`, only regular spaces (`' '`) and
  tabs (`"\t"`) are removed from the end of each line before the comparison.
- **Suffix must be meaningful.** After optional trimming an empty suffix triggers an
  `InvalidArgumentException`.
- **Canonical newline support.** If your text uses `"\n"` while the adapter was created
  with `"\r\n"`, the helper still recognises the lines and rejoins them using the adapter's
  newline sequence after replacements.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$string` | `null\|string` | — | Text the line must end with. Cannot be empty after optional trimming. `null` is treated as an empty string. |
| `$trim` | `bool` | `false` | When `true`, ignore trailing spaces and tabs on each line before comparing the suffix. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A fresh `Newline` adapter carrying the end-of-line constraint. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `InvalidArgumentException` | `$string` is empty after trimming when `$trim` is `true`. |

## Examples

### Replace lines that end with a suffix

<!-- test:newline-endswith-replace -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$report = <<<TEXT
Build: ok
Deploy: failed
Audit: failed
TEXT;

$result = XString::new($report)
    ->replace(Newline::new("\n")->endsWith('failed'), '[redacted]');

#Test: self::assertSame("Build: ok\n[redacted]\n[redacted]", (string) $result);
```

### Trim trailing spaces and tabs before matching

<!-- test:newline-endswith-trim -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$checklist = "pass  \nretry\t\nfail";

$result = XString::new($checklist)
    ->replace(Newline::new("\n")->endsWith('retry', trim: true), '[again]');

#Test: self::assertSame("pass  \n[again]\nfail", (string) $result);
```

### Inspect logs for warnings at the end of a line

<!-- test:newline-endswith-contains -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$log = XString::new("INFO ready\nWARN: Disk slow   \nOK\n");

#Test: self::assertTrue($log->contains(Newline::new("\n")->endsWith('slow', trim: true)));
#Test: self::assertFalse($log->contains(Newline::new("\n")->endsWith('offline', trim: true)));
```

### Immutable helpers carry their constraint

<!-- test:newline-endswith-immutability -->
```php
use Orryv\XString\Newline;

$base = Newline::new("\n");
$matcher = $base->endsWith('!');

$constraint = $matcher->getLineConstraint();

#Test: self::assertNull($base->getLineConstraint());
#Test: self::assertSame(['type' => 'ends_with', 'needle' => '!', 'trim' => false], $constraint);
#Test: self::assertSame("\n", (string) $base);
#Test: self::assertSame("\n", (string) $matcher);
```

### Reject empty suffixes after trimming

<!-- test:newline-endswith-empty -->
```php
use InvalidArgumentException;
use Orryv\XString\Newline;

$this->expectException(InvalidArgumentException::class);
Newline::new("\n")->endsWith("  \t  ", trim: true);
```

### Adapt Unix data with a Windows newline adapter

<!-- test:newline-endswith-cross-platform -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$notes = "alpha\nbeta\n";

$result = XString::new($notes)
    ->replace(Newline::new("\r\n")->endsWith('beta'), 'ROW');

#Test: self::assertSame("alpha\r\nROW\r\n", (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `Newline::endsWith` | `public function endsWith(null\|string $string, bool $trim = false): self` — Return a newline adapter whose lines must end with the given suffix, optionally trimming trailing spaces and tabs before matching. |
