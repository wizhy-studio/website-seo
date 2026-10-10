// =========================================================
// WIZHY WEB STUDIO — SUPABASE EDGE FUNCTION: wizhy-chat
// Secure serverless backend calling Google Gemini Flash API
// Multi-model resilience pool + Lead Management & Telegram In-Place Edit
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
- Turnaround Time: 3 to 7 business days depending on project scope.

CONVERSATIONAL LEAD INTAKE FLOW (VERY IMPORTANT):
1. Keep replies short (2–3 sentences max). Never overwhelm the visitor with a wall of questions.
2. Step 1 (Contact Capture): When a user shows interest in a website, pricing, or the free offer, invite them to share their Name and WhatsApp number (or email) first.
3. Step 2 (Progressive Details): When they provide their contact details:
   - Warmly acknowledge them by their Name.
   - Mention that their initial request is noted and our team will connect with them.
   - Then naturally ask the next quick question to understand their needs:
     "To help us prepare the best proposal, which package or service are you interested in (e.g. Free 1-page offer, Starter ₹999, or Full Business ₹4,999)?"
4. Step 3 (Requirements & Timeline): Once they name a service or idea, ask:
   "Awesome choice! What kind of business or website are you building, and do you have a target launch date in mind?"
5. Step 4 (Wrap-up): When all details are gathered, celebrate and confirm that everything is logged and our senior strategist will message them on WhatsApp shortly!
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

// Generate Date-based tracking code: YYYYMM-XXXX
function generateTrackingCode(): string {
  const d = new Date();
  const yy = String(d.getFullYear()).slice(-2);
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const rand = Math.floor(1000 + Math.random() * 9000);
  return `${yy}${mm}-${rand}`;
}

// Extract name, phone, email, service from conversation
function parseLeadData(messages: Array<{ sender: string; text: string }>) {
  let phone = "";
  let email = "";
  let name = "";
  let projectService = "";
  let allUserText = "";

  for (const m of messages) {
    if (m.sender === "user") {
      const txt = m.text;
      allUserText += (allUserText ? " | " : "") + txt;

      if (!phone) {
        const pMatch = txt.match(/(?:\+?91[\s-]?)?[6-9]\d{9}/);
        if (pMatch) phone = pMatch[0].replace(/\s+/g, "");
      }

      if (!email) {
        const eMatch = txt.match(/[\w.-]+@[\w.-]+\.[a-zA-Z]{2,}/);
        if (eMatch) email = eMatch[0];
      }

      // Try detecting name if patterns like "name is X" or "I am X" or "My name is X"
      if (!name) {
        const nameMatch = txt.match(/(?:my name is|i am|this is|name\s*[:\-])\s+([A-Za-z]{2,20}(?:\s+[A-Za-z]{2,20})?)/i);
        if (nameMatch) {
          name = nameMatch[1].trim();
        }
      }

      // Detect service interest
      const lower = txt.toLowerCase();
      if (!projectService) {
        if (lower.includes("free") || lower.includes("first 10") || lower.includes("offer")) {
          projectService = "🎁 Free 1-Page Website Offer (₹0)";
        } else if (lower.includes("starter") || lower.includes("999") || lower.includes("single page")) {
          projectService = "1-Page Starter Website (₹999)";
        } else if (lower.includes("growth") || lower.includes("2499") || lower.includes("2,499") || lower.includes("multi")) {
          projectService = "Multi-Page Growth (₹2,499)";
        } else if (lower.includes("ecommerce") || lower.includes("store") || lower.includes("shop") || lower.includes("4999") || lower.includes("business")) {
          projectService = "Full Business / E-Commerce (₹4,999)";
        } else if (lower.includes("seo") || lower.includes("audit")) {
          projectService = "SEO & Google Ranking";
        }
      }
    }
  }

  // Fallback name heuristic: If user said "Rahul and 9876543210" or "Rahul 9876543210"
  if (!name && phone) {
    for (const m of messages) {
      if (m.sender === "user" && m.text.includes(phone.slice(-6))) {
        const cleaned = m.text.replace(phone, "").replace(/(and|my|number|phone|whatsapp|no|\:)/gi, "").trim();
        const words = cleaned.split(/\s+/).filter(w => /^[A-Za-z]{2,20}$/.test(w));
        if (words.length >= 1 && words.length <= 3) {
          name = words.join(" ");
          break;
        }
      }
    }
  }

  const isComplete = Boolean((phone || email) && projectService && messages.filter(m => m.sender === "user").length >= 2);

  return { phone, email, name, projectService, allUserText, isComplete };
}

