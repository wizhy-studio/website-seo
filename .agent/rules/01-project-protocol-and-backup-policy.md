# ========================================================================
# WIZHY STUDIO — PROJECT PROTOCOL & BACKUP POLICY (v2.0)
# ========================================================================

PROJECT ROOTS & PATHS:
- Parent Workspace: c:\My Files\Websites\1. AG\Wizhy\
- Active Folder: c:\My Files\Websites\1. AG\Wizhy\1. Wizhy-1 (and future Wizhy-2, Wizhy-3...)
- Local Backup Archive Directory: c:\My Files\Websites\1. AG\Wizhy\1. Wizhy-1\Archive\
- Local Temporary Captures / Screenshots Directory: c:\My Files\Websites\1. AG\Wizhy\1. Wizhy-1\temp_files\
- Master Credentials: C:\My Files\Websites\1. AG\Wizhy\0. Don't Touch\.env
- GitHub Repository: https://github.com/wizhy-studio/website-seo.git
- Target Live URL (Phase 2): https://web.wizhy.in

========================================================================
1. PRE & POST PAIRED BACKUP PROTOCOL (MANDATORY)
========================================================================
For EVERY coding task or edit:
1. Determine the highest existing version number `_v[X]` across backups in the `Archive/` directory. Select the next version number `_v[X+1]`.
2. BEFORE making any code edits, create a PRE-change backup saved directly into `Archive/`:
   `Archive/wizhy_studio_pre_[short_description]_v[X+1].zip`
3. Make the specific requested code modifications.
4. Verify the changes completely.
5. AFTER verification passes, create a POST-change backup with the EXACT SAME version number and description saved directly into `Archive/`:
   `Archive/wizhy_studio_post_[short_description]_v[X+1].zip`

PAIRING RULE:
Both Pre and Post backup files MUST share the identical version number (`_v[X+1]`) and description so they clearly form a 1-to-1 matching pair before and after each change.

STORAGE LOCATION RULE:
- ALL `.zip` backup files (past, present, and future) MUST be stored strictly in `C:\My Files\Websites\1. AG\Wizhy\1. Wizhy-1\Archive\`.
- Never leave `.zip` files in the project root.

EXCLUSIONS WHEN CREATING BACKUPS (STRICT):
When creating a backup `.zip`, strictly exclude:
- `Archive/` and `Archive_zip/` (since backups are already archived and must not be recursively backed up)
- `temp_files/` (temporary screenshots and visual QA captures)
- `0. Don't Touch/` and `Don't Touch/`
- `.git/`
- `node_modules/`
- `can be deleted/` and `can_be_deleted/`
- `wizhy-studio-v4_1-package/`
- `*.zip`

========================================================================
2. TEMPORARY FILES & SCREENSHOTS STORAGE RULE
========================================================================
- ALL QA verification screenshots, visual audit images, and temporary files (past, present, and future) MUST be saved in:
  `C:\My Files\Websites\1. AG\Wizhy\1. Wizhy-1\temp_files\`
- Never save temporary screenshots in the project root.

========================================================================
3. GITHUB REPOSITORY & GIT TRACKING RULES
========================================================================
- NEVER upload or commit the following directories or files to GitHub:
  - `Archive/` (and all `.zip` files)
  - `temp_files/` (and all temporary screenshot/QA images)
  - `0. Don't Touch/` (and all `.env` secrets)
  - Temporary automation scripts (`create_backup.ps1`, `headless_qa.js`, `qa_*.js`, etc.)
  - Temporary summary bundles (`wizhy-studio-*.md`)
- Keep only clean production website files in GitHub (`index.html`, policy pages, `css/`, `js/`, `assets/`, `robots.txt`, `sitemap.xml`, `README.md`, `.gitignore`, `.agent/rules/`, `.github/workflows/`).
- All local modification and backup files remain 100% intact on the Windows local drive.

========================================================================
4. PHASED DEPLOYMENT POLICY
========================================================================
- PHASE 1 (CURRENT):
  - All development is done directly on the static frontend source files (`index.html`, `css/`, `js/`, `assets/`).
  - NO WordPress theme `.zip` or SSH deployment is performed during Phase 1.
  - Edits are verified locally, backed up with Pre/Post pairs in `Archive/`, and committed/pushed to GitHub (`https://github.com/wizhy-studio/website-seo.git`).
  
- PHASE 2 (FREEZE & LIVE WORDPRESS DEPLOYMENT):
  - Only when all changes are explicitly frozen and approved by the user, we package the custom WordPress theme files and deploy via SSH (`89.117.27.244:65002`) to `web.wizhy.in`.

========================================================================
5. STRICT SCOPE CONTROL
========================================================================
- Only modify what is explicitly requested.
- Do NOT modify, refactor, or delete working sections, UI/UX, animations, or theme toggles unless explicitly instructed.
- All previous features (light/dark mode, SEO audit tool, lead magnet, voice input, WhatsApp button, FAQ accordion, legal policies) must remain 100% functional.
