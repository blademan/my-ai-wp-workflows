# CLAUDE.md

Standing instructions for every WordPress task in this repo (Bricks Builder, ETCH, custom themes, Astro).

## Rule 1: read the matching SOP first

For any WordPress task (theme conversion, app-like theme, MCP setup, PageSpeed work), you MUST read the matching file in `./sop-library/` **before** writing code or giving advice, then follow it step by step. Finish by working through the SOP's **Definition of Done** checklist and report which boxes are ticked and which are not. A task is not done until every box is ticked.

Start with `sop-library/00-README.md` if you are unsure which SOP applies. Do not edit the SOP files unless I explicitly ask.

## Which SOP applies

| Situation | Read |
|-----------|------|
| Finished static HTML/Tailwind design must ship as a custom WordPress theme | `sop-library/01-html-to-wordpress-conversion.md` |
| Greenfield "app-like" theme: CPT/SCF-driven, no editor, minimal admin, dark mode | `sop-library/02-ai-wordpress-app-theme-blueprint.md` |
| Connecting Claude / IDE agents to a local, staging or client WordPress site | `sop-library/03-mcp-setup-wordpress.md` |
| Optimizing Bricks + FlyingPress (+ Perfmatters) for Core Web Vitals | `sop-library/04-wordpress-pagespeed-playbook.md` |

Choosing an approach for a new project (from `00-README.md`):

| Situation | Path |
|-----------|------|
| Client edits content visually, agency maintains layout | Bricks or ETCH + SOP 04 |
| Design arrives as finished HTML, content is structured/repeatable | SOP 01 (+ SOP 02 for admin/dark-mode patterns) |
| Data-driven tool/dashboard/changelog/roadmap, one editor role | SOP 02 |
| Mostly static content, performance first, no client editing | Astro static site (outside the SOP library) |

If a task spans several situations, read every matching SOP.

## Cross-cutting rules (always apply)

- **Escape on output** (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`); **sanitize on input**.
- **Nonce + capability check** on every state-changing request.
- **PHP 8.2+** compatible code only; no deprecated functions.
- **Never commit or print secrets**: no passwords, app passwords, tokens or API keys in code, docs, repos or `claude_desktop_config.json` examples. Use environment variables or a password manager.
- **Staging before production**: nothing runs on production until it has passed on staging.
- **Prefix everything** (functions, classes, constants, hooks, options, CPTs, handles) with the project prefix. Replace the SOPs' `acme_` / `ACME_` placeholder with it. If the prefix is not stated for the project, ask me; do not guess.
- Keep custom code in version control (theme, mu-plugin or Scripts Organizer export), never only in a database.
- Record non-obvious decisions in the project's `README.md`.

## ⚠ VERIFY items

Items tagged `⚠ VERIFY` in the SOPs (hook names, package names, CLI flags) change between vendor versions. Before using one, check it against the current vendor docs or the installed version. In your reply, tell me which `⚠ VERIFY` items you checked, what source you used, and the result. If you could not verify one, say so and flag it as unverified instead of using it silently. Removing a tag and bumping the SOP version is a change to the SOP files, so ask me first.

## Git rules

- Never push to `main` or force-push without my explicit approval.
- Ask before pushing any branch.
