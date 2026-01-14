---
description: A strategic 6-phase workflow for converting high-fidelity HTML into enterprise WordPress themes.
---

# HTML to WordPress Conversion Workflow: The Logic of Build

This workflow organizes the conversion process into 6 logical phases, moving from architecture to enterprise-grade hardening.

## Phase 0: Brand & Asset Sanitization
Pre-flight check of the static source.
- [ ] **Token Extraction**: Identify primary/secondary colors, typography stacks, and spacing units.
- [ ] **Asset Optimization**: Convert icons to SVGs, compress images, and identify brand fonts.
- [ ] **Naming Convention**: Align static class names (e.g., `.btn-primary`) with Tailwind or custom CSS tokens.

## Phase 1: Architecture & System Core
Organize the source and establish the WordPress environment.
- [ ] **Asset Reorganization**: Flatten the directory and isolate the `screenshot.png`.
- [ ] **Theme Foundation**: Create `style.css` (metadata) and `functions.php`.
- [ ] **System Services**:
    - [ ] **Enqueues**: Setup Google Fonts, Tailwind CDN/Build, and GSAP Suite.
    - [ ] **Main Page Provisioning**: Implement `acme_create_initial_pages` to auto-create and link essential nodes.
    - [ ] **Status Dashboard**: Add a custom widget for system health and seeder access.

## Phase 2: Structural Skeleton
Establish the global templates and navigation logic.
- [ ] **Header & Footer**: Construct `header.php` and `footer.php` with `wp_head()` and `wp_footer()`.
- [ ] **The Fallback**: Create a robust `index.php` for hierarchy compliance.
- [ ] **Navigation Audit**: Replace static links with `home_url()` and verify anchor IDs.

## Phase 3: Data Engine & Media Ingestion
Transform static sections into dynamic content.
- [ ] **Custom Post Types**: Register CPTs (e.g., `project`) in `functions.php`.
- [ ] **Media Sideloading**: Move static images into the WordPress Media Library.
- [ ] **Seeding**: Implement a one-click seed function for initial content.

## Phase 4: Visual Templates & Interaction
Map the designs to PHP and add the premium interactive layer.
- [ ] **Componentization**: Break down recurring UI into `template-parts/` (e.g., `card-project.php`).
- [ ] **Template Mapping**: Build `front-page.php`, `page-{slug}.php`, and `single-{post_type}.php`.
- [ ] **Global Animation Suite**: Use `assets/js/animations.js` with atomic classes (`.gsap-reveal`, `.gsap-counter`).

## Phase 5: Hardening & Security
Finalize functionality and prepare for deployment.
- [ ] **Form Handling**: Secure contact forms using nonces, AJAX, and backend data ingestion into custom storage.
- [ ] **SEO & Schema**: Register JSON-LD structured data for the organization and projects.
- [ ] **Industrial UX**: Implement smooth scrolling and grayscale-to-color transitions.

## Phase 6: White Labeling & Handover
Sanitize the administrative experience for the client.
- [ ] **Agency Branding**: Apply agency aesthetics (primary colors) and typography to the WordPress admin area.
- [ ] **Admin Sanitization**: Purge "Howdy", hide Screen Options/Help tabs, and lock the dashboard to specific custom widgets.
- [ ] **Support Integration**: Add a dedicated agency support widget for direct communication.
- [ ] **Advanced Toggle**: Implement a Sidebar toggle mechanism to switch between Client and Technical views.
