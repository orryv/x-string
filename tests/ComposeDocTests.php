<?php

// Run `php tests/ComposeDocTests.php` to regenerate the docs in README.md

$max_version = 1.1;
$base_path = __DIR__ . '/../';
$base_namespace = 'Orryv\\XString';

$main_readme = file_get_contents($base_path . 'readme-v3.md');


echo 'Readme: ' . strlen($main_readme) . ' bytes', PHP_EOL;

$lines = explode("\n", $main_readme);

removeDocsDir();

$zero_count = 0;

foreach($lines as $line) {
    if(!str_starts_with($line, '| [`')) {
        continue;
    }

    // Extract doc url
    preg_match('/\|\s+\[`(.*?)`\]\((.*?)\)\s+\|/', $line, $matches);
    if(count($matches) < 3) {
        echo '  Could not parse line: ' . $line, PHP_EOL;
        continue;
    }

    $method_name = $matches[1];
    $doc_path = $matches[2];
    echo 'Method: ' . $method_name .  ' (' . $doc_path . ')', PHP_EOL;

    // Check version (second column) version is a float number
    $version = substr($line, strpos($line, '|', 1) + 1);
    $version = substr($version, 0, strpos($version, '|'));
    $version = trim($version);
    
    if($version > $max_version) {
        echo '  Skipping version ' . $version . ' (max ' . $max_version . ')', PHP_EOL;
        continue;
    }
    
    // Load doc file
    $doc_file = $base_path . $doc_path;
    if(!file_exists($doc_file)) {
        echo '  Doc file not found: ' . $doc_file, PHP_EOL;
        continue;
    }

    $doc_content = file_get_contents($doc_file);
    $doc_lines = explode("\n", $doc_content);

    $blocks = extractTestBlocks($doc_content);
    
    echo '  Found ' . count($blocks) . ' test blocks', PHP_EOL;

    if(empty($blocks)) {
        $zero_count++;
    }

    $data = parseBlocks($blocks);
    composeTestFile($data, $doc_path, $method_name);

}

echo PHP_EOL . 'Metrics: ' . $zero_count . ' methods with no tests', PHP_EOL;

function extractTestBlocks(string $doc_content): array {
    $blocks = [];
    $current_block = [];
    $block_test_name = false;
    $next_is_opening = false;

    $lines = explode("\n", $doc_content);
    foreach ($lines as $key => $line) {
        $trimmedLine = ltrim($line);
        if (str_starts_with(strtolower($trimmedLine), '<!-- test:') && str_starts_with(ltrim($lines[$key + 1] ?? ''), '```')) {
            $block_test_name = trim(substr($trimmedLine, 10, -4));
            $current_block = [];
            $next_is_opening = true;
            continue;
        }

        if ($block_test_name) {
            if (!$next_is_opening && str_starts_with(ltrim($line), '```')) {
                if (!empty($current_block)) {
                    // echo '  Found test block: ' . $block_test_name . ' (' . count($current_block) . ' lines)', PHP_EOL;
                    $blocks[$block_test_name] = implode("\n", $current_block);
                }
                $block_test_name = false;
                continue;
            }
            // $current_block[] = $line;
        }

        if (!$next_is_opening && $block_test_name) {
            $current_block[] = $line;

            $lowerTrimmed = strtolower($trimmedLine);
            if((str_starts_with($lowerTrimmed, '#test:') || str_starts_with($lowerTrimmed, '# test:') )
                && (strpos($line, 'self::') === false && strpos($line, '$this->') === false)
            ) {
                echo '  Found non-self or instance (self::, $this->) test line: ' . $line . PHP_EOL;
                exit;
            }

        }

        if ($next_is_opening) {
            $next_is_opening = false;
        }
    }

    return $blocks;
}

