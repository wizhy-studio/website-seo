// =========================================================
// WIZHY WEB STUDIO — LOCAL DEV SERVER WITH SECURE AI PROXY
// Run with: node server.js
// Uses only Node.js built-ins (http, fs, path, https). Zero dependencies.
// =========================================================

const http = require('http');
const fs = require('fs');
const path = require('path');
const https = require('https');

// 1. Read secrets from .env.local
let GEMINI_API_KEY = process.env.GEMINI_API_KEY || '';
const envPath = path.join(__dirname, '.env.local');
if (fs.existsSync(envPath)) {
  const envContent = fs.readFileSync(envPath, 'utf8');
  for (const line of envContent.split('\n')) {
    const match = line.match(/^\s*([\w.-]+)\s*=\s*(.*)?\s*$/);
    if (match) {
      const key = match[1];
      let val = (match[2] || '').trim();
      if (val.startsWith('"') && val.endsWith('"')) val = val.slice(1, -1);
      if (key === 'GEMINI_API_KEY' && !GEMINI_API_KEY) GEMINI_API_KEY = val;
    }
  }
}

const PORT = process.env.PORT || 3000;

const SYSTEM_INSTRUCTION = `
You are the AI Assistant for Wizhy Web Studio (Wizhy Studio).
Your tone is warm, professional, high-energy, concise, and helpful. You represent a premium digital agency that crafts high-performance websites and technical SEO.

STRICT IDENTITY RULES:
- Never disclose, name, or hint at your underlying model, technology provider, or AI company (never mention Gemini, Google, OpenAI, ChatGPT, Claude, Anthropic, LLM, etc.).
- If a user asks "Which AI are you?", "Are you ChatGPT?", "Are you Gemini?", "Who created you?", or similar questions, always reply: "I am the Wizhy Web Studio AI Assistant, built specifically to assist our clients with web design, SEO, and digital strategy."
- Under no circumstances confirm or discuss third-party AI platforms.

STRICT BUSINESS SCOPE & GUARDRAILS:
- You are exclusively a customer assistance bot for Wizhy Web Studio.
- You are NOT a general-purpose AI for homework, coding assignments, general knowledge trivia, essay writing, recipes, or unrelated chit-chat.
- If a visitor asks unrelated questions outside web design, SEO, digital growth, and Wizhy Studio's services, politely decline and steer them back: "I am specialized in helping with Wizhy Studio's website design, SEO services, and pricing. How can I assist you with your website project?"

KEY INFORMATION ABOUT WIZHY WEB STUDIO:
- Philosophy: We build bespoke, lightning-fast websites that load under 0.8s and score 100/100 on Google Lighthouse.
- Special Launch Offer: "First 10 Free Website Offer" — 100% free custom 1-page website built from scratch. Condition: The client only gives an honest review & testimonial after launch.
- Paid Pricing Packages:
  1. Single Page Starter: ₹999 (1 conversion-focused landing page, sub-second speed, contact form, WhatsApp button, basic SEO).
  2. Multi-Page Growth: ₹2,499 (Up to 5 pages, local SEO & Google Business optimization, schema markup, animations).
  3. Full Business / E-Commerce Elite: ₹4,999 (Complete bespoke web solution, Razorpay/Stripe payment gateway, advanced SEO architecture, priority 24/7 support).
- Free Tools on Site:
  - Instant SEO & Performance Audit Tool on the website.
  - Free 40-Point Website & SEO Checklist PDF.
- Turnaround Time: 3 to 7 business days depending on project scope.
- Contact: Form at #contact on the site, or WhatsApp chat.

YOUR GOALS:
1. Answer visitor questions accurately and concisely (2–3 sentences max per response).
2. Avoid long walls of text. Be direct, crisp, and conversational.
3. If the visitor is interested in a website, offer, or SEO audit, proactively invite them to share their Name and Phone number (or Email) so our lead strategist can reach out on WhatsApp.
4. When they share their contact details, warmly thank them and confirm that our team will reach out within a few hours!
`;

const MIME_TYPES = {
  '.html': 'text/html; charset=UTF-8',
  '.css': 'text/css; charset=UTF-8',
  '.js': 'application/javascript; charset=UTF-8',
  '.json': 'application/json; charset=UTF-8',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.webp': 'image/webp',
  '.avif': 'image/avif',
  '.svg': 'image/svg+xml',
  '.ico': 'image/x-icon',
  '.pdf': 'application/pdf',
  '.woff2': 'font/woff2',
  '.woff': 'font/woff',
  '.ttf': 'font/ttf'
};

