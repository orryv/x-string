# Documentation updates for initial methods

The following method guides were created and now include runnable examples that are converted into PHPUnit tests:

- [`XString::new`](x-string/methods/new.md)
- [`XString::randInt`](x-string/methods/randInt.md)
- [`XString::randLower`](x-string/methods/randLower.md)
- [`XString::randUpper`](x-string/methods/randUpper.md)
- [`XString::randAlpha`](x-string/methods/randAlpha.md)

Each document follows the shared template (technical details, parameter tables, exceptions, and example sections) and provides
testable PHP snippets annotated with `<!-- test:... -->` comments.

The tests generated from these examples live under `tests/Docs/docs/x-string/methods/` and are executed with `composer test`.
