<?php

declare(strict_types=1);


$autoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

$config = parse_compose_doc_arguments($argv);

$tester = ComposeDocTests::new($config['root_folder'], $config['namespace']);

if (!empty($config['exclude'])) {
    $tester->setExcludeFolders($config['exclude']);
}

if ($config['features']['block-generation'] === false) {
    $tester->disableTestBlockGeneration();
}

if ($config['features']['method-list'] === false) {
    $tester->disableMethodListCheck();
}

if ($config['features']['doc-urls'] === false) {
    $tester->disableAllUrlsCheck();
}

$tester
    ->setErrorLimit($config['error_limit'])
    ->setWarningLimit($config['warning_limit']);

$exitCode = $tester->run();

exit($exitCode);

/**
 * @param array<int, string> $argv
 * @return array{
 *     root_folder: string,
 *     namespace: string|null,
 *     exclude: array<int, string>,
 *     features: array{block-generation: bool|null, method-list: bool|null, doc-urls: bool|null},
 *     error_limit: int|null,
 *     warning_limit: int|null
 * }
 */
function parse_compose_doc_arguments(array $argv): array
{
    $defaults = [
        'root_folder' => realpath(__DIR__ . '/../') ?: (__DIR__ . '/../'),
        'namespace' => null,
        'exclude' => [],
        'features' => [
            'block-generation' => null,
            'method-list' => null,
            'doc-urls' => null,
        ],
        'error_limit' => null,
        'warning_limit' => null,
    ];

    $config = $defaults;

    foreach (array_slice($argv, 1) as $arg) {
        switch ($arg) {
            case '-block-generation':
                $config['features']['block-generation'] = true;
                continue 2;
            case '-disable-block-generation':
                $config['features']['block-generation'] = false;
                continue 2;
            case '-method-list':
                $config['features']['method-list'] = true;
                continue 2;
            case '-disable-method-list':
                $config['features']['method-list'] = false;
                continue 2;
            case '-doc-urls':
                $config['features']['doc-urls'] = true;
                continue 2;
            case '-disable-doc-urls':
                $config['features']['doc-urls'] = false;
                continue 2;
        }

        if (str_starts_with($arg, '-base-namespace=')) {
            $config['namespace'] = substr($arg, strlen('-base-namespace='));
            continue;
        }

        if (str_starts_with($arg, '-root-folder=')) {
            $config['root_folder'] = substr($arg, strlen('-root-folder='));
            continue;
        }

        if (str_starts_with($arg, '-exclude-folders=')) {
            $json = substr($arg, strlen('-exclude-folders='));
            try {
                $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable $exception) {
                fwrite(STDERR, 'Invalid JSON passed to -exclude-folders: ' . $exception->getMessage() . PHP_EOL);
                continue;
            }

            if (is_array($decoded)) {
                $config['exclude'] = array_values(array_filter(
                    array_map(static fn ($value): string => (string) $value, $decoded),
                    static fn (string $value): bool => $value !== ''
                ));
            }

            continue;
        }

        if (str_starts_with($arg, '-max-errors=')) {
            $limit = substr($arg, strlen('-max-errors='));
            $config['error_limit'] = ctype_digit($limit) ? (int) $limit : null;
            continue;
        }

        if (str_starts_with($arg, '-max-warnings=')) {
            $limit = substr($arg, strlen('-max-warnings='));
            $config['warning_limit'] = ctype_digit($limit) ? (int) $limit : null;
            continue;
        }
    }

    return $config;
}

class ComposeDocTests
{
    private static ?self $instance = null;
    private ?string $root_folder = null;
    private array $exclude = ['/vendor/', '/.git/', '/tests/'];
    private ?string $current_file = null;
    private array $processed_files = [];

    private string $base_namespace = 'Orryv\\XString';

    private int $method_list_missing_count = 0;
    private int $all_urls_missing_count = 0;
    private int $test_blocks_found_count = 0;
    private int $test_blocks_docs_missing_count = 0;

    /** @var array<string, array<int, array{message: string, line: int|null}>> */
    private array $errors = [];

    /** @var array<string, array<int, array{message: string, line: int|null}>> */
    private array $warnings = [];

