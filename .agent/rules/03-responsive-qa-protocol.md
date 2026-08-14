# ========================================================================
# WIZHY STUDIO — RESPONSIVE QA & VERIFICATION PROTOCOL
# ========================================================================

CORE PRINCIPLE:
- Unverified ≠ Pass
- Screenshot Captured ≠ Screenshot Verified
- Page Loaded ≠ Page Rendered Correctly

TEST MATRIX:
Test across extracted media-query breakpoints PLUS standard viewport widths:
- Mobile: 320px, 375px, 390px, 414px
- Tablet: 768px, 820px, 1024px
- Desktop: 1280px, 1440px, 1920px
For every width, test in BOTH Light Mode and Dark Mode.

REQUIRED CHECKS:
1. Visual: Zero horizontal overflow, no clipped text, no overlapping elements, clean grid alignment.
2. Functional: Theme toggle works, FAQ accordion operates, voice input & audit tool respond, buttons/links clickable.
3. Console & Network: Zero JavaScript console errors, zero 404 network asset failures.
4. Full-Page Scroll: Inspect entire scroll depth from header to footer, not just above-the-fold.
5. Independent Auditor Verification (Agent B): Verify evidence before declaring any task complete.
