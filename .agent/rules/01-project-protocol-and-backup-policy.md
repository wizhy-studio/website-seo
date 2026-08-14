# ========================================================================
# WIZHY STUDIO — PROJECT PROTOCOL & BACKUP POLICY (v1.0)
# ========================================================================

PROJECT ROOTS & PATHS:
- Parent Workspace: c:\My Files\Websites\1. AG\Wizhy\
- Active Folder: c:\My Files\Websites\1. AG\Wizhy\1. Wizhy-1 (and future Wizhy-2, Wizhy-3...)
- Master Credentials: C:\My Files\Websites\1. AG\Wizhy\0. Don't Touch\.env
- GitHub Repository: https://github.com/wizhy-studio/website-seo.git
- Target Live URL (Phase 2): https://web.wizhy.in

========================================================================
1. PRE & POST PAIRED BACKUP PROTOCOL (MANDATORY)
========================================================================
For EVERY coding task or edit:
1. Determine the highest existing version number `_v[X]` across backups in the project root. Select the next version number `_v[X+1]`.
2. BEFORE making any code edits, create a PRE-change backup:
   `wizhy_studio_pre_[short_description]_v[X+1].zip`
3. Make the specific requested code modifications.
4. Verify the changes completely.
5. AFTER verification passes, create a POST-change backup with the EXACT SAME version number and description:
   `wizhy_studio_post_[short_description]_v[X+1].zip`

PAIRING RULE:
Both Pre and Post backup files MUST share the identical version number (`_v[X+1]`) and description so they clearly form a 1-to-1 matching pair before and after each change.

EXCLUSIONS (STRICT):
Strictly exclude:
- `0. Don't Touch/`
- `Don't Touch/`
- `.git/`
- `node_modules/`
- `Archive/`
- `can be deleted/`
- `can_be_deleted/`
- `*.zip`

========================================================================
2. PHASED DEPLOYMENT POLICY
========================================================================
- PHASE 1 (CURRENT):
  - All development is done directly on the static frontend source files (`index.html`, `css/`, `js/`, `assets/`).
  - NO WordPress theme `.zip` or SSH deployment is performed during Phase 1.
  - Edits are verified locally, backed up with Pre/Post pairs, and committed/pushed to GitHub (`https://github.com/wizhy-studio/website-seo.git`).
  
- PHASE 2 (FREEZE & LIVE WORDPRESS DEPLOYMENT):
  - Only when all changes are explicitly frozen and approved by the user, we package the custom WordPress theme files and deploy via SSH (`89.117.27.244:65002`) to `web.wizhy.in`.

========================================================================
3. STRICT SCOPE CONTROL
========================================================================
- Only modify what is explicitly requested.
- Do NOT modify, refactor, or delete working sections, UI/UX, animations, or theme toggles unless explicitly instructed.
- All previous features (light/dark mode, SEO audit tool, lead magnet, voice input, WhatsApp button, FAQ accordion) must remain 100% functional.