    private int $error_count = 0;
    private int $warning_count = 0;
    private ?int $error_limit = null;
    private ?int $warning_limit = null;

    /** @var array<string, array{file: string, short: string}> */
    private array $class_map = [];

    /** @var array<string, array<int, string>> */
    private array $short_name_map = [];

    /** @var array<string, array{signature: string, visibility: string, file: string|null, line: int|null}> */
    private array $source_methods = [];

    /** @var array<string, array{signature: string, visibility: string, file: string|null, line: int|null}> */
    private array $public_methods = [];

    /** @var array<string, array<int, array{signature: string, line: int, file: string, doc_path: string|null}>> */
    private array $documented_methods = [];

    /** @var array<string, int> */
    private array $file_metrics = [
        'php_files_scanned' => 0,
        'doc_files_scanned' => 0,
    ];

    private bool $run_test_block_generation = true;
    private bool $run_method_list_check = true;
    private bool $run_all_urls_check = true;

    public static function new(string $root_folder, ?string $base_namespace = null): self
    {
        self::$instance = new self();
        self::$instance->root_folder = rtrim($root_folder, '/');

        if ($base_namespace !== null) {
            self::$instance->base_namespace = rtrim($base_namespace, '\\');
        }

        self::$instance->handleRawRootFolder($root_folder);
        self::$instance->normalizeExcludes();

        return self::$instance;
    }

    public function setExcludeFolders(array $exclude): self
    {
        $this->exclude = array_merge($exclude, ['/vendor/', '/.git/']);
        $this->normalizeExcludes();
        return $this;
    }

    public function disableTestBlockGeneration(): self
    {
        $this->run_test_block_generation = false;
        return $this;
    }

    public function disableMethodListCheck(): self
    {
        $this->run_method_list_check = false;
        return $this;
    }

    public function disableAllUrlsCheck(): self
    {
        $this->run_all_urls_check = false;
        return $this;
    }

    public function setErrorLimit(?int $limit): self
    {
        $this->error_limit = $limit;
        return $this;
    }

    public function setWarningLimit(?int $limit): self
    {
        $this->warning_limit = $limit;
        return $this;
    }


    public function run(): int
    {
        if ($this->run_test_block_generation) {
            $this->removeDocsDir();
        }

        $this->scanSourceFiles();

        if ($this->run_test_block_generation || $this->run_method_list_check) {
            $this->processDocumentationFiles();
        }

        if ($this->run_all_urls_check) {
            $this->validateDocumentationUrls();
        }

        if ($this->run_method_list_check) {
            $this->checkMethodDocumentationCoverage();
        }
        $this->outputReport();

        return $this->determineExitCode();
    }

    private function processDocumentationFiles(): void
    {
        $this->walkItems($this->createIterator());
    }

    private function validateDocumentationUrls(): void
    {
        $this->secondWalkItems($this->createIterator());
    }

