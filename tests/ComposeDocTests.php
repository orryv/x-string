<?php

// TODO: Search src/ (and possibly other folders) for methods without docblocks, docblocks of public methods must also have an @see (dedicated documentation) tag
// TODO: Search in the code for public methods that are not in a method-list in any doc file
// TODO: accept a error and warning limit, and return non-zero exit code if exceeded
// TODO: put into own project and make installable via composer, and make config with args possible (root folder, base namespace, exclude paths, etc)


ComposeDocTests::run(__DIR__ . '/../', null, 'Orryv\\XString');


class ComposeDocTests
{
    private ?self $instance = null;
    private ?string $root_folder = null;
    private array $exclude = ['/vendor/', '/.git/', '/tests/'];
    private ?string $current_file = null;
    private array $processed_files = [];

    private string $base_namespace = 'Orryv\\XString';

    private int $method_list_missing_count = 0;
    private int $all_urls_missing_count = 0;
    private int $test_blocks_found_count = 0;
    private int $test_blocks_docs_missing_count = 0;

    public static function run($root_folder, ?array $exclude = null, ?string $base_namespace = null): void
    {
        $instance = new self();

        if($exclude !== null) {
            $instance->exclude = array_merge($exclude, ['/vendor/', '/.git/']);
        }

        if ($base_namespace !== null) {
            $instance->base_namespace = rtrim($base_namespace, '\\');
        }

        $instance->normalizeExcludes();
        $instance->handleRawRootFolder($root_folder);
        $instance->removeDocsDir();

        // echo 'Root folder: ' . $root_folder . PHP_EOL;
        // exit;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($instance->root_folder, FilesystemIterator::SKIP_DOTS)
        );

        $instance->walkItems($iterator);
        $instance->secondWalkItems($iterator);

