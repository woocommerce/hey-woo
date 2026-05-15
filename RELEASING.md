# Releasing Hey Woo

Hey Woo releases are built and published from this repository. Do not publish
`hey-woo.zip` from the WooCommerce for Claude monorepo.

If release-bound code changed first in the WooCommerce for Claude monorepo,
sync it into this repository using [SYNCING.md](./SYNCING.md) before starting
the release checklist.

## Release Checklist

1. Merge all release-bound PRs to `trunk`.
2. Bump the version in:
   - `hey-woo.php`
   - `package.json`
3. Run the local package checks:

   ```bash
   composer validate --strict
   pnpm install --frozen-lockfile
   pnpm run lint:php
   pnpm run plugin-zip
   unzip -t hey-woo.zip
   ```

4. Open and merge the version-bump PR.
5. In GitHub Actions, run **Release Hey Woo** from `trunk`.
6. Enter the version without the `v` prefix, for example `0.1.0`.
7. Keep the release as a draft until the generated notes and attached
   `hey-woo.zip` are reviewed.

The release workflow validates that the requested version matches the
`Version:` header in `hey-woo.php`, builds the plugin zip, tests the zip, creates
the `v<version>` tag, and attaches `hey-woo.zip` to the GitHub Release.
