// ==============================================================================
// WIZHY WEB STUDIO — FULL REPOSITORY SECRET SCANNER (GitGuardian Emulation)
// Run with: node scan_secrets.js
// Scans all files across the repo for leaked secrets, API keys, and Bearer tokens.
// ==============================================================================

const fs = require('fs');
const path = require('path');

const IGNORED_DIRS = new Set([
  '.git',
  'node_modules',
  '.agent',
  'temp_files',
  'irrelevant',
  'Archive',
  'Archive_zip'
]);

const IGNORED_FILES = new Set([
  '.env.local',
  'scan_secrets.js',
  'package-lock.json'
]);

const SECRET_RULES = [
  { name: 'Hardcoded Bearer Token', regex: /Bearer\s+['"][a-zA-Z0-9_\-\.]{15,}['"]/i },
  { name: 'Telegram Bot Token', regex: /[0-9]{8,12}:[a-zA-Z0-9_-]{35}/ },
  { name: 'Cloudflare Turnstile Secret Key', regex: /0x4[a-zA-Z0-9_-]{28,}/ },
  { name: 'Supabase Anon Key in Backend/PHP', regex: /sb_publishable_[a-zA-Z0-9_-]{25,}/ },
  { name: 'Google API Key', regex: /AIzaSy[a-zA-Z0-9_-]{33}/ },
  { name: 'Stripe Secret Key', regex: /sk_live_[a-zA-Z0-9]{24,}/ },
  { name: 'Private Key Block', regex: /-----BEGIN[ A-Z0-9_-]*PRIVATE KEY-----/ },
  { name: 'Hardcoded Password in String', regex: /(?:password|secret|passwd)\s*[:=]\s*['"][^'"]{8,}['"]/i }
];

let filesScanned = 0;
let violationsFound = 0;

function walk(dir) {
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      if (!IGNORED_DIRS.has(entry.name)) {
        walk(fullPath);
      }
    } else if (entry.isFile()) {
      if (!IGNORED_FILES.has(entry.name) && !entry.name.endsWith('.min.js') && !entry.name.endsWith('.min.css')) {
        scanFile(fullPath);
      }
    }
  }
}

function scanFile(filePath) {
  filesScanned++;
  const relativePath = path.relative(process.cwd(), filePath);
  let content;
  try {
    content = fs.readFileSync(filePath, 'utf8');
  } catch (err) {
    return; // binary file
  }

  const lines = content.split('\n');
  lines.forEach((line, idx) => {
    for (const rule of SECRET_RULES) {
      // In frontend script.js, public client anon keys are normal if not Bearer headers,
      // but in PHP / backend files, any hardcoded anon key is a violation.
      if (rule.name === 'Supabase Anon Key in Backend/PHP') {
        if (!filePath.endsWith('.php') && !filePath.endsWith('.yml')) continue;
      }
      if (rule.regex.test(line)) {
        console.error(`🚨 [${rule.name}] in ${relativePath}:${idx + 1}`);
        console.error(`   > ${line.trim()}`);
        violationsFound++;
      }
    }
  });
}

console.log('🔍 Starting Deep GitGuardian-Style Secret Audit...');
walk(process.cwd());
console.log(`\n📊 Audit Complete: ${filesScanned} files scanned.`);

if (violationsFound > 0) {
  console.error(`❌ FAILED: ${violationsFound} potential secret exposure(s) detected!`);
  process.exit(1);
} else {
  console.log('✅ PASSED: Zero exposed secrets, Bearer tokens, or credentials found in codebase.');
  process.exit(0);
}
