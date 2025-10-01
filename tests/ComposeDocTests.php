<?php

// Run `php tests/ComposeDocTests.php` to regenerate the docs in README.md

$base_path = __DIR__ . '/../';
$base_namespace = 'Orryv\\XString';

removeDocsDir();

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base_path, FilesystemIterator::SKIP_DOTS)
);

$zero_count = 0;

foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }

    if (strtolower($file->getExtension()) !== 'md') {
        continue;
    }

    $doc_path = substr($file->getPathname(), strlen($base_path));

    echo 'Processing: ' . $doc_path . PHP_EOL;

    $doc_content = file_get_contents($file->getPathname());

    $blocks = extractTestBlocks($doc_content);

    echo '  Found ' . count($blocks) . ' test blocks', PHP_EOL;

    if (empty($blocks)) {
        $zero_count++;
        continue;
    }

    $data = parseBlocks($blocks);
    composeTestFile($data, $doc_path);
}

echo PHP_EOL . 'Metrics: ' . $zero_count . ' markdown files with no tests', PHP_EOL;

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

function composeTestFile(array $data, string $doc_path): void
{
    global $base_namespace;

    $normalized_path = ltrim($doc_path, '/');
    $path_info = pathinfo($normalized_path);

    $name = $path_info['filename'] ?? '';

    $relative_directory = $path_info['dirname'] ?? '';
    if ($relative_directory === '.') {
        $relative_directory = '';
    }

    $name = pascalCase($name);

    $namespace_segments = [];
    $trimmed_path = trim($relative_directory, '/');
    if ($trimmed_path !== '') {
        foreach (explode('/', $trimmed_path) as $segment) {
            if ($segment === '') {
                continue;
            }

            $namespace_segments[] = pascalCase($segment);
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
        $method_name = 'test' . pascalCase($test_name);
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

function pascalCase(string $value): string
{
    $value = trim($value);

    if ($value === '') {
        return 'Doc';
    }

    $normalized = preg_replace('/[^A-Za-z0-9]+/', ' ', $value);
    $chunks = preg_split('/\s+/', $normalized);

    $segments = [];
    foreach ($chunks as $chunk) {
        if ($chunk === '') {
            continue;
        }

        $subSegments = preg_split('/(?<=[a-z0-9])(?=[A-Z])/', $chunk);
        foreach ($subSegments as $sub) {
            if ($sub === '') {
                continue;
            }
            $segments[] = $sub;
        }
    }

    if (empty($segments)) {
        return 'Doc';
    }

    return implode('', array_map(fn($segment) => ucfirst(strtolower($segment)), $segments));
}

?>