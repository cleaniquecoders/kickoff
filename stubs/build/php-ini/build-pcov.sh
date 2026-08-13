#!/usr/bin/env bash
#
# Build and install the pcov coverage driver for the PHP on this machine.
#
# Pest's Test Impact Analysis (`composer test-tia`) needs a coverage driver to
# record which files each test touches. Xdebug can do it, but a cold record of
# a mid-sized suite under Xdebug takes on the order of ninety minutes; under
# pcov the same record takes about seventy seconds.
#
# pcov is normally installable with `pecl install pcov`. Try that first — this
# script exists for the case where no build is published for your PHP version
# yet (which is usual for the newest release), so it has to be compiled.
#
# Two things are easy to get wrong and both fail in ways that point elsewhere:
#
#   1. --enable-pcov=shared is required. Built static, `ZEND_GET_MODULE` is
#      compiled out, so the .so has no `get_module` symbol and PHP rejects it
#      at startup with "Invalid library (maybe not a PHP library)" — which
#      reads like a version incompatibility and is not one.
#   2. Homebrew's PHP headers include "pcre2.h" without adding pcre2 to the
#      include path, so CPPFLAGS has to point at it.
#
# Re-run this after any PHP upgrade: the extension is installed into a
# version-specific directory (…/pecl/<api>/) and will not survive one.

set -euo pipefail

# Resolve this before any cd — the build happens in a temp directory.
INI_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

PCOV_REF="${PCOV_REF:-develop}"
PHP_CONFIG="${PHP_CONFIG:-$(command -v php-config)}"

# Homebrew only. On Linux the distro headers put pcre2 on the include path
# already, so leave CPPFLAGS empty rather than guessing at a prefix.
if [ -z "${PCRE2_PREFIX:-}" ] && command -v brew > /dev/null 2>&1; then
    PCRE2_PREFIX="$(brew --prefix pcre2 2>/dev/null || true)"
fi

EXTRA_CPPFLAGS=""
if [ -n "${PCRE2_PREFIX:-}" ]; then
    EXTRA_CPPFLAGS="-I${PCRE2_PREFIX}/include"
fi

WORKDIR="$(mktemp -d)"
trap 'rm -rf "$WORKDIR"' EXIT

echo "==> Building pcov (${PCOV_REF}) against $("$PHP_CONFIG" --version)"

curl -fsSL "https://github.com/krakjoe/pcov/archive/refs/heads/${PCOV_REF}.tar.gz" \
    | tar xz -C "$WORKDIR"

cd "$WORKDIR"/pcov-*

phpize > /dev/null

CPPFLAGS="$EXTRA_CPPFLAGS" ./configure \
    --with-php-config="$PHP_CONFIG" \
    --enable-pcov=shared > /dev/null

# -DCOMPILE_DL_PCOV=1 is belt and braces alongside --enable-pcov=shared; it is
# the define that exposes get_module, and the build is useless without it.
make -j"$(getconf _NPROCESSORS_ONLN)" \
    CPPFLAGS="$EXTRA_CPPFLAGS -DCOMPILE_DL_PCOV=1" > /dev/null

# Not fatal if nm is unavailable or uses different flags — the load check below
# is the real gate. This one just names the cause when it fails.
if command -v nm > /dev/null 2>&1; then
    if ! nm -g modules/pcov.so 2>/dev/null | grep -q get_module; then
        echo "!! pcov.so has no get_module symbol — PHP would refuse to load it." >&2
        echo "   Built static instead of shared? Check --enable-pcov=shared." >&2
        exit 1
    fi
fi

make install

echo "==> Verifying"
PHP_INI_SCAN_DIR="$INI_DIR" php -r '
    if (! extension_loaded("pcov")) {
        fwrite(STDERR, "pcov did NOT load\n");
        exit(1);
    }
    printf("pcov %s loaded OK\n", phpversion("pcov"));
'
