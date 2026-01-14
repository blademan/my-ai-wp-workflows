---
description: An AI-optimized blueprint for building clean, data-driven WordPress themes with global dark mode and a minimal admin interface.
---

# AI WordPress Blueprint: The Modern App Approach

This workflow refines the standard conversion process into a specialized "App-Like" theme builds, prioritizing clean admin interfaces, dark mode, and automated data seeding.

## Phase 1: Intelligent Foundation & Seeding
Establish a zero-config setup experience.
- [ ] **Auto-Page Creation**: Hook into `after_switch_theme` to create essential pages (Home, Thank You) automatically.
- [ ] **Data Seeding**: Create a `seeder.php` to populate CPTs with dummy content (Changelogs, Roadmaps) on activation.
- [ ] **No-Editor Policy**: Disable the Gutenberg Block Editor globally to force structured data usage via Custom Fields.

## Phase 2: The "Clean Admin" Protocol
Aggressively simplify the WordPress dashboard for the end-user.
- [ ] **Menu Sanitization**: Remove standard "Posts", "Comments", and "Media" from the sidebar.
- [ ] **Admin Bar Detox**: Remove "New Post", "Comments", and "WP Logo" from the top bar.
- [ ] **Dashboard Purge**: Unregister default widgets (News, Quick Draft) and replace with a single "Status Widget".
- [ ] **Performance Hygiene**: Disable Emojis, WP Version, and unneeded `<head>` tags in `functions.php`.

## Phase 3: Global Dark Mode Architecture
Implement a resilient, toggle-based dark mode system.
- [ ] **Tailwind Config**: Enable `darkMode: 'class'` in `tailwind.config.js` (or inline script).
- [ ] **Persistence Layer**: Use `localStorage` in `main.js` to save user preference (Light/Dark).
- [ ] **Global Application**: Ensure `dark:` classes are applied to:
    - [ ] **Body/HTML**: `bg-slate-50` → `dark:bg-slate-900`.
    - [ ] **Typography**: `text-slate-900` → `dark:text-white`.
    - [ ] **Components**: Cards, Inputs, and Borders.
- [ ] **Sticky Toggle**: Place a fixed-position toggle (Sun/Moon) in the `header.php`.

## Phase 4: Dynamic Data Integration
Build robust templates using Secure Custom Fields (SCF).
- [ ] **PHP Field Registration**: Register all SCF groups via PHP (`inc/acf-fields.php`) to keep version control clean.
- [ ] **Template Logic**: Use `get_field()` directly in templates (`front-page.php`, `archive.php`).
- [ ] **Fallback Handling**: Always check `if ($field)` before outputting to prevent empty blocks.
- [ ] **SVG Usage**: Use inline SVGs for icons instead of image assets for better color control (`currentColor`).

## Phase 5: Packaging & Handover
ensure the theme is portable and "Client-Ready".
- [ ] **Dependency Note**: Explicitly list "Secure Custom Fields" as a required plugin in `README.md`.
- [ ] **Redirect Strategy**: Implement a global 404 redirect to Home to avoid dead ends.
- [ ] **Final Polish**: Verify `readme.txt` mentions the Seeder workflow for new installs.
