# Admin Skin

Clean, professional wp-admin restyle with light/dark mode, a branded login screen and optional white-labelling for client users.

- Prefix: `dic_` / `DIC_` (functions, constants, option, user meta, handles, hooks, AJAX action)
- Requires: WordPress 6.9+, PHP 8.2+
- Settings: **Settings → Admin Skin** (`manage_options` only)

## Install

Copy the `dic-admin-skin/` folder to `wp-content/plugins/` (or zip it) and activate. Test on staging first.

## What it does

| Area | Behaviour |
|------|-----------|
| Skin | One stylesheet driven by CSS custom properties. Brand colour set in settings. |
| Dark mode | Per-user (`dic_admin_skin_mode` user meta): Auto / Light / Dark, toggled from the admin bar. A tiny inline script in `<head>` sets the theme before CSS paints, so there is no flash. |
| Login | Logo, background colour, card layout, follows system dark mode. Logo links to the home page. |
| Dashboard | Optional: removes the default widgets and registers one Site status widget. |
| White-label | Optional: hides WP logo, footer credit and update nags for non-agency users. |

## Decisions

- **Variables + stable selectors only.** WordPress 7.0 redesigned wp-admin (SOP 02). Overriding a small set of selectors and `--wp-admin-theme-color` keeps breakage low. Re-test after every major WP upgrade.
- **Block editor stays light.** Dark mode is deliberately not applied on block-editor screens; the editor ships its own styles.
- **Agency detection by email domain.** Same pattern as SOP 02's `agency_user()` check. If no domain is configured, everyone counts as agency, so white-label does nothing by default. Override with the `dic_admin_skin_is_agency_user` filter.
- **Hide, never block.** White-label removes visual clutter only; no capabilities are changed.
- **Logo via attachment ID**, validated with `wp_attachment_is_image()`. Uploading raw SVG is a core restriction; use a PNG/WebP logo.
- **Inline CSS** only carries sanitized hex colours (`sanitize_hex_color`) and an escaped logo URL.

## Security

- Settings: Settings API (`options.php` enforces nonce + capability), every field sanitized in `dic_admin_skin_sanitize_options()`.
- Mode toggle AJAX: `check_ajax_referer`, `read` capability, whitelist of `auto|light|dark`.
- All output escaped (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).

## QA checklist

- [ ] Fresh install → activate → admin renders, no PHP notices (`WP_DEBUG` on)
- [ ] Light, dark and auto modes persist after reload, no flash
- [ ] Tested as agency user and as client user (white-label on)
- [ ] Login page in light and dark, logo and link correct
- [ ] Contrast ≥ 4.5:1 for text, ≥ 3:1 for UI in both modes
- [ ] Plugin Check passes
- [ ] Tested alongside Bricks, FlyingPress, Perfmatters, SCF screens

## Changelog

### 1.0.0
- Initial release.
