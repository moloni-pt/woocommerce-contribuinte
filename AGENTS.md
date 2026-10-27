# AGENTS.md — woocommerce-contribuinte

Guidance for AI coding agents (Claude Code, Cursor, Copilot, …) and new developers working in this Moloni plugin. This is the canonical context file; `CLAUDE.md` imports it.

> Draft: **fill in the TODOs**, seeding from this plugin's README.

## What this plugin is

TODO: one paragraph — which host platform it extends (WooCommerce/WordPress, PrestaShop, OpenCart, Magento, Joomla/VirtueMart, WHMCS, Shopify, Shopkit, Wix), and what it syncs to Moloni. It is a PHP module that integrates with Moloni **over the public API** (api.moloni.pt) — it does not run alongside any Moloni platform or call internal services.

## Responsibilities & boundaries

TODO: what this plugin **owns** (which host events it reacts to, what it pushes/pulls to Moloni) and what it **must not do**. Note the Moloni API surface it depends on. An agent uses this to judge whether a change belongs here at all.

## Architecture map

TODO: the top-level layout and what each part does — the entrypoint, the host-integration layer (hooks/actions/observers/module lifecycle), the Moloni API client, and any submodules (`git submodule …`) that must be initialized.

## Running locally

TODO: how to install and run.
- `composer install` (where the plugin uses Composer), then how the module loads into a local host (WordPress/WooCommerce, PrestaShop, OpenCart, Magento, Joomla/VirtueMart, WHMCS, …) — host version, dev DB, install path.
- If the plugin builds front-end assets (npm/gulp), the build step.
- Where Moloni API credentials + host settings are configured (in the host, never committed).

## Commands

TODO: fill the real scripts/steps for this plugin.

| Command                     | What it does                              |
| --------------------------- | ----------------------------------------- |
| `composer install`          | Install PHP dependencies (if used)        |
| `npm install && npm run build` | (if used) build front-end assets       |
| `phpcs` / `phpunit`         | (if configured) lint + tests              |

**To verify a change:** run this plugin's `phpcs`/`phpunit` where it has them; where it doesn't, load the module into a local host and exercise the flow.

## Conventions

- TODO: host-platform conventions (hook/action/observer naming, module lifecycle, the host's expected layout).
- TODO: how Moloni API credentials + host settings are configured (in the host settings / config) — never hard-coded, never committed.
- **Idempotency + fiscal correctness are load-bearing** — document creation must not double-issue; tax/document-type mappings are fiscal-legal.

## Testing

- TODO: test framework + how it runs (PHPUnit if present), or "no automated suite — verify by loading into a local host and exercising the flow".

## Gotchas

Curated, verified conventions and traps. This is the default-trusted source — every agent reads it.

- TODO: anything non-obvious that has bitten people (submodules, host version quirks, cron/hook retries, API rate limits, sandbox setup).

> Recent, **unverified** findings live in `.claude/journal/` (one dated file per finding). They are promoted into this section by manual curation once verified — treat them as leads, not yet rules.
