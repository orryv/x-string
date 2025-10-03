# Newlines

<!-- test:newlines -->
```php
use Orryv\XString;
use Orryv\XString\Newline;

$str = <<<EOT
 Line1 - blabla
Hello, World!
EOT;

$string = XString::new($str);
#Test: self::assertEquals($str, (string)$string);

// Remove first line (one way to do it)
$string = $string->after(Newline::new()->startsWith('Line1', trim:true));
echo $string; // Outputs: "Hello, World!"
#Test: self::assertEquals("Hello, World!", (string)$string);
```
