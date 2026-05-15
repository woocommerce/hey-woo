# Syncing From The Monorepo

Hey Woo is released from this repository. During the monorepo transition, the
WooCommerce for Claude monorepo may still carry an integration copy at
`plugins/hey-woo/` plus the shared `php-packages/commerce-abilities/` package.

Use this checklist when bringing release-bound changes from the monorepo into
this repository.

## What To Copy

From `woocommerce/woocommerce-claude`:

- `plugins/hey-woo/hey-woo.php`
- `plugins/hey-woo/includes/`
- `plugins/hey-woo/packages/`
- `plugins/hey-woo/routes/`
- `plugins/hey-woo/shims/`
- `plugins/hey-woo/skills/`
- `plugins/hey-woo/LICENSE`
- `php-packages/commerce-abilities/`

## What To Preserve Here

Do not overwrite standalone-repo ownership files without reviewing the diff:

- `.github/`
- `.gitignore`
- `.wp-env.json`
- `README.md`
- `RELEASING.md`
- `SYNCING.md`
- `composer.json`
- `package.json`

The standalone `composer.json` must keep the path repository URL as
`php-packages/commerce-abilities`. The standalone `package.json` must keep
self-contained build scripts that call `wp-build`, `wp-scripts`, and `wp-env`
from this repository's own `node_modules`.

## After Syncing

Refresh dependencies and verify the release package:

```bash
composer update --lock --no-interaction
composer validate --strict
pnpm install --frozen-lockfile
pnpm run lint:php
pnpm run plugin-zip
unzip -t hey-woo.zip
```

Open a PR in this repository with a summary of the monorepo commit or PR that
was synced. Merge that PR before running the **Release Hey Woo** workflow.
