# XString::fromFile()

## Table of Contents
- [XString::fromFile()](#xstringfromfile)
  - [Table of Contents](#table-of-contents)
  - [Technical details](#technical-details)
  - [Description](#description)
  - [Important notes and considerations](#important-notes-and-considerations)
  - [Parameters](#parameters)
  - [Returns](#returns)
  - [Thrown exceptions](#thrown-exceptions)
  - [Examples](#examples)
    - [Read an entire file into an `XString`](#read-an-entire-file-into-an-xstring)
    - [Read a slice with offset and length](#read-a-slice-with-offset-and-length)
    - [Specify a custom mode and encoding](#specify-a-custom-mode-and-encoding)
    - [Non-existent files raise a runtime exception](#non-existent-files-raise-a-runtime-exception)
    - [Invalid arguments are rejected](#invalid-arguments-are-rejected)
  - [One-line API table entry](#one-line-api-table-entry)

## Technical details

**Signature:**

```php
public static function fromFile(
    string $file_path,
    ?int $length = null,
    ?int $offset = 0,
    string $encoding = 'UTF-8'
): self
```

| Namespace | Instance / Static | Immutable (returns clone) | Public / Private / Protected |
| --- | --- | --- | --- |
| `Orryv\XString` | Static | ✓ | Public |

## Description

Creates a new immutable `XString` populated with the contents of a file. You may optionally limit how much of the file is read
by specifying an offset and/or length, and you can choose the encoding label the instance should report. Switch the logical
iteration mode afterwards with `withMode()` if you need to work in `'bytes'` or `'codepoints'`.

## Important notes and considerations

- **Immutable construction.** Reading a file always returns a fresh `XString`; no existing instance is modified.
- **Partial reads.** Combine `$offset` and `$length` to read only a slice of the file without loading it all into memory.
- **Encoding label.** `$encoding` is stored on the resulting instance and is used by operations such as `toUpper()` and
  `length()` when computing codepoint-based results.
- **Mode changes.** Use `withMode()` after reading if you need a different logical iteration mode.

## Parameters

| Parameter | Default | Type | Description |
| --- | --- | --- | --- |
| `$file_path` | — | `string` | Absolute or relative path to the file to read. Must exist and be readable. |
| `$length` | `null` | `null\|int` | Maximum number of bytes to read. When `null`, the remainder of the file is read. Must be ≥ 0 if provided. |
| `$offset` | `0` | `null\|int` | Number of bytes to skip before reading. Treated as 0 when `null`. Must be ≥ 0. |
| `$encoding` | `'UTF-8'` | `string` | Encoding label stored on the resulting instance. Must be a non-empty string. |

## Returns

| Return Type | Immutable (returns clone) | Description |
| --- | --- | --- |
| `self` | ✓ | A new immutable `XString` containing the file's data. |

## Thrown exceptions

| Exception | When |
| --- | --- |
| `RuntimeException` | The file does not exist, is unreadable, or reading fails. |
| `InvalidArgumentException` | `$offset` or `$length` are negative, or `$encoding` is empty. |

## Examples

### Read an entire file into an `XString`

<!-- test:from-file-basic -->
```php
use Orryv\XString;

$file = tempnam(sys_get_temp_dir(), 'xstring_');
file_put_contents($file, "Hello file!\nSecond line");

$xstring = XString::fromFile($file);

#Test: self::assertSame("Hello file!\nSecond line", (string) $xstring);
#Test: self::assertSame(strlen("Hello file!\nSecond line"), $xstring->length());

@unlink($file);
```

### Read a slice with offset and length

<!-- test:from-file-slice -->
```php
use Orryv\XString;

$file = tempnam(sys_get_temp_dir(), 'xstring_');
file_put_contents($file, 'abcdefghij');

$xstring = XString::fromFile($file, length: 4, offset: 3);

#Test: self::assertSame('defg', (string) $xstring);
#Test: self::assertSame(4, $xstring->length());

@unlink($file);
```

### Specify a custom encoding

<!-- test:from-file-mode -->
```php
use Orryv\XString;

$file = tempnam(sys_get_temp_dir(), 'xstring_');
$bytes = mb_convert_encoding('äëïöü', 'ISO-8859-1', 'UTF-8');
file_put_contents($file, $bytes);

$xstring = XString::fromFile($file, encoding: 'ISO-8859-1')
    ->withMode('codepoints', 'ISO-8859-1');
$utf8 = mb_convert_encoding((string) $xstring, 'UTF-8', 'ISO-8859-1');

#Test: self::assertSame('äëïöü', $utf8);
#Test: self::assertSame(5, $xstring->length());

@unlink($file);
```

### Non-existent files raise a runtime exception

<!-- test:from-file-missing -->
```php
use Orryv\XString;
use RuntimeException;

$path = sys_get_temp_dir() . '/missing-' . uniqid('xstring_', true);
#Test: $this->expectException(RuntimeException::class);
XString::fromFile($path);
```

### Invalid arguments are rejected

<!-- test:from-file-invalid -->
```php
use InvalidArgumentException;
use Orryv\XString;

$file = tempnam(sys_get_temp_dir(), 'xstring_');
file_put_contents($file, 'content');

try {
    #Test: $this->expectException(InvalidArgumentException::class);
    XString::fromFile($file, length: -1);
} finally {
    @unlink($file);
}
```

## One-line API table entry

| Method | Signature & Description |
| --- | --- |
| `XString::fromFile` | `public static function fromFile(string $file_path, ?int $length = null, ?int $offset = 0, string $encoding = 'UTF-8'): self` — Create an immutable `XString` from a file's contents with optional slicing and encoding. |