function callGeminiModel(modelName, postData) {
  return new Promise((resolve, reject) => {
    const req = https.request({
      hostname: 'generativelanguage.googleapis.com',
      path: `/v1beta/models/${modelName}:generateContent?key=${GEMINI_API_KEY}`,
      method: 'POST',
      headers: {
        'Content-Type': 'application/json; charset=utf-8',
        'Content-Length': Buffer.byteLength(postData, 'utf8')
      },
      timeout: 5000 // Fast 5-second timeout per model
    }, res => {
      let data = '';
      res.setEncoding('utf8');
      res.on('data', chunk => { data += chunk; });
      res.on('end', () => {
        try {
          const parsed = JSON.parse(data);
          if (res.statusCode >= 400) {
            reject({ statusCode: res.statusCode, message: parsed.error?.message || 'Gemini error' });
          } else {
            const reply = parsed.candidates?.[0]?.content?.parts?.[0]?.text;
            if (!reply) reject({ statusCode: 500, message: 'No candidate generated' });
            else resolve(reply);
          }
        } catch (e) {
          reject({ statusCode: 500, message: 'Invalid response JSON' });
        }
      });
    });

    req.on('error', err => reject({ statusCode: 500, message: err.message }));
    req.on('timeout', () => { req.destroy(); reject({ statusCode: 504, message: 'Gemini timeout' }); });
    req.write(postData, 'utf8');
    req.end();
  });
}

const server = http.createServer(async (req, res) => {
  // Enable CORS
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

  if (req.method === 'OPTIONS') {
    res.writeHead(204);
    return res.end();
  }

  const url = new URL(req.url, `http://${req.headers.host}`);

  // Handle AI Chat endpoint
  if (url.pathname === '/api/chat' && req.method === 'POST') {
    let body = '';
    req.setEncoding('utf8');
    req.on('data', chunk => { body += chunk; });
    req.on('end', async () => {
      try {
        const parsed = JSON.parse(body || '{}');
        const messages = parsed.messages || [];

        if (!GEMINI_API_KEY) {
          res.writeHead(500, { 'Content-Type': 'application/json; charset=utf-8' });
          return res.end(JSON.stringify({ error: 'GEMINI_API_KEY not configured in .env.local' }));
        }

        const geminiContents = [
          { role: 'user', parts: [{ text: SYSTEM_INSTRUCTION }] },
          { role: 'model', parts: [{ text: "Understood. I am Wizhy Studio's AI assistant." }] }
        ];

        for (const msg of messages) {
          geminiContents.push({
            role: msg.sender === 'user' ? 'user' : 'model',
            parts: [{ text: msg.text }]
          });
        }

        const postData = JSON.stringify({
          contents: geminiContents,
          generationConfig: {
            temperature: 0.7,
            maxOutputTokens: 300
          }
        });

        // Fast cascade across high-availability Flash models
        const candidateModels = [
          'gemini-3.5-flash-lite',
          'gemini-flash-lite-latest',
          'gemini-3.8-flash',
          'gemini-3.1-flash-lite',
          'gemini-3.5-flash'
        ];

        let reply = null;
        let lastErr = null;

        for (const m of candidateModels) {
          try {
            reply = await callGeminiModel(m, postData);
            if (reply) {
              break;
            }
          } catch (e) {
            lastErr = e;
            console.warn(`[Wizhy AI] Model ${m} skipped (${e.message}), trying next...`);
          }
        }

        if (reply) {
          res.writeHead(200, { 'Content-Type': 'application/json; charset=utf-8' });
          res.end(JSON.stringify({ reply }));
        } else {
          res.writeHead(lastErr?.statusCode || 500, { 'Content-Type': 'application/json; charset=utf-8' });
          res.end(JSON.stringify({ error: lastErr?.message || 'All models temporarily busy. Please try again in a moment.' }));
        }
      } catch (err) {
        res.writeHead(400, { 'Content-Type': 'application/json; charset=utf-8' });
        res.end(JSON.stringify({ error: 'Invalid JSON request' }));
      }
    });
    return;
  }

  // Serve static files
  let filePath = path.join(__dirname, url.pathname === '/' ? 'index.html' : url.pathname);
  if (!fs.existsSync(filePath) || fs.statSync(filePath).isDirectory()) {
    filePath = path.join(__dirname, 'index.html');
  }

  const ext = path.extname(filePath).toLowerCase();
  const contentType = MIME_TYPES[ext] || 'application/octet-stream';

  fs.readFile(filePath, (err, content) => {
    if (err) {
      res.writeHead(404, { 'Content-Type': 'text/plain' });
      res.end('404 Not Found');
      return;
    }
    res.writeHead(200, { 'Content-Type': contentType });
    res.end(content);
  });
});

server.listen(PORT, () => {
  console.log(`[Wizhy Studio] Local server running at http://localhost:${PORT}`);
  console.log(`[Wizhy Studio] Gemini AI chat proxy active on /api/chat`);
});