serve(async (req) => {
  if (req.method === "OPTIONS") {
    return new Response("ok", { headers: corsHeaders });
  }

  try {
    const { messages, leadSession } = await req.json();

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

    // Build Gemini context
    const geminiContents = [
      { role: "user", parts: [{ text: SYSTEM_INSTRUCTION }] },
      { role: "model", parts: [{ text: "Understood. I am WizAI, Wizhy Studio's AI assistant." }] }
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

    // Parse lead information across the entire conversation
    const parsed = parseLeadData(messages);
    let updatedLeadSession = leadSession || null;

    if (parsed.phone || parsed.email) {
      const codeSeq = leadSession?.codeSeq || generateTrackingCode();
      const currentInquiryId = parsed.isComplete ? `INQ-${codeSeq}` : `REF-${codeSeq}`;
      const statusLabel = parsed.isComplete ? "COMPLETE INQUIRY" : "INQUIRY IN PROGRESS (Partial Details)";

      const supabaseUrl = Deno.env.get("SUPABASE_URL");
      const supabaseServiceKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY") || Deno.env.get("SUPABASE_ANON_KEY");
      const tgToken = Deno.env.get("TELEGRAM_BOT_TOKEN");
      const tgChatId = Deno.env.get("TELEGRAM_CHAT_ID");

      // Save / Update to Supabase contact_form_leads table
      if (supabaseUrl && supabaseServiceKey) {
        try {
          const supabase = createClient(supabaseUrl, supabaseServiceKey);
          const leadPayload = {
            name: parsed.name || "WizAI Chat Visitor",
            email: parsed.email || null,
            phone: parsed.phone || null,
            service: parsed.projectService || "Chat Discussion",
            message: `[${currentInquiryId}] User Transcript: ${parsed.allUserText}`,
            lead_source: parsed.isComplete ? "ai_chat_completed" : "ai_chat_partial"
          };

          if (leadSession?.dbRowId) {
            // Update existing row
            await supabase.from("contact_form_leads").update(leadPayload).eq("id", leadSession.dbRowId);
          } else {
            // Insert initial row
            const { data } = await supabase.from("contact_form_leads").insert([leadPayload]).select("id").single();
            if (data?.id) {
              updatedLeadSession = { ...(updatedLeadSession || {}), dbRowId: data.id };
            }
          }
        } catch (dbErr) {
          console.warn("[Supabase Edge] Lead update note:", dbErr);
        }
      }

      // Handle Telegram: editMessageText for single notification, or sendMessage if first time
      if (tgToken && tgChatId) {
        try {
          const tgText = parsed.isComplete
            ? `✅ <b>${statusLabel}: #${currentInquiryId}</b>\n\n` +
              `👤 <b>Name:</b> ${parsed.name || "Client"}\n` +
              `📞 <b>Phone:</b> ${parsed.phone || "Not provided"}\n` +
              `✉️ <b>Email:</b> ${parsed.email || "Not provided"}\n` +
              `💼 <b>Service:</b> ${parsed.projectService}\n` +
              `💬 <b>Details:</b> ${parsed.allUserText}\n` +
              `🌐 <b>Source:</b> WizAI Chat Assistant\n` +
              `⏰ <b>Status:</b> Qualified Lead (All details filled)`
            : `⏳ <b>${statusLabel}: #${currentInquiryId}</b>\n\n` +
              `👤 <b>Name:</b> ${parsed.name || "Visitor"}\n` +
              `📞 <b>Phone:</b> ${parsed.phone || "Not provided"}\n` +
              `✉️ <b>Email:</b> ${parsed.email || "Not provided"}\n` +
              `💼 <b>Service:</b> ${parsed.projectService || "Under discussion"}\n` +
              `💬 <b>Current Input:</b> ${parsed.allUserText}\n` +
              `🌐 <b>Source:</b> WizAI Chat Assistant\n` +
              `⚠️ <i>Status: Saved. WizAI is asking for service requirements...</i>`;

          const existingTgMsgId = leadSession?.tgMessageId;

          if (existingTgMsgId) {
            // OPTION 1: Edit the existing Telegram message in-place!
            const editRes = await fetch(`https://api.telegram.org/bot${tgToken}/editMessageText`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                chat_id: tgChatId,
                message_id: existingTgMsgId,
                text: tgText,
                parse_mode: "HTML"
              })
            });
            const editData = await editRes.json();
            if (!editData.ok) {
              console.warn("[Telegram Edit note]:", editData.description);
            }
          } else {
            // First notification: Send original message and capture its message_id
            const sendRes = await fetch(`https://api.telegram.org/bot${tgToken}/sendMessage`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                chat_id: tgChatId,
                text: tgText,
                parse_mode: "HTML"
              })
            });
            const sendData = await sendRes.json();
            if (sendData.ok && sendData.result?.message_id) {
              updatedLeadSession = {
                ...(updatedLeadSession || {}),
                tgMessageId: sendData.result.message_id,
                codeSeq: codeSeq,
                inquiryId: currentInquiryId
              };
            }
          }
        } catch (tgErr) {
          console.warn("[Telegram Bot Error]:", tgErr);
        }
      }

      if (!updatedLeadSession) {
        updatedLeadSession = { codeSeq, inquiryId: currentInquiryId };
      } else {
        updatedLeadSession.codeSeq = codeSeq;
        updatedLeadSession.inquiryId = currentInquiryId;
      }
    }

    return new Response(
      JSON.stringify({ reply, leadSession: updatedLeadSession }),
      { headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  } catch (err) {
    return new Response(
      JSON.stringify({ error: err.message }),
      { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }
});
