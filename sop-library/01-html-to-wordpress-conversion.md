---
title: HTML → WordPress Theme Conversion
version: 2.0
last_reviewed: 2026-10-01
applies_to: WordPress 6.9+ / 7.x, PHP 8.2+, custom classic-PHP theme
prerequisites: finished static HTML design, Local (or equivalent) dev site, Git repo, staging environment
---

# HTML → WordPress Conversion SOP

Converts a high-fidelity static design into a maintainable, secure, client-ready WordPress theme.

> **Decide first:** if the client must edit layout visually, use Bricks/ETCH instead (see README decision table). This SOP is for structured, template-driven builds.

## Phase 0 — Source Audit & Tokens
- [ ] **Token extraction**: colors, type scale, spacing, radii, shadows → CSS custom properties (and `theme.json` if you want editor parity).
- [ ] **Assets**: icons → inline SVG (`currentColor`); images → WebP/AVIF at 1x/2x; fonts → self-hosted WOFF2 (subset, `font-display: swap`). Do not hot-link Google Fonts in production (privacy/GDPR + extra connection).
- [ ] **Naming**: map static class names to Tailwind utilities or BEM tokens once; document in `README.md`.
- [ ] **Accessibility pre-check**: heading order, contrast ≥ 4.5:1, focus states, alt text plan.
- [ ] **Content model sketch**: list every repeating block → candidate CPT / taxonomy / SCF group.

## Phase 1 — Theme Foundation
- [ ] Folder layout: `assets/{css,js,img,fonts}`, `inc/`, `template-parts/`, `screenshot.png` (1200×900).
- [ ] `style.css` (header metadata only if using a build step), `functions.php` (bootstrap only — `require` files from `inc/`).
- [ ] `theme.json` for color palette, font sizes, spacing (even in a classic theme — it feeds the editor and global styles).
- [ ] **Build pipeline**: Tailwind CLI/Vite build for production. *CDN Tailwind is for prototypes only* (render-blocking, no purge, no CSP).
- [ ] **Enqueue** via `wp_enqueue_style/script` with versioning (`filemtime`) and `['strategy' => 'defer']` for scripts. GSAP: load only on templates that use it (all GSAP plugins are free as of 2025 — confirm license note in project README).
- [ ] **Initial pages**: `after_switch_theme` hook → create Home, Contact, Thank You, Privacy; set `show_on_front=page`. Must be **idempotent** (check by slug/option flag before inserting).
- [ ] **Status dashboard widget**: environment, theme version, seeder status, links to docs.
- [ ] Disable emojis, `wp_generator`, RSD/WLW links, oEmbed discovery if unused.

## Phase 2 — Structural Skeleton
- [ ] `header.php` / `footer.php` with `wp_head()`, `wp_footer()`, `wp_body_open()`, `language_attributes()`.
- [ ] Menus registered with `register_nav_menus()` + `wp_nav_menu()` (no hard-coded links).
- [ ] `index.php`, `404.php` (real **404 status** page — do *not* redirect 404s to Home), `search.php`, `page.php`, `single.php`.
- [ ] Skip link, landmarks (`header/nav/main/footer`), one `<h1>` per page.
- [ ] Navigation audit: all URLs through `home_url()` / `get_permalink()`; anchors verified.

## Phase 3 — Data Engine & Media
- [ ] Register CPTs/taxonomies in `inc/post-types.php` with `show_in_rest => true`, proper `rewrite` slugs, `supports`, and `menu_icon`.
- [ ] Custom fields via **Secure Custom Fields** (registered in PHP, see SOP 02 Phase 4) or native meta + `register_post_meta`.
- [ ] **Media sideloading**: use `media_sideload_image()` / `wp_insert_attachment()` + `wp_generate_attachment_metadata()`; store original filename in meta to avoid duplicates on re-run.
- [ ] **Seeder**: `inc/seeder.php`, exposed as **WP-CLI command** (`wp acme seed`) *and* an admin button (nonce + `manage_options`). Idempotent, with `--reset` flag for dev only.
- [ ] Never run the seeder automatically on production activation.

## Phase 4 — Templates & Interaction
- [ ] `template-parts/` for every repeated component (`card-project.php`, `section-hero.php`) using `get_template_part( $slug, $name, $args )`.
- [ ] Templates: `front-page.php`, `page-{slug}.php`, `single-{cpt}.php`, `archive-{cpt}.php`.
- [ ] Pagination, empty states and `if ( $field )` guards on every field output.
- [ ] **Animation layer**: single `animations.js` with atomic classes (`.gsap-reveal`, `.gsap-counter`).
  - [ ] Respect `prefers-reduced-motion`.
  - [ ] No entrance animation on above-the-fold content (hurts LCP — see SOP 04).
  - [ ] Disable heavy animation on touch/mobile where it adds CPU cost.
- [ ] Images: always `width`/`height`, `loading="lazy"` except LCP image (`fetchpriority="high"`, no lazy).

## Phase 5 — Forms, SEO, Security
- [ ] **Forms**: prefer REST route (`register_rest_route`) over `admin-ajax`; required: nonce, sanitization, server-side validation, honeypot + time-trap, rate limit (transient per IP hash), `wp_mail` via SMTP plugin, store submissions in a private CPT or table.
- [ ] **SEO**: if SEOPress/Yoast/RankMath is installed, let it own meta + schema; add only custom JSON-LD types it can't produce, and output each schema type **once** (no duplicates).
- [ ] **Headers/security**: CSP (report-only first), `X-Content-Type-Options`, `Referrer-Policy`; disable XML-RPC if unused; limit login attempts; disable file editing (`DISALLOW_FILE_EDIT`).
- [ ] **i18n**: wrap strings in `__()` / `esc_html__()` with a text domain even for single-language sites.

## Phase 6 — White-Label & Handover
- [ ] Agency branding on login screen and admin (colors, logo) via `login_enqueue_scripts` + `admin_enqueue_scripts`.
- [ ] Admin sanitization **by role/user**, not globally: apply only when `! acme_is_agency_user()` so developers keep full access. Remove "Howdy" via `admin_bar_menu` filter; hide Screen Options/Help only for the client role.
- [ ] Dashboard locked to custom widgets (status, support contact).
- [ ] Sidebar toggle (Client ↔ Technical view) stored in user meta.
- [ ] Client documentation: 1-page Loom/PDF + `README.md` (required plugins, seeder, deploy steps, who to contact).

## Phase 7 — QA & Launch Gate (new)
- [ ] Lighthouse/PageSpeed (mobile + desktop) — run SOP 04.
- [ ] axe DevTools / WAVE: no critical issues. Keyboard-only walkthrough.
- [ ] Cross-browser: Safari (iOS), Chrome, Firefox. Real-device mobile check.
- [ ] Link checker, 404 test, form test (success, validation error, spam attempt).
- [ ] `WP_DEBUG` on in staging: zero PHP notices/deprecations.
- [ ] Backups verified + rollback plan written. Search-engine visibility setting correct (staging = noindex, production = index).
- [ ] Redirect map (if replacing an existing site) tested.

## Definition of Done
All boxes above ticked · README complete · staging signed off by client · production deploy via the agreed pipeline (no direct edits on server) · post-launch monitoring (uptime + Search Console) enabled.