        echo 'Finished processing.' . PHP_EOL;
        echo '  Method-list missing docs count: ' . $instance->method_list_missing_count . PHP_EOL;
        echo '  All URLs missing docs count: ' . $instance->all_urls_missing_count . PHP_EOL;
        echo '  File count in /docs/ with no test blocks: ' . $instance->test_blocks_docs_missing_count . PHP_EOL;
        echo '  Total test blocks found: ' . $instance->test_blocks_found_count . PHP_EOL;
    }

    public function removeDocsDir(): void 
    {
        $dir = $this->root_folder . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Docs';
        echo 'Removing existing ' . $dir . ' directory if it exists...' . PHP_EOL;
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

    public function walkItems($iterator, $current = null): void
    {
        foreach ($iterator as $fileinfo) {
            $rel_path = str_replace($this->root_folder, '', $fileinfo->getPathname());

            foreach($this->exclude as $excl) {
                if(str_contains($rel_path, $excl)) {
                    continue 2;
                }
            }

            if ($fileinfo->isDir()) {
                continue;
            }

            $this->current_file = $fileinfo->getPathname();

            $filename = $fileinfo->getFilename();
            if (str_ends_with($filename, '.md')) {
                // self::processDocFile($fileinfo->getPathname(), $root_folder, $current);
                echo $rel_path . ': ' . PHP_EOL;
                $content = file_get_contents($fileinfo->getPathname());
                if($content === false) {
                    echo '  ERROR: Could not read file, skipping', PHP_EOL;
                    continue;
                }
                $this->processTestBlocks($content, $rel_path);
                // exit;
                $this->processMethodList($content);
            }
        }
    }

    public function secondWalkItems($iterator, $current = null): void
    {
        foreach ($iterator as $fileinfo) {
            $rel_path = str_replace($this->root_folder, '', $fileinfo->getPathname());

            foreach($this->exclude as $excl) {
                if(str_contains($rel_path, $excl)) {
                    continue 2;
                }
            }

            if ($fileinfo->isDir()) {
                continue;
            }

            $this->current_file = $fileinfo->getPathname();

            $filename = $fileinfo->getFilename();
            if (str_ends_with($filename, '.md')) {
                // self::processDocFile($fileinfo->getPathname(), $root_folder, $current);
                echo $rel_path . ': ' . PHP_EOL;
                $content = file_get_contents($fileinfo->getPathname());
                if($content === false) {
                    echo '  ERROR: Could not read file, skipping', PHP_EOL;
                    continue;
                }
                $this->checkIfAllDocUrlsExistAndAreValid($content, $fileinfo->getPath());
            }
        }
    }

    private function handleRawRootFolder($root_folder): void
    {
        $real_path = realpath($root_folder);
        if ($real_path === false || !is_dir($real_path)) {
            throw new InvalidArgumentException("The provided root folder path is invalid: $root_folder");
        }
        $folder = rtrim($real_path, DIRECTORY_SEPARATOR);
        echo 'Root folder: ' . $folder . PHP_EOL;
        $this->root_folder = $folder;
    }

    private function normalizeExcludes(): void
    {
        $normalized = [];
        foreach ($this->exclude as $path) {
            if(DIRECTORY_SEPARATOR === '\\') {
                $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
            } else {
                $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
            }
            $normalized[] = $path;
        }
        $this->exclude = $normalized;
    }

    private function processTestBlocks(string $content, string $rel_path): void
    {
        $blocks = [];
        $lines = preg_split('/\r\n|\n|\r/', $content);
        if ($lines === false) {
            $this->error('Unable to read documentation content.', null);
            return;
        }

        $line_count = count($lines);

        for ($index = 0; $index < $line_count; $index++) {
            $line = $lines[$index];

            if (preg_match('/^\s*<!--\s*test:\s*([A-Za-z0-9-]+)\s*-->$/', $line, $marker_match) !== 1) {
                if (preg_match('/^\s*<!--\s*test:/', $line) === 1) {
                    $this->error('Found "<!-- test:... -->" but something is wrong with the formatting. Either the name has invalid characters (<!-- test:[A-Za-z0-9-] -->) or the code block is missing on the line after.', $index + 1);
                }

                continue;
            }

            $test_name = $marker_match[1];

            if ($index + 1 >= $line_count) {
                $this->error('Found "<!-- test:... -->" but the code block is missing on the line after.', $index + 1);
                break;
            }

            $fence_line = $lines[$index + 1];
            if (preg_match('/^\s*```(?:[A-Za-z0-9_-]+)?\s*$/', $fence_line) !== 1) {
                $this->error('Found "<!-- test:... -->" but the code block is missing on the line after.', $index + 1);
                continue;
            }

            $block_lines = [];
            $found_closing = false;

            for ($block_index = $index + 2; $block_index < $line_count; $block_index++) {
                $current = $lines[$block_index];
                if (preg_match('/^\s*```\s*$/', $current) === 1) {
                    $found_closing = true;
                    break;
                }

                $block_lines[] = rtrim($current, "\r");
            }

            if (!$found_closing) {
                $this->error('Opening ``` without a closing ```.', $index + 1);
                continue;
            }

            $blocks[$test_name] = rtrim(implode("\n", $block_lines));
            $index = $block_index;
            $this->test_blocks_found_count++;
        }

        echo '  Found ' . count($blocks) . ' test blocks', PHP_EOL;
        if(str_starts_with($rel_path, DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR) && count($blocks) < 1) {
            $this->test_blocks_docs_missing_count++;
            $this->warning('  No test blocks found in a doc file under /docs/.', null);
        }

        if(!empty($blocks)) {
            $data = $this->parseBlocks($blocks);
            $this->composeTestFile($data, $rel_path);
        }

    }

    private function parseBlocks(array $blocks): array {
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

    private function composeTestFile(array $data, string $doc_path): void
    {
        $name = substr(basename($doc_path), 0, -3);
        $name = $this->studly($name);

        $directory = str_replace(['\\', '/'], '/', dirname($doc_path));
        $relative_path = '';
        if ($directory !== '/' && $directory !== '.') {
            $relative = trim($directory, '/');
            if (str_starts_with($relative, 'docs/')) {
                $relative = substr($relative, strlen('docs/'));
            }
            $relative_path = $relative;
        }

        $namespace_segments = [];
        $trimmed_path = trim($relative_path, '/');
        if ($trimmed_path !== '') {
            foreach (explode('/', $trimmed_path) as $segment) {
                if ($segment === '') {
                    continue;
                }

                $namespace_segments[] = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $segment)));
            }
        }

        $namespace = $this->base_namespace . '\\Tests\\Docs';
        if (!empty($namespace_segments)) {
            $namespace .= '\\' . implode('\\', $namespace_segments);
        }

        $relative_dir = $relative_path === '' ? '' : DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
        $new_path = $this->root_folder . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Docs' . $relative_dir . DIRECTORY_SEPARATOR . $name . 'Test.php';

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
            $method_name = 'test' . $this->studly($test_name);
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
            echo 'WARNING: No tests found, skipping file creation', PHP_EOL;
            return;
        }

        file_put_contents($new_path, $output);
    }

    private function studly(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', trim($value));
        $value = ucwords($value);

        return str_replace(' ', '', $value);
    }

    private function processMethodList(string $content): void
    {
        // regex match all
        preg_match_all('/^<!--\s*method-list\s*-->\h*\R(?=\|)\K(?:\|[^\r\n]*\R)*(?:\|[^\r\n]*)(?=\R[ \t]*\R|$)/m', $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        preg_match_all('/^<!--\s*method-list\s*-->/m', $content, $count, PREG_SET_ORDER);

        if(count($count) != count($matches)) {
            $line = strpos($content, '<!-- method-list -->');
            $this->error('Found "<!-- method-list -->" but something is wrong with the table formatting.', $line);
            return;
        }


        if(!empty($matches)) {
            // Split into lines
            foreach($matches as $match) {
                // Split into lines
                $lines = explode("\n", $match[0][0]);
                $passed_header = false;

                foreach($lines as $key =>$line) {
                    $current_line   = 1 + preg_match_all('/\R/', substr($content, 0, $match[0][1])) + $key;

                    if(!$passed_header) {
                        if(preg_match('/^\|\s*-{3,}\s*\|/', $line)) {
                            $passed_header = true;
                        }
                        continue;
                    }


                    if(str_starts_with($line, '| ')) {
                        if(str_starts_with($line, '| [`')) {
                            if(!str_contains($line, '](') || !str_contains($line, ') |')) {
                                $this->warning('Line does not contain a valid markdown link to a doc file.', $current_line);
                                continue;
                            } // else OK, will be processed below
                        } else if(str_starts_with($line, '| `')) {
                            $this->warning('Found presumably a method without an URL to a doc file.', $current_line);
                            continue;
                        } else if(str_starts_with($line, '| [')) {
                            $this->warning('Found presumably a method not wrapped in backticks (``)', $current_line);
                            continue;
                        }
                        
                    } else {
                        $this->warning('Line does not start with "| " (pipe and space).', $current_line);
                        continue;
                    }

                    // Extract doc url
                    preg_match('/\|\s+\[`(.*?)`\]\((.*?)\)\s+\|/', $line, $url_matches);

                    if (count($url_matches) < 3) {
                        $this->warning('Could not extract method name and doc path from line.', $current_line);
                        continue;
                    }

                    if(strpos($url_matches[2], '\\') !== false) {
                        $this->error('Doc path contains backslashes (\\) instead of forward slashes (/).', $current_line);
                        continue;
                    }

                    $doc_path = $this->root_folder . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $url_matches[2]);
                    $this->processed_files[] = $doc_path;

                    if(count($url_matches) < 3) {
                        echo '  Could not parse line: ' . $line, PHP_EOL;
                        continue;
                    }

                    // Check if method name aligns with filename
                    if(!str_ends_with($url_matches[2], $url_matches[1] . '.md')) {
                        $this->warning('Method name ('.$url_matches[1].') does not align with filename in doc path ('.$url_matches[2].')', $current_line);
                        continue;
                    }

                    // Check if the url exists
                    if(!$this->path_exists_strict($doc_path)) {
                        $this->method_list_missing_count++;
                        $this->error('Doc file not found: ' . $doc_path, $current_line);
                        continue;
                    }
                }
            }
            
        }
    }

    private function checkIfAllDocUrlsExistAndAreValid(string $content, $dir): void
    {
        $base_path = $dir . DIRECTORY_SEPARATOR;
        $max_version = 3.0;
        $zero_count = 0;

        // regex match all
        preg_match_all('/\[([^\]]+)\]\(([^)]+)\)/', $content, $url_matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach($url_matches as $url_match) {
            $current_line = 1 + preg_match_all('/\R/', substr($content, 0, $url_match[0][1]));
            $path = $base_path . str_replace('/', DIRECTORY_SEPARATOR, $url_match[2][0]);

            if(str_starts_with($url_match[2][0], 'http://') || str_starts_with($url_match[2][0], 'https://') || str_starts_with($url_match[2][0], '#')) {
                // External link or anchor, skip
                continue;
            }

            if(in_array($path, $this->processed_files, true)) {
                // Already processed
                continue;
            }

            if(strpos($url_match[2][0], '\\') !== false) {
                $this->error('Doc path contains backslashes (\\) instead of forward slashes (/).', $current_line);
                continue;
            }

            if(substr($content, $url_match[0][1] - strlen('<!-- skip-url-warning -->'), strlen('<!-- skip-url-warning -->')) === '<!-- skip-url-warning -->') {
                // marked to skip warning
                continue;
            }

            if(!$this->path_exists_strict($path)) {
                $this->all_urls_missing_count++;
                $this->warning('Doc file ('.$url_match[1][0].') not found in a method list, and file does not exist. Put "<!-- skip-url-warning -->" immediately before the []() to tag it. Path: ' . $url_match[2][0] . ' (' . $path . ')', $current_line);
                continue;
            } else {
                $this->warning('Doc file ('.$url_match[1][0].') not found in a method list. Put "<!-- skip-url-warning -->" immediately before the []() to tag it. Path: ' . $url_match[2][0] . ' (' . $path . ')', $current_line);
            }
        }
    }

    private function error(string $message, ?int $line = null): void
    {
        // Enable ANSI on Windows (no-op elsewhere)
        if (DIRECTORY_SEPARATOR === '\\' && function_exists('sapi_windows_vt100_support')) {
            @sapi_windows_vt100_support(STDOUT, true);
            @sapi_windows_vt100_support(STDERR, true);
        }

        $RED   = "\033[1;31m"; // bright red
        $RESET = "\033[0m";

        echo $RED . '  ERROR:' . $RESET . ' ' . $message . PHP_EOL;
        echo '    In file: ' . $this->current_file . PHP_EOL;
        if ($line !== null) {
            echo '    At line: ' . $line . PHP_EOL;
        }
    }

    private function warning(string $message, ?int $line = null): void
    {
        // Enable ANSI on Windows (no-op elsewhere)
        if (DIRECTORY_SEPARATOR === '\\' && function_exists('sapi_windows_vt100_support')) {
            @sapi_windows_vt100_support(STDOUT, true);
            @sapi_windows_vt100_support(STDERR, true);
        }

        $RESET = "\033[0m";

        // 24-bit "orange" (works on modern Windows consoles)
        $ORANGE24 = "\033[38;2;255;165;0m";

        // Fallback for 16-color consoles: bright yellow
        $YELLOW = "\033[1;33m";

        // Heuristic: prefer truecolor when likely supported
        $truecolorLikely =
            getenv('WT_SESSION') ||                    // Windows Terminal
            getenv('TERM_PROGRAM') === 'vscode' ||     // VS Code terminal
            getenv('ConEmuANSI') === 'ON' ||           // ConEmu
            getenv('ANSICON');                         // Ansicon

        $COLOR = $truecolorLikely ? $ORANGE24 : $YELLOW;

        echo $COLOR . '  WARNING:' . $RESET . ' ' . $message . PHP_EOL;
        echo '    In file: ' . $this->current_file . PHP_EOL;
        if ($line !== null) {
            echo '    At line: ' . $line . PHP_EOL;
        }
    }

    private function path_exists_strict(string $path): bool
    {
        $sep  = DIRECTORY_SEPARATOR;
        // Normalize separators for the current OS
        $path = str_replace($sep === '\\' ? '/' : '\\', $sep, $path);
        $path = rtrim($path, "/\\");
        if ($path === '') return false;

        // Split into parts
        if ($sep === '\\') {
            // Windows
            if (str_starts_with($path, '\\\\')) {
                // UNC: \\server\share\...
                $raw   = preg_split('~[\\\\]+~', substr($path, 2));
                if (count($raw) < 2) return false; // need at least \\server\share
                $server = $raw[0];
                $share  = $raw[1];
                $parts  = array_slice($raw, 2);
                $accum  = '\\\\' . $server . '\\' . $share;
            } else {
                // Drive path: C:\...
                $raw   = preg_split('~[\\\\]+~', $path);
                $drive = array_shift($raw);           // e.g. "C:"
                $parts = $raw;
                $accum = $drive;                      // "C:"
            }
        } else {
            // POSIX
            $isAbs = str_starts_with($path, '/');
            $parts = preg_split('~[/]+~', $path);
            $accum = $isAbs ? '' : getcwd();         // start from cwd for relative
        }

        // Helper: exact-match a child entry inside $dir
        $exactChild = static function (string $dir, string $name): bool {
            $list = @scandir($dir, SCANDIR_SORT_NONE);
            if ($list === false) return false;
            foreach ($list as $entry) {
                if ($entry === $name) return true;
            }
            return false;
        };

        // Walk each segment with exact-case checks
        foreach ($parts as $i => $part) {
            if ($part === '' || $part === '.') continue;
            if ($part === '..') {
                $accum = dirname($accum);
                continue;
            }

            // Ensure parent exists (file or dir)
            if (!is_dir($accum) && !is_file($accum)) return false;

            // Exact-case child check
            if (!$exactChild($accum, $part)) return false;

            // Descend
            $accum = ($accum === '' || str_ends_with($accum, $sep))
                ? $accum . $part
                : $accum . $sep . $part;
        }

        // Final node exists with exact casing (file or dir)
        return $accum !== '' && (is_file($accum) || is_dir($accum));
    }



    
}



?>