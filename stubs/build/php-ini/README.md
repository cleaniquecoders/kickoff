# build/php-ini

An extra PHP ini directory, loaded by pointing `PHP_INI_SCAN_DIR` at it. Only
the coverage-collecting composer scripts do so:

| Script | Coverage driver | Notes |
|---|---|---|
| `composer test` | none | `XDEBUG_MODE=off` — the default, no prerequisites |
| `composer test-parallel` | none | `XDEBUG_MODE=off` |
| `composer test-arch` | none | `XDEBUG_MODE=off` |
| `composer test-tia` | pcov | needs pcov installed (below) |
| `composer test-tia-fresh` | pcov | discards the graph and re-records |
| `composer test-coverage` | pcov | |

`PHP_INI_SCAN_DIR` rather than `php -d` because ParaTest spawns its worker
processes without the parent's `-d` flags — an environment variable is the only
thing that reaches them. For the same reason the composer scripts use the
array + `@putenv` form rather than an inline `VAR=x` command prefix: `@putenv`
keeps `@php` (so Composer picks the right PHP binary), works on Windows, and
still forwards extra CLI arguments.

## Installing pcov

```bash
pecl install pcov
```

If no build is published for your PHP version yet, compile it:

```bash
bash build/php-ini/build-pcov.sh
```

**Re-run this after any PHP upgrade** — the extension installs into a
version-specific `pecl/<api>/` directory and will not survive one. If pcov is
missing, `composer test-tia` fails with *"Recorded zero edges — coverage driver
likely missing."*

The build script exists because two failure modes are easy to hit and neither
says what it means:

- Built **static** rather than shared, `ZEND_GET_MODULE` is compiled out; the
  resulting `pcov.so` has no `get_module` symbol and PHP reports *"Invalid
  library (maybe not a PHP library)"*, which reads like a PHP-version
  incompatibility and is not one.
- Homebrew's PHP headers `#include "pcre2.h"` without putting pcre2 on the
  include path, so the build fails at `php_pcre.h` until `CPPFLAGS` points
  at `$(brew --prefix pcre2)/include`.

## Why TIA is not the default `composer test`

Test Impact Analysis has to *record* per-test coverage before it can skip
anything, so it needs a coverage driver — it is not free and it is not
zero-config. Under Xdebug a cold record of a mid-sized suite runs to about
ninety minutes, and Composer's default 300-second process timeout kills it part
way through, leaving a `worker-edges-*.json` and **no** `graph.json`. That
graph is unusable, so the next run re-records from scratch and dies the same
way, forever. Under pcov the same record takes about seventy seconds and a
replay about five.

So `composer test` runs the suite plainly with `XDEBUG_MODE=off` — correct on
any machine with no setup — and `composer test-tia` is the opt-in fast loop
once pcov is installed. Both TIA scripts disable the Composer process timeout
for the same reason.

## Why Xdebug is still loaded

Xdebug is typically a `zend_extension` in the main `php.ini`, not in `conf.d/`,
so `PHP_INI_SCAN_DIR` cannot unload it. That is fine: every test script sets
`XDEBUG_MODE=off`, which makes it inert, and PHPUnit then selects pcov as the
coverage driver. Leaving the mode at a configured `coverage` value instead
costs roughly **3×** on every run — Pest's own `XdebugRestarter` drops Xdebug
automatically, but only when TIA is replaying a valid graph, so it never covers
a plain run.
