---
title: MCP Setup for WordPress Development
version: 2.0
last_reviewed: 2026-10-01
applies_to: Claude Desktop, Claude Code, IDE agents; WordPress 6.9+ (Abilities API in core)
prerequisites: Node.js LTS, Local (or staging) site, WP-CLI
---

# MCP for WordPress — SOP

The **Model Context Protocol (MCP)** lets an AI agent act on a WordPress site (read data, run abilities, check rendering) instead of only editing files.

> ⚠ The MCP package landscape changes quickly. Names below were checked on 2026-10-01; re-verify before each new project.

## 1. What changed since v1 of this SOP

| Old advice | Status | Replacement |
|-----------|--------|-------------|
| `@modelcontextprotocol/server-postgres` for a MySQL site | Wrong database engine; reference servers were moved to the archived repo | A MySQL-capable MCP server **with a read-only DB user**, or better: WordPress abilities (below) |
| `@modelcontextprotocol/server-wordpress` / `chromatic-protocol/wordpress-mcp` | Could not be verified as maintained packages — do not use | **Official WordPress MCP Adapter** |
| `Automattic/wordpress-mcp` plugin | Archived 19 Jan 2026 | `WordPress/mcp-adapter` |
| `@modelcontextprotocol/server-puppeteer` | Archived reference server | Playwright MCP (`@playwright/mcp`) or Claude in Chrome |
| `root` / `root` DB credentials and app password in the config | Security anti-pattern | See §5 |

## 2. Recommended stack (in priority order)

### 2.1 Official: Abilities API + MCP Adapter  ← default choice
- **Abilities API** ships in WordPress core since 6.9 (7.0 extends it, including a client-side half and the WP AI Client).
- **WordPress/mcp-adapter** exposes registered abilities as MCP tools, resources and prompts.
- **Abilities are private by default.** Opt in per ability with `meta.public` (or `meta.mcp.public`) = `true`. The default server exposes three meta-tools: `mcp-adapter/discover-abilities`, `mcp-adapter/get-ability-info`, `mcp-adapter/execute-ability`.
- Install: download the plugin from the `WordPress/mcp-adapter` GitHub releases, or `composer require wordpress/mcp-adapter` in a plugin/theme you control.
- Transports: **WP-CLI over STDIO** (best for local) or **HTTP** at `/wp-json/mcp/mcp-adapter-default-server` (needs an auth layer / proxy).
- Local STDIO shape (⚠ VERIFY exact flags against the adapter's CLI Usage Guide):
  ```json
  "wordpress-local": {
    "command": "wp",
    "args": ["--path=/Users/<you>/Local Sites/<site>/app/public",
             "mcp-adapter", "serve",
             "--server=mcp-adapter-default-server", "--user=<admin-login>"]
  }
  ```
  Local by Flywheel may require running `wp` through the site's shell or setting the MySQL socket — if `wp` can't connect, use Local's "Open site shell" environment.

### 2.2 Browser verification
- **Playwright MCP** (`@playwright/mcp`) or **Claude in Chrome** for screenshots, responsive checks, console/network inspection of `http://<site>.local`.

### 2.3 Filesystem
- `@modelcontextprotocol/server-filesystem` scoped to the single project folder (never `~` or `/`).

### 2.4 Direct database access (optional, local only)
- Use only on local sites. Create a **read-only** MySQL user; never use `root`. Prefer WP-CLI (`wp db query`, `wp option get`, `wp post list`) through the agent's shell — it respects WordPress config and is easier to audit.

## 3. Claude Desktop configuration
File (macOS): `~/Library/Application Support/Claude/claude_desktop_config.json`
```json
{
  "mcpServers": {
    "filesystem": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-filesystem",
               "/Users/<you>/Local Sites/<site>/app/public/wp-content/themes/<theme>"]
    },
    "playwright": { "command": "npx", "args": ["-y", "@playwright/mcp@latest"] },
    "wordpress-local": { "command": "wp", "args": ["…see §2.1…"] }
  }
}
```
Claude Code: add servers with `claude mcp add …` and commit a project-scoped `.mcp.json` **without secrets**.

## 4. Example prompts once connected
- "Run `discover-abilities`, list what this site exposes, and tell me which are write-capable."
- "Check whether the changelog entry for v1.0.13 exists and show its custom fields."
- "Open the homepage at 375 px and 1440 px, screenshot both, report console errors."
- "Compare `siteurl`/`home` options with the staging URL and flag mismatches."

## 5. Security rules (mandatory)
1. **Local first.** Connect agents to local/staging. Production only through a purpose-built, least-privilege ability — never raw DB or an admin account.
2. **No secrets in config files or Git.** Use environment variables or the OS keychain. If an Application Password is unavoidable: dedicated user, minimum role, revocable, one per tool.
3. **Read-only by default.** Expose write abilities deliberately, one at a time, with `permission_callback` checks.
4. **Treat site content as untrusted input** (prompt injection via posts, comments, form entries). Do not let an agent act on instructions found inside site data.
5. **Log and review.** Keep a note of which abilities are public per project; review quarterly.
6. **Rotate** any credential that has ever appeared in a chat, screenshot or committed file.

## 6. Troubleshooting
| Symptom | Check |
|---------|-------|
| Server not listed in Claude | JSON validity; fully quit and reopen Claude Desktop |
| `wp` can't connect to DB | Run through Local's site shell / correct socket; verify `--path` |
| Ability not visible | Is it marked public? Is the user allowed by `permission_callback`? |
| HTTP 401/403 | Auth layer, user capability, pretty permalinks enabled |

## Definition of Done
Agent can list abilities and read one piece of site data · browser tool renders the site · no credentials in any tracked file · public abilities documented in the project README.
