# Contributing To Hey Woo

Hey Woo is a WordPress plugin for the WooCommerce admin. It owns the
bring-your-own-key Ask Claude chat experience for merchants who provide an
Anthropic API key.

This is a public repository. Do not commit secrets, API keys, customer data,
order exports, internal-only URLs, or anything not intended for public
distribution.

## Product Boundaries

Hey Woo owns:

- WordPress-admin Ask Claude chat.
- Conversation history.
- BYOK settings and Anthropic API calls.
- Workflow skills used by the admin chat.
- The `hey-woo.zip` release.

WooCommerce for Claude owns:

- The external Claude/MCP product.
- MCP setup, `.mcpb` bundles, and WooCommerce REST API key provisioning.
- Product/readiness abilities that Hey Woo can use when both plugins are
  installed.

The shared `woocommerce/commerce-abilities` package is carried in this
repository under `php-packages/commerce-abilities` for release builds. Composer
installs it into `vendor/woocommerce/commerce-abilities`, and the release zip
includes that vendored copy.

## Privacy Rule

Analytics responses are aggregated counts, sums, averages, and grouped rows.
Do not expose customer names, emails, addresses, order exports, or other PII to
AI by default.

If a future feature needs PII, it must be opt-in, clearly justified by the
response shape, and reviewed as a privacy-sensitive change.

## Merchant-Scope Rule

Merchant-facing text must speak to a merchant, not a plugin developer. Do not
tell merchants to add a tool, register a REST endpoint, or edit plugin code.

When the product cannot answer something, steer the merchant to an action they
can take: a setting, connector, export/manual workflow, or a clear explanation
that the current tools cannot answer it.

## Local Development

```bash
pnpm install
composer install
pnpm run start
pnpm run lint:php
pnpm run plugin-zip
unzip -t hey-woo.zip
```

Do not commit generated artefacts:

- `vendor/`
- `build/`
- `node_modules/`
- `hey-woo.zip`

## Release Ownership

Hey Woo releases are built from this repository only. Do not publish
`hey-woo.zip` from the WooCommerce for Claude monorepo.

See [RELEASING.md](./RELEASING.md) for the release checklist.

## Syncing From The Monorepo

During the transition, release-bound changes may start in the WooCommerce for
Claude monorepo. Sync those changes into this repository before releasing Hey
Woo.

See [SYNCING.md](./SYNCING.md) for the copy/preserve checklist.
