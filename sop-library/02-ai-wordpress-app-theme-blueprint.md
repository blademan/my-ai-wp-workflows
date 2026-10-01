---
title: AI WordPress Blueprint — App-Like Theme
version: 2.0
last_reviewed: 2026-10-01
applies_to: WordPress 6.9+ / 7.x, PHP 8.2+, Secure Custom Fields, Tailwind 3 or 4
prerequisites: SOP 01 foundation completed (or being followed in parallel)
---

# App-Like Theme Blueprint

For data-driven sites (changelogs, roadmaps, dashboards, directories) with a single focused editor experience, global dark mode and an almost empty admin.

## Phase 1 — Zero-Config Foundation
- [ ] **Auto-pages** on `after_switch_theme` (Home, Thank You, Privacy). Idempotent — look up by slug first.
- [ ] **Seeder** in `inc/seeder.php`: WP-CLI command + admin button; marks itself done via an option; safe to re-run.
- [ ] **No-Editor policy** (structured data only):
  ```php
  add_filter( 'use_block_editor_for_post_type', '__return_false' );          // classic UI for all types
  add_action( 'init', function () {
      foreach ( [ 'page', 'roadmap_item', 'changelog' ] as $pt ) {
          remove_post_type_support( $pt, 'editor' );                           // hide content box entirely
      }
  } );
  ```
  Note: removing `editor` support also hides the content field in REST for those types — fine for SCF-only content, but confirm no plugin depends on it.

## Phase 2 — Clean Admin Protocol (role-aware)
**Rule:** simplify the UI for clients, never for the agency. Gate everything with:
```php
function acme_is_agency_user(): bool {
    $u = wp_get_current_user();
    return $u && str_ends_with( $u->user_email, '@your-agency.com' ); // or a constant list of user IDs
}
```
- [ ] **Menu sanitization**: `remove_menu_page( 'edit.php' )` (Posts), `edit-comments.php`; hide Media only if no field needs it — SCF image fields still use the media modal, so *hiding the menu item is safe, blocking the capability is not*.
- [ ] **Admin bar detox**: remove `wp-logo`, `new-content`, `comments` nodes on `admin_bar_menu` (priority 999).
- [ ] **Dashboard purge**: `remove_meta_box()` for all default widgets on `wp_dashboard_setup`; register one Status widget.
- [ ] **Performance hygiene**: disable emojis, `wp_generator`, RSD, WLW, shortlink, REST/oEmbed discovery links as needed. Keep REST API open for logged-in users (blocks and plugins depend on it).
- [ ] **WordPress 7.0 note**: the admin was visually redesigned — re-test every admin CSS override after upgrades.

## Phase 3 — Global Dark Mode
- [ ] **Tailwind config**
  - v3: `darkMode: 'class'` in `tailwind.config.js`.
  - v4: in CSS → `@custom-variant dark (&:where(.dark, .dark *));`
- [ ] **No-flash script** — print in `<head>` *before* any CSS (hook `wp_head` priority 1):
  ```html
  <script>
  (function(){try{var t=localStorage.getItem('theme');
  var d=t?t==='dark':matchMedia('(prefers-color-scheme: dark)').matches;
  document.documentElement.classList.toggle('dark',d);}catch(e){}})();
  </script>
  ```
  This respects system preference by default and avoids the white flash.
- [ ] **Toggle** in `main.js`: update class + `localStorage` (wrapped in try/catch) + `aria-pressed` + `<meta name="color-scheme">`.
- [ ] **Apply `dark:` variants** to body, typography, cards, inputs, borders, focus rings, code blocks, SVG fills.
- [ ] **Sticky toggle** (Sun/Moon) in `header.php`: real `<button>` with accessible label, ≥ 44×44 px target.
- [ ] Contrast check in both modes (≥ 4.5:1 text, ≥ 3:1 UI).

## Phase 4 — Dynamic Data (Secure Custom Fields)
- [ ] **Register groups in PHP** on `acf/init` with `acf_add_local_field_group()` (file: `inc/scf-fields.php`; keep the filename neutral, not `acf-`). Alternative: Local JSON in `/acf-json` — pick one per project and document it.
- [ ] **Guard missing plugin**: `if ( ! function_exists( 'get_field' ) ) { add_action( 'admin_notices', … ); return; }`
- [ ] **Template logic**: `get_field()` / `the_field()` in `front-page.php`, `archive-*.php`, `single-*.php`; always `if ( $field )` before markup.
- [ ] **Escape everything**: `esc_html`, `esc_url`, `wp_kses_post` for WYSIWYG.
- [ ] **Icons**: inline SVG with `currentColor`; `aria-hidden="true"` when decorative.
- [ ] Query performance: `no_found_rows`, `posts_per_page` limits, avoid N+1 `get_field()` loops in archives (prime meta cache with `update_post_meta_cache`).

## Phase 5 — Packaging & Handover
- [ ] **README.md**: required plugin (Secure Custom Fields), PHP/WP minimums, seeder usage, build commands.
- [ ] **404 handling**: serve a proper `404.php` with HTTP 404 and links home. **Do not redirect every 404 to Home** — Google treats it as a soft-404, it hides broken links and hurts crawl quality. Use targeted 301s for real moved URLs.
- [ ] `readme.txt` mentions the seeder workflow for fresh installs.
- [ ] Version bump + changelog entry + tagged release.

## Phase 6 — QA (new)
- [ ] Test as client role *and* agency role.
- [ ] Test fresh install → activate → seed → front page renders with zero config.
- [ ] `WP_DEBUG` clean; Plugin Check passes; axe passes; PageSpeed (SOP 04).

## Definition of Done
Fresh install works in < 5 minutes · client admin shows only intended menus · dark mode persists without flash · no 404 redirects · README complete.