function parseBlocks(array $blocks): array {
    $data = [
        'uses' => [],
        'tests' => [],
    ];

    foreach ($blocks as $test_name => $block) {
        $lines = explode("\n", $block);
        foreach ($lines as $line) {

            if(!isset($data['tests'][$test_name])){
                $data['tests'][$test_name] = '';
            }

            $trimmed = trim($line);
            if(empty($trimmed)) {
                continue;
            } else if(str_starts_with($trimmed, '//')) {
                continue;
            }


            $trimmedLine = ltrim($line);
            $lowerTrimmed = strtolower($trimmedLine);

            if (str_starts_with($lowerTrimmed, 'use ')) {
                $useLine = trim($trimmedLine);
                if (!str_ends_with($useLine, ';')) {
                    $useLine .= ';';
                }

                $data['uses'][] = $useLine;
                continue;
            } elseif (str_starts_with($lowerTrimmed, '#test:')) {
                $data['tests'][$test_name] .= trim(substr($trimmedLine, 6)) . "\n";
                continue;
            } elseif(str_starts_with($lowerTrimmed, '# test:')) {
                $data['tests'][$test_name] .= trim(substr($trimmedLine, 7)) . "\n";
                continue;
            }

            if(str_starts_with(ltrim($line), '#')) {
                continue;
            }

            $data['tests'][$test_name] .= $line . "\n";
        }
    }
    return $data;
}

function composeTestFile(array $data, string $doc_path, string $method): void
{
    global $base_namespace;

    $normalized_path = ltrim($doc_path, '/');
    $path_info = pathinfo($normalized_path);

    $name = $path_info['filename'] ?? '';

    if($method !== $name) {
        echo 'ERROR: method name (' . $method . ') does not match doc file name (' . $name . ' -> ' . $doc_path . ')' . PHP_EOL;
        return;
    }

    $relative_directory = $path_info['dirname'] ?? '';
    if ($relative_directory === '.') {
        $relative_directory = '';
    }

    // first character to upper
    $name = ucfirst($name);

    $namespace_segments = [];
    $trimmed_path = trim($relative_directory, '/');
    if ($trimmed_path !== '') {
        foreach (explode('/', $trimmed_path) as $segment) {
            if ($segment === '') {
                continue;
            }

            $namespace_segments[] = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $segment)));
        }
    }

    $namespace = $base_namespace . '\\Tests\\Docs';
    if (!empty($namespace_segments)) {
        $namespace .= '\\' . implode('\\', $namespace_segments);
    }

    $relative_directory_path = $relative_directory === ''
        ? ''
        : str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative_directory) . DIRECTORY_SEPARATOR;

    $new_path = __DIR__ . DIRECTORY_SEPARATOR . 'Docs' . DIRECTORY_SEPARATOR . $relative_directory_path . $name . 'Test.php';
    $dir = dirname($new_path);
    // echo '  New path: ' . $new_path . PHP_EOL;
    if(!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $output = "<?php\n";
    $output .= "\n";
    $output .= "declare(strict_types=1);\n";
    $output .= "\n";
    $output .= "namespace " . $namespace . ";\n";
    $output .= "\n";
    $output .= "use PHPUnit\\Framework\\TestCase;\n";
    if(!empty($data['uses'])) {
        $unique_uses = array_unique($data['uses']);
        sort($unique_uses);
        foreach($unique_uses as $use) {
            if(empty(trim($use))) {
                continue;
            }

            $output .= $use . "\n";
        }
    }
    $output .= "\n";
    $output .= "final class " . $name . "Test extends TestCase\n";
    $output .= "{\n";
    foreach($data['tests'] as $test_name => $test_code) {
        $method_name = 'test' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $test_name)));
        $output .= "    public function " . $method_name . "(): void\n";
        $output .= "    {\n";
        $test_lines = explode("\n", trim($test_code));
        foreach($test_lines as $line) {
            $output .= "        " . rtrim($line) . "\n";
        }
        $output .= "    }\n\n";
    }

    $output .= "}\n";

    if(empty($data['tests'])) {
        echo '  WARNING: No tests found, skipping file creation', PHP_EOL;
        return;
    }

    file_put_contents($new_path, $output);
}

function removeDocsDir(): void {
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'Docs';
    if(is_dir($dir)) {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
                     RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }
}

?>