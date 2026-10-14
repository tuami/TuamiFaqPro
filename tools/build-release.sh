#!/usr/bin/env bash
set -euo pipefail
root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
export DO_NOT_TRACK=1
command -v shopware-cli >/dev/null || { echo 'Install Shopware CLI in Linux/WSL, macOS or Docker first.' >&2; exit 1; }
shopware-cli extension validate "$root" --only builtin
# Always package the current working tree, never an older Git tag.
shopware-cli extension package "$root" --disable-git --release
