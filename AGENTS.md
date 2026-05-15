# Hey Woo

Hey Woo is the bring-your-own-key WordPress-admin chat plugin for WooCommerce.
It is a public repo: never commit secrets, API keys, customer data, order
exports, internal-only URLs, or anything not intended for public distribution.

Read [CONTRIBUTING.md](./CONTRIBUTING.md) before making code or docs changes.

## Highest-Stakes Rules

- **Privacy:** analytics data returned to AI must be aggregated by default. Do
  not expose customer names, emails, addresses, order exports, or other PII.
- **Merchant scope:** merchant-facing text must not ask merchants to add tools,
  register endpoints, or edit plugin code. Offer settings, connectors,
  export/manual workflows, or honest product limits instead.
- **Release ownership:** `hey-woo.zip` is released from this repository only.
  The WooCommerce for Claude monorepo may carry an integration copy, but it must
  not publish Hey Woo releases.

## Layout

```text
hey-woo/
|-- hey-woo.php                       # WordPress plugin bootstrap
|-- includes/
|   |-- difm/                         # Ask Claude REST controllers, chat page, workflow skills
|   |-- settings/                     # WooCommerce > Settings > Hey Woo tab
|   `-- telemetry/                    # BYOK request logging hooks
|-- packages/                         # wp-build packages
|-- routes/ai-insights/               # Ask Claude admin UI route
|-- skills/                           # Workflow skill references for chat
|-- php-packages/commerce-abilities/  # Shared local Composer path package
|-- RELEASING.md
`-- SYNCING.md
```

## Local Commands

```bash
pnpm install
composer install
pnpm run start
pnpm run lint:php
pnpm run plugin-zip
unzip -t hey-woo.zip
```

Generated directories and artefacts stay uncommitted: `vendor/`, `build/`,
`node_modules/`, `.wp-env/`, and `hey-woo.zip`.

## Syncing

If a release-bound change starts in `woocommerce/woocommerce-claude`, follow
[SYNCING.md](./SYNCING.md). Preserve this repo's standalone ownership files,
especially `.github/`, `.wp-env.json`, `composer.json`, `package.json`,
`RELEASING.md`, and `SYNCING.md`.
