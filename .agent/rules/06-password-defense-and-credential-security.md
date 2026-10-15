# ========================================================================
# 06: CREDENTIAL SECURITY & SECRET LEAK DEFENSE PROTOCOL (v1.0)
# ========================================================================
# Also known as: Password Defense & Zero-Leak Credential Policy
# Scope: Mandatory across all Antigravity (AG) chat sessions, subagents,
#        branches, themes, templates, scripts, and workflows.
# ========================================================================

---

## 1. Core Rule & Zero-Tolerance Policy

**NEVER hardcode any secret, token, key, password, or authorization header into any repository file.**

This includes, but is not limited to:
- **Telegram Bot Tokens** (e.g. `123456789:ABCdefGhI...`)
- **Cloudflare Turnstile Secret Keys** (e.g. `0x4AAAAAA...`)
- **Supabase Anon / Service Role Keys** (e.g. `sb_publishable_...`, `eyJhbGci...`)
- **HTTP Authorization Bearer Headers** (e.g. `'Authorization' => 'Bearer ...'`)
- **Google Cloud / Maps API Keys** (e.g. `AIzaSy...`)
- **Stripe Secret / Restricted Keys** (e.g. `sk_live_...`, `rk_live_...`)
- **Private Cryptographic Keys & Certificates** (`BEGIN ... PRIVATE KEY`)
- **Database / FTP / Hostinger Passwords & Credentials**

Any commit containing a literal, raw credential is a critical security violation.

---

## 2. Credential Storage Architecture (Where Secrets Must Live)

Secrets must NEVER be committed to Git. Instead, use strict environment isolation:

### A. Local Development Environment
- Store local development secrets strictly in:
  - `.env.local` (in the project root, strictly gitignored)
  - `C:\My Files\Websites\1. AG\Wizhy\0. Don't Touch\.env` (master reference, strictly gitignored)
- Never stage or commit any `.env*` file except for example templates (`.env.example` with dummy values).

### B. Live Production WordPress (Hostinger)
- Credentials on Hostinger must reside **outside the Git repository theme folder** in `wp-config.php`:
  ```php
  // Defined in wp-config.php (outside Git tracking):
  define('WIZHY_TG_BOT_TOKEN', 'YOUR_ACTUAL_BOT_TOKEN');
  define('WIZHY_CF_TURNSTILE_SECRET', 'YOUR_ACTUAL_TURNSTILE_SECRET');
  define('WIZHY_SUPABASE_URL', 'https://your-project.supabase.co');
  define('WIZHY_SUPABASE_ANON_KEY', 'YOUR_ACTUAL_ANON_KEY');
  define('WIZHY_BREVO_API_KEY', 'YOUR_ACTUAL_BREVO_KEY');
  ```
- Alternatively, store non-critical operational keys in the WordPress database options table via `update_option('wizhy_*', ...)` and retrieve them via `get_option()`.

### C. GitHub Actions & CI/CD Pipelines
- Store CI/CD credentials exclusively in:
  - **GitHub Repo** → **Settings** → **Secrets and variables** → **Actions**
- Reference them in workflows as `${{ secrets.SECRET_NAME }}`.
- NEVER supply real secret strings as default fallbacks in workflow files.

---

## 3. Safe Coding Standards in Theme & Backend Files

When writing PHP in `wizhy-ag-theme/functions.php` or any other theme file:

### DO (Safe Dynamic Resolution):
```php
// Retrieve secret safely with fallback to WordPress database option
$supabaseUrl = defined('WIZHY_SUPABASE_URL') ? WIZHY_SUPABASE_URL : get_option('wizhy_supabase_url', '');
$anonKey     = defined('WIZHY_SUPABASE_ANON_KEY') ? WIZHY_SUPABASE_ANON_KEY : get_option('wizhy_supabase_anon_key', '');

// Construct Bearer headers dynamically
if (!empty($supabaseUrl) && !empty($anonKey)) {
    $response = wp_remote_get(rtrim($supabaseUrl, '/') . '/rest/v1/endpoint', array(
        'timeout' => 5,
        'headers' => array(
            'apikey'        => $anonKey,
            'Authorization' => 'Bearer ' . $anonKey,
        )
    ));
}
```

### DON'T (Dangerous - Triggers GitGuardian & Pre-Commit Shield):
```php
// ❌ CRITICAL VIOLATION: Hardcoding raw secrets in theme files
// Example of forbidden code: 'Authorization' => 'Bearer ' . 'ACTUAL_TOKEN_STRING'
// Example of forbidden code: $bot_token = 'ACTUAL_TELEGRAM_BOT_TOKEN';
```

---

## 4. Mandatory Pre-Commit & Verification Workflow

Before running `git commit` or pushing any changes to GitHub:

1. **Verify GitGuardian-Style Scanner Passes**:
   Run the full repo scanner:
   ```bash
   node scan_secrets.js
   # or
   npm run audit-secrets
   ```
   The scanner must exit with `0` violations found across all files.

2. **Ensure Git Pre-Commit Hook is Intact**:
   - The file `.git/hooks/pre-commit` must be present and executable.
   - It automatically halts any commit that introduces matched credential patterns.

3. **Verify Git Staging Area**:
   - Run `git status` and `git diff --cached` to inspect staged lines.
   - Confirm no `.env`, `.pem`, `.key`, or backup `.zip` files are staged.

---

## 5. Emergency Incident Response Protocol

If an alert is received from GitGuardian, Hostinger, or GitHub Secret Scanning:
1. **Rotate/Revoke Immediately**:
   - Invalidate the compromised token in the service provider console (e.g. Supabase, Telegram BotFather, Cloudflare dashboard).
   - Generate a new replacement secret and update `wp-config.php` and GitHub Secrets.
2. **Sanitize Repository Code**:
   - Replace any exposed literal strings with dynamic constants (`defined('...') ? ... : ...`).
3. **Audit History & Clean**:
   - Run `node scan_secrets.js` to confirm zero remaining exposures.
   - Push the fix to `origin/main`.
4. **Resolve Alert**:
   - In the GitGuardian alert dashboard, mark the incident as **"Resolved — Secret revoked & removed from repository"**.
