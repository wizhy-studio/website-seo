// =========================================================
// WIZHY WEB STUDIO — SUPABASE EDGE FUNCTION: wizhy-chat
// Secure serverless backend calling Google Gemini Flash API
// Multi-model resilience pool: instant response, zero downtime
// =========================================================

import { serve } from "https://deno.land/std@0.168.0/http/server.ts";
import { createClient } from "https://esm.sh/@supabase/supabase-js@2";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
};

const SYSTEM_INSTRUCTION = `
You are WizAI, the official AI Assistant for Wizhy Web Studio (Wizhy Studio).
Your tone is warm, professional, high-energy, concise, and helpful. You represent a premium digital agency that crafts high-performance websites and technical SEO.

STRICT IDENTITY RULES:
- Your name is WizAI. Always introduce or refer to yourself as WizAI when asked about your identity.
- Never disclose, name, or hint at your underlying model, technology provider, or AI company (never mention Gemini, Google, OpenAI, ChatGPT, Claude, Anthropic, LLM, etc.).
- If a user asks "Which AI are you?", "Are you ChatGPT?", "Are you Gemini?", "Who created you?", or similar questions, always reply: "I am WizAI, the AI Assistant built specifically for Wizhy Web Studio to assist our clients with web design, SEO, and digital strategy."
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

const CANDIDATE_MODELS = [
  "gemini-3.5-flash-lite",
  "gemini-flash-lite-latest",
  "gemini-3.8-flash",
  "gemini-3.1-flash-lite",
  "gemini-3.5-flash"
];

async function callModelWithTimeout(modelName: string, apiKey: string, bodyJson: string, timeoutMs: number) {
  const controller = new AbortController();
  const tId = setTimeout(() => controller.abort(), timeoutMs);
  const url = `https://generativelanguage.googleapis.com/v1beta/models/${modelName}:generateContent?key=${apiKey}`;

  try {
    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: bodyJson,
      signal: controller.signal
    });
    clearTimeout(tId);
    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      throw new Error(err.error?.message || `HTTP ${res.status}`);
    }
    const data = await res.json();
    const text = data.candidates?.[0]?.content?.parts?.[0]?.text;
    if (!text) throw new Error("Empty candidate");
    return text;
  } finally {
    clearTimeout(tId);
  }
}

serve(async (req) => {
  if (req.method === "OPTIONS") {
    return new Response("ok", { headers: corsHeaders });
  }

  try {
    const { messages, userContact } = await req.json();

    if (!messages || !Array.isArray(messages) || messages.length === 0) {
      return new Response(
        JSON.stringify({ error: "Missing messages payload" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    const apiKey = Deno.env.get("GEMINI_API_KEY");
    if (!apiKey) {
      return new Response(
        JSON.stringify({ error: "GEMINI_API_KEY is not configured in Supabase Secrets." }),
        { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    const geminiContents = [
      { role: "user", parts: [{ text: SYSTEM_INSTRUCTION }] },
      { role: "model", parts: [{ text: "Understood. I am Wizhy Studio's AI assistant." }] }
    ];

    for (const msg of messages) {
      geminiContents.push({
        role: msg.sender === "user" ? "user" : "model",
        parts: [{ text: msg.text }]
      });
    }

    const postData = JSON.stringify({
      contents: geminiContents,
      generationConfig: {
        temperature: 0.7,
        maxOutputTokens: 300,
      }
    });

    let reply = null;
    let lastErr = null;

    for (const model of CANDIDATE_MODELS) {
      try {
        reply = await callModelWithTimeout(model, apiKey, postData, 5000);
        if (reply) break;
      } catch (e) {
        lastErr = e;
        console.warn(`[Supabase Edge] Model ${model} skipped (${e.message}), trying next...`);
      }
    }

    if (!reply) {
      throw new Error(lastErr?.message || "Service temporarily busy. Please try again.");
    }

    // Automatically detect phone or email in user's message to capture lead
    const lastUserMsg = messages[messages.length - 1]?.text || "";
    const phoneMatch = lastUserMsg.match(/[6-9]\d{9}/);
    const emailMatch = lastUserMsg.match(/[\w.-]+@[\w.-]+\.\w+/);

    let leadSaved = false;
    if (phoneMatch || emailMatch || userContact) {
      const supabaseUrl = Deno.env.get("SUPABASE_URL");
      const supabaseServiceKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY") || Deno.env.get("SUPABASE_ANON_KEY");

      if (supabaseUrl && supabaseServiceKey) {
        try {
          const supabase = createClient(supabaseUrl, supabaseServiceKey);
          await supabase.from("leads").insert([
            {
              phone: phoneMatch ? phoneMatch[0] : (userContact?.phone || null),
              email: emailMatch ? emailMatch[0] : (userContact?.email || null),
              message: `[AI Chat Inquiry]: ${lastUserMsg}`,
              lead_source: "ai_chat_assistant"
            }
          ]);
          // Instant Telegram Admin Notification
          const tgToken = Deno.env.get("TELEGRAM_BOT_TOKEN");
          const tgChatId = Deno.env.get("TELEGRAM_CHAT_ID");
          if (tgToken && tgChatId) {
            try {
              const tgText = `🔔 NEW AI CHAT LEAD on web.wizhy.in!\n• Contact: ${phoneMatch ? phoneMatch[0] : (emailMatch ? emailMatch[0] : 'Provided in chat')}\n• Message: ${lastUserMsg}`;
              fetch(`https://api.telegram.org/bot${tgToken}/sendMessage`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ chat_id: tgChatId, text: tgText })
              }).catch(() => {});
            } catch (_) {}
          }
          leadSaved = true;
        } catch (dbErr) {
          console.warn("Could not save AI lead to Supabase:", dbErr);
        }
      }
    }

    return new Response(
      JSON.stringify({ reply, leadSaved }),
      { headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  } catch (err) {
    return new Response(
      JSON.stringify({ error: err.message }),
      { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }
});
