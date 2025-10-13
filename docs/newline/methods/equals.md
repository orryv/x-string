# Newline::equals()

## Table of Contents
- [Newline::equals()](#newlineequals)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Examples](#examples)
    - [Replace lines that exactly match a token](#replace-lines-that-exactly-match-a-token)
    - [Detect blank separator lines](#detect-blank-separator-lines)
    - [Compare a single-line string against the helper](#compare-a-single-line-string-against-the-helper)
    - [Immutable helpers carry their constraint](#immutable-helpers-carry-their-constraint)
    - [Canonicalise newline differences when replacing](#canonicalise-newline-differences-when-replacing)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public function equals(null|string $string): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\\XString` | Instance | ✓ | Public |

## Description

Produce a newline adapter that matches lines equal to the provided string. The adapter can
be used with `replace()` to swap entire lines, with `contains()` to check for specific
rows, and even with `equals()` to verify that a string is exactly one matching line.

## Important notes and considerations

- **Immutable helper.** Each call yields a new `Newline` instance; the original remains
  unchanged.
- **Empty strings allowed.** Unlike `startsWith()`/`endsWith()`/`contains()`, equality
  accepts empty strings to match blank lines.
- **Canonical newline support.** When the adapter's newline differs from the subject's
  newline, comparisons are still performed correctly and replacements rejoin using the
  adapter's newline sequence.

## Parameters

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `$string` | `null\|string` | — | The exact line content to match. `null` is treated as an empty string. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new `Newline` adapter that matches lines equal to the provided string. |

## Examples

### Replace lines that exactly match a token

<!-- test:newline-equals-replace -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$list = "TODO\nShip product\nTODO\n";

$result = XString::new($list)
    ->replace(Newline::new("\n")->equals('TODO'), '[done]');

#Test: self::assertSame("[done]\nShip product\n[done]\n", (string) $result);
```

### Detect blank separator lines

<!-- test:newline-equals-blank -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$message = XString::new("Header\n\nBody");

#Test: self::assertTrue($message->contains(Newline::new("\n")->equals('')));
#Test: self::assertFalse(XString::new('Single line')->contains(Newline::new("\n")->equals('')));
```

### Compare a single-line string against the helper

<!-- test:newline-equals-equals -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$value = XString::new("DONE\n");

#Test: self::assertTrue($value->equals(Newline::new("\n")->equals('DONE')));
#Test: self::assertFalse($value->equals(Newline::new("\n")->equals('FAIL')));
```

### Immutable helpers carry their constraint

<!-- test:newline-equals-immutability -->
```php
use Orryv\XString\Newline;

$base = Newline::new("\n");
$matcher = $base->equals('Ready');

$constraint = $matcher->getLineConstraint();

#Test: self::assertNull($base->getLineConstraint());
#Test: self::assertSame(['type' => 'equals', 'needle' => 'Ready'], $constraint);
#Test: self::assertSame("\n", (string) $base);
#Test: self::assertSame("\n", (string) $matcher);
```

### Canonicalise newline differences when replacing

<!-- test:newline-equals-cross-platform -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$content = "alpha\nbeta\n";

$result = XString::new($content)
    ->replace(Newline::new("\r\n")->equals('beta'), 'ROW');

#Test: self::assertSame("alpha\r\nROW\r\n", (string) $result);
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `Newline::equals` | `public function equals(null\|string $string): self` — Return a newline adapter whose lines must be exactly equal to the provided string. |
