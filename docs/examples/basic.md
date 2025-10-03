# Basic Usage

<!-- test:basic -->
```php
use Orryv\XString;

// Create a new XString instance
$str = XString::new(" Hello, World! \n");
#Test: self::assertTrue($str instanceof XString);
#Test: self::assertEquals(" Hello, World! \n", (string)$str);

// Trim whitespace
$trimmed = $str->trim();
#Test: self::assertEquals("Hello, World!", (string)$trimmed);
```