# Hey Woo

Bring-your-own-key WooCommerce assistant plugin.

Hey Woo owns the WordPress-admin chat experience for merchants who provide an
Anthropic API key. It installs the shared `woocommerce/commerce-abilities`
package with Composer's path repository, boots the shared analytics abilities,
and exposes the Ask Claude screen plus its supporting REST endpoints from this
plugin.

WooCommerce for Claude still owns the external MCP product. When that plugin is
also active, Hey Woo can use its product and readiness abilities in the chat
tool bridge; otherwise the admin chat runs with the shared analytics tools.

## Development

Read [CONTRIBUTING.md](./CONTRIBUTING.md) before opening a PR.

```bash
pnpm install
composer install
pnpm run start
pnpm run lint:php
pnpm run plugin-zip
unzip -t hey-woo.zip
```

The repository carries `php-packages/commerce-abilities` as the release-time
source for the `woocommerce/commerce-abilities` Composer path package. Composer
installs it into `vendor/woocommerce/commerce-abilities`, and
`@wordpress/scripts plugin-zip` includes that vendored package in
`hey-woo.zip`.

Do not commit `vendor/`, `build/`, or `hey-woo.zip`; CI and the release
workflow rebuild them.

## Releases

Hey Woo releases are built from this repository. See [RELEASING.md](./RELEASING.md).

If a change starts in the WooCommerce for Claude monorepo, follow
[SYNCING.md](./SYNCING.md) before releasing it here.