    private function createIterator(): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->root_folder, FilesystemIterator::SKIP_DOTS)
        );
    }

    private function relativePath(string $absolutePath): string
    {
        if ($this->root_folder === null) {
            return $absolutePath;
        }

        $prefix = rtrim($this->root_folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (str_starts_with($absolutePath, $prefix)) {
            return substr($absolutePath, strlen($prefix));
        }

        return $absolutePath;
    }

    private function isExcludedPath(string $relativePath): bool
    {
        foreach ($this->exclude as $exclusion) {
            if ($exclusion === '') {
                continue;
            }

            if (str_contains($relativePath, $exclusion)) {
                return true;
            }
        }

        return false;
    }

    private function scanSourceFiles(): void
    {
        $iterator = $this->createIterator();
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir()) {
                continue;
            }

            $path = $fileinfo->getPathname();
            $relative = $this->relativePath($path);

            if ($this->isExcludedPath($relative)) {
                continue;
            }

            if (strtolower($fileinfo->getExtension()) !== 'php') {
                continue;
            }

            $this->file_metrics['php_files_scanned']++;
            $this->current_file = $path;

            $this->registerClassesFromFile($path);
        }

        $this->analyzeCollectedClasses();
    }

    private function registerClassesFromFile(string $path): void
    {
        $code = file_get_contents($path);
        if ($code === false) {
            $this->error('Unable to read PHP source file for analysis.', null, $path);
            return;
        }

        $tokens = token_get_all($code);
        $namespace = '';
        $tokenCount = count($tokens);

        for ($index = 0; $index < $tokenCount; $index++) {
            $token = $tokens[$index];
            if (!is_array($token)) {
                continue;
            }

            $tokenId = $token[0];

            if ($tokenId === T_NAMESPACE) {
                $namespace = $this->collectNamespace($tokens, $index);
                continue;
            }

            if (!in_array($tokenId, [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM], true)) {
                continue;
            }

            if ($this->isAnonymousClass($tokens, $index)) {
                continue;
            }

            $className = $this->collectClassName($tokens, $index);
            if ($className === null) {
                continue;
            }

            $fqn = ltrim($namespace . '\\' . $className, '\\');
            $this->class_map[$fqn] = [
                'file' => $path,
                'short' => $className,
            ];

            $this->short_name_map[$className][] = $fqn;
        }
    }

    /**
     * @param array<int, array{0:int,1:string,2:int}|string> $tokens
     */
    private function collectNamespace(array $tokens, int &$index): string
    {
        $namespace = '';
        $tokenCount = count($tokens);

        for ($i = $index + 1; $i < $tokenCount; $i++) {
            $token = $tokens[$i];

            if (is_array($token)) {
                $id = $token[0];
                if ($id === T_STRING) {
                    $namespace .= $token[1];
                    continue;
                }
                if ($id === T_NS_SEPARATOR) {
                    $namespace .= '\\';
                    continue;
                }
                if (defined('T_NAME_QUALIFIED') && $id === constant('T_NAME_QUALIFIED')) {
                    $namespace .= $token[1];
                    continue;
                }
                if (defined('T_NAME_FULLY_QUALIFIED') && $id === constant('T_NAME_FULLY_QUALIFIED')) {
                    $namespace .= ltrim($token[1], '\\');
                    continue;
                }
                if ($id === T_WHITESPACE || $id === T_COMMENT || $id === T_DOC_COMMENT) {
                    continue;
                }
            } else {
                if ($token === ';' || $token === '{') {
                    break;
                }
            }

            break;
        }

        return trim($namespace, '\\');
    }

    /**
     * @param array<int, array{0:int,1:string,2:int}|string> $tokens
     */
    private function collectClassName(array $tokens, int $index): ?string
    {
        $tokenCount = count($tokens);
        for ($i = $index + 1; $i < $tokenCount; $i++) {
            $token = $tokens[$i];
            if (is_array($token)) {
                if ($token[0] === T_STRING) {
                    return $token[1];
                }

                if (in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                    continue;
                }
            } else {
                if ($token === '{' || $token === '(') {
                    break;
                }
            }

            break;
        }

        return null;
    }

    /**
     * @param array<int, array{0:int,1:string,2:int}|string> $tokens
     */
    private function isAnonymousClass(array $tokens, int $index): bool
    {
        for ($i = $index - 1; $i >= 0; $i--) {
            $token = $tokens[$i];
            if (is_array($token)) {
                if (in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                    continue;
                }

                return $token[0] === T_NEW;
            }

            if (trim((string) $token) === '') {
                continue;
            }

            return false;
        }

        return false;
    }

    private function analyzeCollectedClasses(): void
    {
        foreach ($this->class_map as $fqn => $info) {
            $file = $info['file'];

            if (
                !class_exists($fqn) &&
                !interface_exists($fqn) &&
                !trait_exists($fqn) &&
                (!function_exists('enum_exists') || !enum_exists($fqn))
            ) {
                if (is_file($file)) {
                    require_once $file;
                }
            }

            try {
                $reflection = new ReflectionClass($fqn);
            } catch (ReflectionException $exception) {
                $this->error('Reflection failed for ' . $fqn . ': ' . $exception->getMessage(), null, $file);
                continue;
            }

            $this->inspectClassMethods($reflection, $info['short']);
        }
    }

    private function inspectClassMethods(ReflectionClass $reflection, string $shortName): void
    {
        foreach ($reflection->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
                continue;
            }

            $methodKey = $shortName . '::' . $method->getName();
            $fileName = $method->getFileName();
            $line = $method->getStartLine() ?: null;
            $visibility = $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private');
            $signature = $this->formatMethodSignature($method);
            $fileLocation = $fileName ?? $reflection->getFileName() ?? null;

            $this->source_methods[$methodKey] = [
                'signature' => $signature,
                'visibility' => $visibility,
                'file' => $fileLocation,
                'line' => $line,
            ];

            if ($method->isPublic()) {
                $this->public_methods[$methodKey] = $this->source_methods[$methodKey];
            }

            $docComment = $method->getDocComment();
            if ($docComment === false || trim($docComment) === '') {
                $this->error(
                    sprintf('Method %s is missing a docblock.', $methodKey),
                    $line,
                    $fileLocation ?? $this->current_file
                );
                continue;
            }

            if ($method->isPublic() && stripos($docComment, '@see') === false) {
                $this->error(
                    sprintf('Docblock for %s must contain an @see tag referencing the dedicated documentation.', $methodKey),
                    $line,
                    $fileLocation ?? $this->current_file
                );
            }
        }
    }

    private function formatMethodSignature(ReflectionMethod $method): string
    {
        $parts = [];

        if ($method->isAbstract() && !$method->getDeclaringClass()->isTrait()) {
            $parts[] = 'abstract';
        }

        if ($method->isFinal()) {
            $parts[] = 'final';
        }

        $parts[] = $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private');

        if ($method->isStatic()) {
            $parts[] = 'static';
        }

        $parts[] = 'function';
        $parts[] = $method->getName();

        $parameters = array_map(
            fn (ReflectionParameter $parameter): string => $this->formatParameter($parameter),
            $method->getParameters()
        );

        $signature = implode(' ', array_filter($parts)) . '(' . implode(', ', $parameters) . ')';

        $returnType = $method->getReturnType();
        if ($returnType !== null) {
            $signature .= ': ' . $this->formatType($returnType);
        }

        return $signature;
    }

    private function formatParameter(ReflectionParameter $parameter): string
    {
        $code = '';
        $type = $parameter->getType();
        if ($type !== null) {
            $code .= $this->formatType($type) . ' ';
        }

        if ($parameter->isPassedByReference()) {
            $code .= '&';
        }

        if ($parameter->isVariadic()) {
            $code .= '...';
        }

        $code .= '$' . $parameter->getName();

        if ($parameter->isDefaultValueAvailable()) {
            if ($parameter->isDefaultValueConstant()) {
                $default = $parameter->getDefaultValueConstantName();
                if ($default === null) {
                    $default = 'null';
                }
            } else {
                try {
                    $value = $parameter->getDefaultValue();
                } catch (ReflectionException $exception) {
                    $value = null;
                }

                $default = $this->exportValue($value);
            }

            $code .= ' = ' . $default;
        }

        return trim($code);
    }

    private function formatType(ReflectionType $type): string
    {
        if ($type instanceof ReflectionUnionType) {
            return implode('|', array_map(fn (ReflectionType $inner): string => $this->formatType($inner), $type->getTypes()));
        }

        if ($type instanceof ReflectionIntersectionType) {
            return implode('&', array_map(fn (ReflectionType $inner): string => $this->formatType($inner), $type->getTypes()));
        }

        if ($type instanceof ReflectionNamedType) {
            $name = $type->getName();
            $short = $this->shortenTypeName($name);

            if ($short === 'mixed') {
                return 'mixed';
            }

            if ($short === 'null') {
                return 'null';
            }

            if ($type->allowsNull()) {
                return '?' . $short;
            }

            return $short;
        }

        return (string) $type;
    }

    private function shortenTypeName(string $type): string
    {
        $type = ltrim($type, '\\');
        if ($type === 'self' || $type === 'static' || $type === 'parent') {
            return $type;
        }

        $position = strrpos($type, '\\');
        if ($position === false) {
            return $type;
        }

        return substr($type, $position + 1);
    }

    private function exportValue(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value)) {
            return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $value) . "'";
        }

        if (is_array($value)) {
            if ($value === []) {
                return '[]';
            }

            return var_export($value, true);
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            if (is_nan($value)) {
                return 'NAN';
            }
            if (is_infinite($value)) {
                return $value > 0 ? 'INF' : '-INF';
            }

            $string = (string) $value;
            if (!str_contains($string, '.') && !str_contains($string, 'e') && !str_contains($string, 'E')) {
                $string .= '.0';
            }

            return $string;
        }

        if (is_object($value)) {
            return 'object(' . $value::class . ')';
        }

        if (is_resource($value)) {
            return 'resource';
        }

        return var_export($value, true);
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
        if(!$this->run_test_block_generation && !$this->run_method_list_check) {
            return;
        }

        foreach ($iterator as $fileinfo) {
            $path = $fileinfo->getPathname();
            $relativePath = $this->relativePath($path);

            if ($this->isExcludedPath($relativePath) || $fileinfo->isDir()) {
                continue;
            }

            $this->current_file = $path;

            if (!str_ends_with($fileinfo->getFilename(), '.md')) {
                continue;
            }

            echo $relativePath . ': ' . PHP_EOL;

            $content = file_get_contents($path);
            if($content === false) {
                $this->error('Unable to read documentation file.', null, $path);
                continue;
            }

            $this->file_metrics['doc_files_scanned']++;

            if($this->run_test_block_generation){
                $this->processTestBlocks($content, $relativePath);
            }

            if($this->run_method_list_check){
                $this->processMethodList($content, $path);
            }
        }
    }

    public function secondWalkItems($iterator, $current = null): void
    {
        if(!$this->run_all_urls_check) {
            return;
        }

        foreach ($iterator as $fileinfo) {
            $path = $fileinfo->getPathname();
            $relativePath = $this->relativePath($path);

            if ($this->isExcludedPath($relativePath) || $fileinfo->isDir()) {
                continue;
            }

            $this->current_file = $path;

            if (!str_ends_with($fileinfo->getFilename(), '.md')) {
                continue;
            }

            echo $relativePath . ': ' . PHP_EOL;

            $content = file_get_contents($path);
            if($content === false) {
                $this->error('Unable to read documentation file.', null, $path);
                continue;
            }

            $this->checkIfAllDocUrlsExistAndAreValid($content, dirname($path));
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
        $docsPrefix = 'docs' . DIRECTORY_SEPARATOR;
        if((str_starts_with($rel_path, DIRECTORY_SEPARATOR . $docsPrefix) || str_starts_with($rel_path, $docsPrefix)) && count($blocks) < 1) {
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

    private function processMethodList(string $content, string $absolutePath): void
    {
        $lines = preg_split('/\r\n|\n|\r/', $content);
        if ($lines === false) {
            $this->error('Unable to parse documentation table contents.', null, $absolutePath);
            return;
        }

        $lineCount = count($lines);

        for ($index = 0; $index < $lineCount; $index++) {
            $line = $lines[$index];

            if (!preg_match('/^\|\s*Method\s*\|/i', $line)) {
                continue;
            }

            $dividerIndex = $index + 1;
            if ($dividerIndex >= $lineCount || !preg_match('/^\|\s*-{3,}\s*\|/', $lines[$dividerIndex])) {
                $this->warning('Method list header must be followed by a table divider row.', $index + 1, $absolutePath);
                continue;
            }

            for ($rowIndex = $dividerIndex + 1; $rowIndex < $lineCount; $rowIndex++) {
                $row = $lines[$rowIndex];
                $trimmed = trim($row);

                if ($trimmed === '' || !str_starts_with($trimmed, '|')) {
                    $index = $rowIndex - 1;
                    break;
                }

                $this->processMethodListRow($row, $rowIndex + 1, $absolutePath);
            }
        }
    }

    private function processMethodListRow(string $row, int $lineNumber, string $absolutePath): void
    {
        $cells = preg_split('/\s*\|\s*/', trim($row, " \t\n\r\0\x0B|"));
        if ($cells === false || count($cells) < 2) {
            $this->warning('Unable to parse method list row.', $lineNumber, $absolutePath);
            return;
        }

        $methodCell = array_shift($cells);
        $methodName = null;
        $docLink = null;
        $resolvedDocPath = null;

        if (preg_match('/\[`(?P<method>[^`]+)`\]\((?P<path>[^)]+)\)/', $methodCell, $linkMatch)) {
            $methodName = trim($linkMatch['method']);
            $docLink = trim($linkMatch['path']);
        } elseif (preg_match('/`(?P<method>[A-Za-z0-9_\\\\]+::[A-Za-z0-9_]+)`/', $methodCell, $nameMatch)) {
            $methodName = trim($nameMatch['method']);
            $this->warning('Method list entry is missing a link to dedicated documentation.', $lineNumber, $absolutePath);
        } else {
            $this->warning('Method list row must contain a backticked method name.', $lineNumber, $absolutePath);
            return;
        }

        if ($methodName === '') {
            $this->warning('Method list entry contains an empty method name.', $lineNumber, $absolutePath);
            return;
        }

        if ($docLink !== null) {
            if (str_contains($docLink, '\\')) {
                $this->error('Doc path contains backslashes (\\) instead of forward slashes (/).', $lineNumber, $absolutePath);
            } else {
                $resolvedDocPath = $this->root_folder . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $docLink);
                $this->processed_files[] = $resolvedDocPath;

                $methodParts = explode('::', $methodName);
                $methodShort = array_pop($methodParts);
                if ($methodShort !== null && !str_ends_with($docLink, $methodShort . '.md')) {
                    $this->warning(
                        sprintf('Method name (%s) does not align with filename in doc path (%s).', $methodName, $docLink),
                        $lineNumber,
                        $absolutePath
                    );
                }

                if (!$this->path_exists_strict($resolvedDocPath)) {
                    $this->method_list_missing_count++;
                    $this->error('Doc file not found: ' . $resolvedDocPath, $lineNumber, $absolutePath);
                }
            }
        }

        $signature = null;
        foreach ($cells as $cell) {
            if (preg_match('/`([^`]+)`/', $cell, $match)) {
                $signature = trim($match[1]);
                break;
            }
        }

        if ($signature === null) {
            $this->warning('Method list row is missing an inline code signature.', $lineNumber, $absolutePath);
            return;
        }

        $this->documented_methods[$methodName][] = [
            'signature' => $signature,
            'line' => $lineNumber,
            'file' => $absolutePath,
            'doc_path' => $resolvedDocPath,
        ];
    }

    private function checkMethodDocumentationCoverage(): void
    {
        foreach ($this->public_methods as $methodName => $metadata) {
            if (!isset($this->documented_methods[$methodName])) {
                $this->warning(
                    sprintf('Public method %s is missing from all method lists.', $methodName),
                    $metadata['line'],
                    $metadata['file']
                );
            }
        }

        foreach ($this->documented_methods as $methodName => $entries) {
            if (!isset($this->source_methods[$methodName])) {
                foreach ($entries as $entry) {
                    $this->warning(
                        sprintf('Documented method %s does not exist in the source code.', $methodName),
                        $entry['line'],
                        $entry['file']
                    );
                }
                continue;
            }

            $actualSignature = $this->source_methods[$methodName]['signature'];

            foreach ($entries as $entry) {
                $normalizedDocSignature = $this->normalizeDocSignature($entry['signature']);
                if ($normalizedDocSignature !== $actualSignature) {
                    $this->error(
                        sprintf(
                            'Signature mismatch for %s. Documented: %s | Actual: %s',
                            $methodName,
                            $entry['signature'],
                            $actualSignature
                        ),
                        $entry['line'],
                        $entry['file']
                    );
                }
            }
        }
    }

    private function normalizeDocSignature(string $signature): string
    {
        $normalized = trim($signature);
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\b(array|list)<[^>]+>/', '$1', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s*\(\s*/', '(', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s*\)\s*/', ')', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s*,\s*/', ', ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s*:\s*/', ': ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s*=\s*/', ' = ', $normalized) ?? $normalized;

        $normalized = preg_replace_callback(
            '/(?<!:)(?:\\\\[A-Za-z_][A-Za-z0-9_]*)+/',
            static function (array $match): string {
                $value = $match[0];
                $pos = strrpos($value, '\\');
                if ($pos === false) {
                    return ltrim($value, '\\');
                }

                return substr($value, $pos + 1);
            },
            $normalized
        ) ?? $normalized;

        return trim($normalized);
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

    private function error(string $message, ?int $line = null, ?string $file = null): void
    {
        $this->recordIssue('errors', $message, $line, $file);
    }

    private function warning(string $message, ?int $line = null, ?string $file = null): void
    {
        $this->recordIssue('warnings', $message, $line, $file);
    }

    private function recordIssue(string $type, string $message, ?int $line, ?string $file = null): void
    {
        $targetFile = $file ?? $this->current_file ?? '[unknown]';

        if ($type === 'errors') {
            $this->error_count++;
            $this->errors[$targetFile][] = ['message' => $message, 'line' => $line];
        } else {
            $this->warning_count++;
            $this->warnings[$targetFile][] = ['message' => $message, 'line' => $line];
        }
    }

    private function outputReport(): void
    {
        $this->outputIssues('errors', $this->errors, 'ERRORS');
        $this->outputIssues('warnings', $this->warnings, 'WARNINGS');

        echo 'Summary:' . PHP_EOL;
        echo '  PHP files scanned: ' . $this->file_metrics['php_files_scanned'] . PHP_EOL;
        echo '  Documentation files scanned: ' . $this->file_metrics['doc_files_scanned'] . PHP_EOL;
        echo '  Method-list missing docs count: ' . $this->method_list_missing_count . PHP_EOL;
        echo '  All URLs missing docs count: ' . $this->all_urls_missing_count . PHP_EOL;
        echo '  File count in /docs/ with no test blocks: ' . $this->test_blocks_docs_missing_count . PHP_EOL;
        echo '  Total test blocks found: ' . $this->test_blocks_found_count . PHP_EOL;
        echo '  Errors: ' . $this->error_count . PHP_EOL;
        echo '  Warnings: ' . $this->warning_count . PHP_EOL;

        $limitMessages = [];
        if ($this->error_limit !== null && $this->error_count > $this->error_limit) {
            $limitMessages[] = sprintf('Error limit exceeded (%d/%d).', $this->error_count, $this->error_limit);
        }
        if ($this->warning_limit !== null && $this->warning_count > $this->warning_limit) {
            $limitMessages[] = sprintf('Warning limit exceeded (%d/%d).', $this->warning_count, $this->warning_limit);
        }

        if (!empty($limitMessages)) {
            echo 'Limits:' . PHP_EOL;
            foreach ($limitMessages as $message) {
                echo '  - ' . $message . PHP_EOL;
            }
        }
    }

    /**
     * @param array<string, array<int, array{message: string, line: int|null}>> $issues
     */
    private function outputIssues(string $type, array $issues, string $label): void
    {
        if (empty($issues)) {
            echo $label . ': none' . PHP_EOL;
            return;
        }

        [$color, $reset] = $this->issueColors($type);

        ksort($issues);
        foreach ($issues as $file => $entries) {
            echo $color . '  ' . $label . ' in ' . $file . $reset . PHP_EOL;
            foreach ($entries as $entry) {
                $lineSuffix = $entry['line'] !== null ? ' (line ' . $entry['line'] . ')' : '';
                echo '    - ' . $entry['message'] . $lineSuffix . PHP_EOL;
            }
        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function issueColors(string $type): array
    {
        if (DIRECTORY_SEPARATOR === '\\' && function_exists('sapi_windows_vt100_support')) {
            @sapi_windows_vt100_support(STDOUT, true);
            @sapi_windows_vt100_support(STDERR, true);
        }

        $reset = "\033[0m";

        if ($type === 'errors') {
            return ["\033[1;31m", $reset];
        }

        $orange = "\033[38;2;255;165;0m";
        $yellow = "\033[1;33m";
        $truecolorLikely =
            getenv('WT_SESSION') ||
            getenv('TERM_PROGRAM') === 'vscode' ||
            getenv('ConEmuANSI') === 'ON' ||
            getenv('ANSICON');

        return [$truecolorLikely ? $orange : $yellow, $reset];
    }

    private function determineExitCode(): int
    {
        $errorExceeded = $this->error_limit !== null && $this->error_count > $this->error_limit;
        $warningExceeded = $this->warning_limit !== null && $this->warning_count > $this->warning_limit;

        return ($errorExceeded || $warningExceeded) ? 1 : 0;
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