---
title: WordPress SOP Library — Index
version: 2.0
last_reviewed: 2026-10-01
review_cadence: quarterly (or when WordPress / Bricks / FlyingPress / MCP major versions ship)
---

# WordPress SOP Library

| # | SOP | Use when |
|---|-----|----------|
| 01 | `01-html-to-wordpress-conversion.md` | You have a finished static HTML/Tailwind design and must ship it as a custom WordPress theme |
| 02 | `02-ai-wordpress-app-theme-blueprint.md` | Greenfield "app-like" theme: CPT/SCF-driven, no editor, minimal admin, dark mode |
| 03 | `03-mcp-setup-wordpress.md` | Connecting Claude / IDE agents to a local, staging or client WordPress site |
| 04 | `04-wordpress-pagespeed-playbook.md` | Optimizing Bricks + FlyingPress (+ Perfmatters) sites for Core Web Vitals |

## Which approach for a new project?

| Situation | Recommended path |
|-----------|------------------|
| Client edits content visually, agency maintains layout | Bricks or ETCH + SOP 04 |
| Design arrives as finished HTML, content is structured/repeatable | SOP 01 (+ SOP 02 for admin/dark-mode patterns) |
| Data-driven tool/dashboard/changelog/roadmap, one editor role | SOP 02 |
| Content is mostly static, performance is the top priority, no client editing | Astro static site (outside this library) |

## Conventions used in every SOP

1. **Header block**: version, last reviewed, applies-to, prerequisites.
2. **Definition of Done** at the end of each SOP — a SOP is not finished until every box is ticked.
3. **Verify tags**: items marked `⚠ VERIFY` depend on third-party hooks/package names that change between versions. Check the vendor docs before relying on them, then remove the tag and bump the version.
4. **Prefix everything**: replace `acme_` / `ACME_` with the project prefix (e.g. `ddc_`).
5. **Never commit secrets**: no passwords, app passwords or API keys in SOPs, repos or `claude_desktop_config.json` examples. Use environment variables or a password manager.
6. **Staging first**: nothing in these SOPs is run on production before it has passed on staging.

## Cross-cutting rules (apply to all SOPs)

- Escape on output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`), sanitize on input, nonce + capability check on every state-changing request.
- PHP 8.2+ compatible code only (no deprecated functions).
- Keep custom code in version control (theme, mu-plugin or Scripts Organizer export) — never only in a database.
- Record every non-obvious decision in the project's `README.md` (why a script is excluded from delay, why a preload is allowed, etc.).

## Changelog

- **2.0 (2026-10-01)** — Full restructure. Added decision table, DoD checklists, security and QA phases; replaced outdated MCP packages with the official WordPress MCP Adapter; fixed PHP 8.2 deprecation and eager-image issues in the PageSpeed code; removed the "redirect all 404s to Home" pattern.
