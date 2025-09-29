# Basic Usage

<!-- test:basic -->
```php
use Orryv\XString;

// Create a new XString instance
$str = new XString(" Hello, World! \n");
#Test: self::assertTrue($str instanceof XString);
#Test: self::assertEquals(" Hello, World! \n", (string)$str);

// Trim whitespace
$trimmed = $str->trim();
echo $trimmed; // Outputs: "Hello, World!"
#Test: self::assertEquals("Hello, World!", (string)$trimmed);
```