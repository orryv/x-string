<?php

// Run `php tests/ComposeDocTests.php` to regenerate the docs in README.md

$base_path = __DIR__ . '/../';
$base_namespace = 'Orryv\\XString';
$exclude = [
    '/vendor/',
];

removeDocsDir();

$zero_count = 0;

foreach (findMarkdownFiles($base_path, $exclude) as $doc_path => $doc_file) {
    echo 'File: ' . $doc_path . PHP_EOL;

    if(!file_exists($doc_file)) {
        echo '  Doc file not found: ' . $doc_file, PHP_EOL;
        continue;
    }

    $doc_content = file_get_contents($doc_file);

    $blocks = extractTestBlocks($doc_content);

    echo '  Found ' . count($blocks) . ' test blocks', PHP_EOL;

    if(empty($blocks)) {
        $zero_count++;
    }

    $data = parseBlocks($blocks);
    composeTestFile($data, $doc_path);

}

echo PHP_EOL . 'Metrics: ' . $zero_count . ' files with no tests', PHP_EOL;

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

    $name = substr(basename($doc_path), 0, -3);

    $directoryPath = dirname($doc_path);
    $path = '';
    if ($directoryPath !== '' && $directoryPath !== '.') {
        $normalizedDirectory = trim(str_replace('\\', '/', $directoryPath), '/');
        if ($normalizedDirectory !== '') {
            $path = $normalizedDirectory . '/';
        }
    }
    // first character to upper
    $name = ucfirst($name);

    $namespace_segments = [];
    $trimmed_path = trim($path, '/');
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

    $new_path = __DIR__ . DIRECTORY_SEPARATOR . 'Docs' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path) . $name . 'Test.php';
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

function findMarkdownFiles(string $base_path, array $exclude): array {
    $files = [];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base_path, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $relativePath = str_replace('\\', '/', substr($file->getPathname(), strlen($base_path)));

        if (!str_ends_with(strtolower($relativePath), '.md')) {
            continue;
        }

        if (isExcludedPath($relativePath, $exclude)) {
            continue;
        }

        $files[$relativePath] = $file->getPathname();
    }

    ksort($files);

    return $files;
}

function isExcludedPath(string $relativePath, array $exclude): bool {
    foreach ($exclude as $excludedPath) {
        $isDirectory = str_ends_with($excludedPath, '/');
        if ($isDirectory) {
            if (str_starts_with($relativePath, ltrim($excludedPath, '/'))) {
                return true;
            }
        } elseif ($relativePath === ltrim($excludedPath, '/')) {
            return true;
        }
    }

    return false;
}

?>
