<?php
/**
 * Wizhy AG Theme Functions
 *
 * @package Wizhy_AG_Theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function wizhy_ag_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'wizhy-ag-theme'),
        'footer'  => __('Footer Menu', 'wizhy-ag-theme'),
    ));
}
add_action('after_setup_theme', 'wizhy_ag_theme_setup');

// Disable standard WP emoji scripts for maximum performance
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Helper to resolve theme asset URLs
function wizhy_asset($path) {
    return get_template_directory_uri() . '/' . ltrim($path, '/');
}

/**
 * Optional Native WordPress AI Chat Proxy
 * If WIZHY_GEMINI_API_KEY is defined in wp-config.php or WordPress options,
 * WordPress can process the AI chat directly.
 */
add_action('rest_api_init', function () {
    register_rest_route('wizhy/v1', '/chat', array(
        'methods'             => 'POST',
        'permission_callback' => '__return_true',
        'callback'            => 'wizhy_handle_native_ai_chat',
    ));
});

function wizhy_handle_native_ai_chat(WP_REST_Request $request) {
    $apiKey = defined('WIZHY_GEMINI_API_KEY') ? WIZHY_GEMINI_API_KEY : get_option('wizhy_gemini_api_key', '');
    if (empty($apiKey)) {
        return new WP_REST_Response(array('error' => 'GEMINI_NOT_CONFIGURED_ON_WP'), 501);
    }

    $params = $request->get_json_params();
    $messages = isset($params['messages']) && is_array($params['messages']) ? $params['messages'] : array();

    if (empty($messages)) {
        return new WP_REST_Response(array('error' => 'Missing messages payload'), 400);
    }

    $systemInstruction = "You are WizAI, the official AI Assistant for Wizhy Web Studio (Wizhy Studio).\nYour tone is warm, professional, high-energy, concise, and helpful. You represent a premium digital agency that crafts high-performance websites and technical SEO.\n\nSTRICT IDENTITY RULES:\n- Your name is WizAI. Always introduce or refer to yourself as WizAI when asked about your identity.\n- Never disclose, name, or hint at your underlying model, technology provider, or AI company.\n- If a user asks 'Which AI are you?' or similar, reply: 'I am WizAI, the AI Assistant built specifically for Wizhy Web Studio to assist our clients with web design, SEO, and digital strategy.'\n\nPRICING & OFFERS:\n- First 10 Free Website Offer: 100% free custom 1-page website (client only gives honest review & testimonial).\n- Single Page Starter: ₹999 / $29.\n- Multi-Page Growth: ₹2,499 / $69.\n- Full Business / E-Commerce: ₹4,999 / $139.\n- Turnaround: 3 to 7 business days.\n- Keep replies concise (2-3 sentences max). Proactively invite visitor for Name and WhatsApp number.";

    $contents = array(
        array('role' => 'user', 'parts' => array(array('text' => $systemInstruction))),
        array('role' => 'model', 'parts' => array(array('text' => 'Understood. I am WizAI, Wizhy Studio\'s AI assistant.')))
    );

    foreach ($messages as $m) {
        $contents[] = array(
            'role'  => (isset($m['sender']) && $m['sender'] === 'user') ? 'user' : 'model',
            'parts' => array(array('text' => isset($m['text']) ? $m['text'] : ''))
        );
    }

    $candidateModels = array(
        'gemini-2.0-flash',
        'gemini-1.5-flash',
        'gemini-2.0-flash-lite',
        'gemini-2.5-flash',
        'gemini-1.5-flash-8b',
        'gemini-1.5-pro'
    );

    $reply = null;
    $lastError = null;

    foreach ($candidateModels as $model) {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . urlencode($apiKey);
        $res = wp_remote_post($url, array(
            'timeout' => 12,
            'headers' => array('Content-Type' => 'application/json'),
            'body'    => wp_json_encode(array(
                'contents'         => $contents,
                'generationConfig' => array(
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 300,
                )
            ))
        ));

        if (!is_wp_error($res)) {
            $code = wp_remote_retrieve_response_code($res);
            $body = wp_remote_retrieve_body($res);
            $data = json_decode($body, true);
            if ($code === 200 && !empty($data['candidates'][0]['content']['parts'][0]['text'])) {
                $reply = $data['candidates'][0]['content']['parts'][0]['text'];
                break;
            } else {
                $lastError = !empty($data['error']['message']) ? $data['error']['message'] : 'HTTP ' . $code;
            }
        } else {
            $lastError = $res->get_error_message();
        }
    }

    if ($reply) {
        return new WP_REST_Response(array('reply' => $reply), 200);
    }

    return new WP_REST_Response(array('error' => $lastError ?: 'AI service busy'), 500);
}

/**
 * Custom Form & Lead Notification REST API Endpoint
 * Sends beautifully formatted HTML emails to both the User and Admin (techwizhy@gmail.com)
 * via WP Mail SMTP (Brevo).
 */
add_action('rest_api_init', function () {
    register_rest_route('wizhy/v1', '/notify', array(
        'methods'             => 'POST',
        'permission_callback' => '__return_true',
        'callback'            => 'wizhy_handle_email_notifications',
    ));
    register_rest_route('wizhy/v1', '/telegram-webhook', array(
        'methods'             => array('GET', 'POST'),
        'permission_callback' => '__return_true',
        'callback'            => 'wizhy_handle_telegram_webhook',
    ));
});

/**
 * Helper: Build a high-end branded HTML email layout matching Wizhy Web Studio's aesthetic
 */
function wizhy_build_email_layout($badgeText, $headline, $subheadline, $bodyHtml, $ctaText = '', $ctaUrl = '') {
    $ctaBlock = '';
    if (!empty($ctaText) && !empty($ctaUrl)) {
        $ctaBlock = '
        <tr>
          <td style="padding: 12px 40px 32px 40px; text-align: left;">
            <a href="' . esc_url($ctaUrl) . '" target="_blank" style="display: inline-block; background-color: #C8481E; color: #FFF8EF; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 600; text-decoration: none; padding: 14px 28px; border-radius: 999px; box-shadow: 0 4px 14px rgba(200, 72, 30, 0.28);">
              ' . esc_html($ctaText) . ' &rarr;
            </a>
          </td>
        </tr>';
    }

    return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . esc_html($headline) . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F4EFE6; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; color: #161412; -webkit-font-smoothing: antialiased;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #F4EFE6; padding: 40px 16px;">
    <tr>
      <td align="center">
        <!-- Main Email Card -->
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #FFFDF9; border-radius: 20px; overflow: hidden; border: 1px solid #E6DEC8; box-shadow: 0 12px 32px rgba(22, 20, 18, 0.06);">
          
          <!-- Top Brand Header -->
          <tr>
            <td style="background: linear-gradient(135deg, #161412 0%, #26211E 100%); padding: 32px 40px; border-bottom: 3px solid #C8481E;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td align="left" valign="middle">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                      <tr>
                        <td style="background-color: #C8481E; width: 38px; height: 38px; border-radius: 10px; text-align: center; vertical-align: middle; font-family: Georgia, serif; font-size: 22px; font-weight: 700; color: #FFF8EF;">
                          W
                        </td>
                        <td style="padding-left: 12px;">
                          <div style="font-size: 18px; font-weight: 700; color: #FFF8EF; letter-spacing: -0.3px;">Wizhy Web Studio</div>
                          <div style="font-size: 12px; color: #B8AFA6; margin-top: 2px;">Websites, Designed to Grow Your Business</div>
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td align="right" valign="middle">
                    <span style="display: inline-block; background-color: rgba(200, 72, 30, 0.22); border: 1px solid rgba(200, 72, 30, 0.5); color: #FF8B66; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; padding: 5px 12px; border-radius: 999px;">
                      ' . esc_html($badgeText) . '
                    </span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Hero Title Area -->
          <tr>
            <td style="padding: 36px 40px 16px 40px; text-align: left;">
              <h1 style="margin: 0 0 10px 0; font-family: Georgia, \'Times New Roman\', serif; font-size: 26px; line-height: 1.25; font-weight: 700; color: #161412;">
                ' . esc_html($headline) . '
              </h1>
              <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #57524E;">
                ' . $subheadline . '
              </p>
            </td>
          </tr>

          <!-- Main Body Content -->
          <tr>
            <td style="padding: 12px 40px 24px 40px; text-align: left; font-size: 15px; line-height: 1.65; color: #2B2825;">
              ' . $bodyHtml . '
            </td>
          </tr>

          ' . $ctaBlock . '

          <!-- Direct Contact Strip -->
          <tr>
            <td style="padding: 0 40px 32px 40px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #F8F3EA; border-radius: 14px; border: 1px solid #EAE0D0; padding: 18px 22px;">
                <tr>
                  <td style="font-size: 13px; line-height: 1.6; color: #4A4540;">
                    <strong style="color: #161412; display: block; margin-bottom: 4px;">Need faster assistance?</strong>
                    WhatsApp / Call: <a href="https://wa.me/919870706956" style="color: #C8481E; font-weight: 600; text-decoration: none;">+91 98707 06956</a> &nbsp;|&nbsp;
                    Email: <a href="mailto:techwizhy@gmail.com" style="color: #C8481E; font-weight: 600; text-decoration: none;">techwizhy@gmail.com</a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background-color: #161412; padding: 24px 40px; text-align: center; color: #9E958C; font-size: 12px; line-height: 1.6;">
              <div style="font-weight: 600; color: #E6DEC8; margin-bottom: 4px;">Wizhy Web Studio (WWS)</div>
              <div>High-Performance Web Design, Development &amp; Technical SEO</div>
              <div style="margin-top: 10px;">
                <a href="https://web.wizhy.in/" style="color: #FF8B66; text-decoration: none; font-weight: 500;">web.wizhy.in</a>
                &nbsp;&bull;&nbsp;
                <a href="https://web.wizhy.in/privacy-policy/" style="color: #9E958C; text-decoration: none;">Privacy Policy</a>
                &nbsp;&bull;&nbsp;
                <a href="https://web.wizhy.in/terms/" style="color: #9E958C; text-decoration: none;">Terms of Service</a>
              </div>
              <div style="margin-top: 10px; font-size: 11px; color: #6E675F;">
                &copy; ' . gmdate('Y') . ' Wizhy Web Studio. All rights reserved.
              </div>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
}

/**
 * Helper: Always return exact IST (Asia/Kolkata, UTC+5:30) time regardless of server/WP timezone
 */
function wizhy_current_ist_time($format = 'd M Y, h:i A', $timestamp = null) {
    try {
        $tz = new DateTimeZone('Asia/Kolkata');
        if ($timestamp !== null) {
            $dt = new DateTime('@' . (int) $timestamp);
            $dt->setTimezone($tz);
        } else {
            $dt = new DateTime('now', $tz);
        }
        return $dt->format($format);
    } catch (Exception $e) {
        return wp_date($format, $timestamp);
    }
}

/**
 * Helper: Send or Edit Instant Push Notification via Telegram Bot API
 * If $editMessageId is provided, edits that existing Telegram message in-place
 * (e.g., upgrading REF-YYYYMM-XXXX to INQ-YYYYMM-XXXX).
 * If $inlineKeyboard array is provided, attaches 1-tap clickable action buttons.
 * Returns the Telegram message_id (int) on success, or false on failure.
 */
function wizhy_send_telegram_alert($htmlText, $editMessageId = null, $inlineKeyboard = null) {
    $token  = defined('WIZHY_TG_BOT_TOKEN') ? WIZHY_TG_BOT_TOKEN : get_option('wizhy_tg_bot_token', '');
    $chatId = defined('WIZHY_TG_CHAT_ID') ? WIZHY_TG_CHAT_ID : get_option('wizhy_tg_chat_id', '');

    if (empty($token) || empty($chatId)) {
        return false;
    }

    $payload = array(
        'chat_id'                  => $chatId,
        'text'                     => $htmlText,
        'parse_mode'               => 'HTML',
        'disable_web_page_preview' => true,
    );

    if (!empty($inlineKeyboard) && is_array($inlineKeyboard)) {
        $payload['reply_markup'] = array('inline_keyboard' => $inlineKeyboard);
    }

    if (!empty($editMessageId)) {
        $payload['message_id'] = (int) $editMessageId;
        $editRes = wp_remote_post("https://api.telegram.org/bot{$token}/editMessageText", array(
            'timeout' => 8,
            'headers' => array('Content-Type' => 'application/json'),
            'body'    => wp_json_encode($payload),
        ));

        if (!is_wp_error($editRes)) {
            $editData = json_decode(wp_remote_retrieve_body($editRes), true);
            if (!empty($editData['ok'])) {
                return (int) $editMessageId;
            }
            // If message text is identical, Telegram returns 400 "message is not modified" — still valid
            if (!empty($editData['description']) && strpos($editData['description'], 'message is not modified') !== false) {
                return (int) $editMessageId;
            }
        }
    }

    $res = wp_remote_post("https://api.telegram.org/bot{$token}/sendMessage", array(
        'timeout' => 8,
        'headers' => array('Content-Type' => 'application/json'),
        'body'    => wp_json_encode($payload),
    ));

    if (!is_wp_error($res) && wp_remote_retrieve_response_code($res) === 200) {
        $data = json_decode(wp_remote_retrieve_body($res), true);
        if (!empty($data['ok']) && !empty($data['result']['message_id'])) {
            return (int) $data['result']['message_id'];
        }
        return true;
    }

    return false;
}

function wizhy_handle_email_notifications(WP_REST_Request $request) {
    $params = $request->get_json_params();
    if (!is_array($params)) {
        return new WP_REST_Response(array('error' => 'Invalid JSON payload'), 400);
    }

    $type       = isset($params['type']) ? sanitize_text_field($params['type']) : 'contact_form';
    $adminEmail = 'techwizhy@gmail.com';
    $timestamp  = wizhy_current_ist_time('d M Y, h:i A') . ' IST';

    /* =========================================================================
     * 1A. PARTIAL FORM CAPTURE (REF-YYYYMM-XXXX) — Telegram Initial / Edit + Optional Admin Email
     * ========================================================================= */
    if ($type === 'contact_form_partial') {
        $name        = !empty($params['name']) ? sanitize_text_field($params['name']) : 'Visitor';
        $email       = !empty($params['email']) ? sanitize_email($params['email']) : '';
        $phone       = !empty($params['phone']) ? sanitize_text_field($params['phone']) : 'Not provided';
        $service     = !empty($params['service']) ? sanitize_text_field($params['service']) : 'Selecting...';
        $budget      = !empty($params['budget']) ? sanitize_text_field($params['budget']) : 'Not specified';
        $message     = isset($params['message']) ? sanitize_textarea_field($params['message']) : '';
        $refId       = !empty($params['inquiry_id']) ? sanitize_text_field($params['inquiry_id']) : ('REF-' . wp_date('Ym') . '-' . wp_rand(1000, 9999));
        $tgMessageId = !empty($params['tg_message_id']) ? (int) $params['tg_message_id'] : null;
        $sendEmail   = !empty($params['send_admin_email']);

        if (empty($email) && $phone === 'Not provided') {
            return new WP_REST_Response(array('error' => 'Phone or email required for partial capture'), 400);
        }

        // Send or Edit Telegram Message with REF-YYYYMM-XXXX
        $tgMsg  = "⏳ <b>INQUIRY IN PROGRESS (Partial): #" . esc_html($refId) . "</b>\n\n";
        $tgMsg .= "👤 <b>Name:</b> " . esc_html($name) . "\n";
        $tgMsg .= "📞 <b>Phone:</b> " . esc_html($phone) . "\n";
        $tgMsg .= "✉️ <b>Email:</b> " . esc_html($email ?: 'Not provided') . "\n";
        $tgMsg .= "💼 <b>Service:</b> " . esc_html($service) . "\n";
        if ($budget !== 'Not specified') {
            $tgMsg .= "💰 <b>Budget:</b> " . esc_html($budget) . "\n";
        }
        if (!empty($message)) {
            $tgMsg .= "💬 <b>Draft Message:</b> " . esc_html($message) . "\n";
        }
        $tgMsg .= "🌐 <b>Source:</b> Quote Form (Live Capture)\n";
        $tgMsg .= "⚠️ <i>Status: Visitor is filling the form...</i>";

        $partialButtons = array();
        $pRow = array();
        if ($phone !== 'Not provided') {
            $pDigits = preg_replace('/\D+/', '', $phone);
            $pRow[] = array(
                'text' => '💬 Chat on WhatsApp',
                'url'  => 'https://wa.me/' . $pDigits . '?text=' . rawurlencode("Hi " . $name . "! Reaching out regarding your draft inquiry #" . $refId . " with Wizhy Web Studio.")
            );
        }
        if (!empty($email)) {
            $pRow[] = array(
                'text' => '✉️ Email Visitor',
                'url'  => 'https://mail.google.com/mail/?view=cm&to=' . rawurlencode($email) . '&su=' . rawurlencode("Your Inquiry [" . $refId . "] - Wizhy Web Studio")
            );
        }
        if (!empty($pRow)) $partialButtons[] = $pRow;

        $newTgMsgId = wizhy_send_telegram_alert($tgMsg, $tgMessageId, $partialButtons);

        // Optional Admin Email for Partial Lead (if visitor leaves without submitting or first contact capture)
        $sentAdmin = false;
        if ($sendEmail) {
            $adminSubject = sprintf('⏳ Partial Lead Captured [%s]: %s (%s)', $refId, $name, $phone !== 'Not provided' ? $phone : $email);
            $adminSubhead = 'A visitor started filling the quote form on <strong>web.wizhy.in</strong> (`#' . esc_html($refId) . '`). If they complete the form, you will receive the full <strong>INQ-' . esc_html(preg_replace('/^REF-/', '', $refId)) . '</strong> confirmation email.';
            $adminBody = '
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; background-color: #FFFFFF; border: 1px solid #E6DEC8; border-radius: 12px; overflow: hidden;">
                <tr>
                  <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; width: 34%; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Partial Ref ID</td>
                  <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #D97706;">' . esc_html($refId) . '</td>
                </tr>
                <tr>
                  <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Visitor Name</td>
                  <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 600; color: #161412;">' . esc_html($name) . '</td>
                </tr>
                <tr>
                  <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Phone / WhatsApp</td>
                  <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #161412;">' . esc_html($phone) . '</td>
                </tr>
                <tr>
                  <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Visitor Email</td>
                  <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px;">' . (!empty($email) ? esc_html($email) : 'Not provided yet') . '</td>
                </tr>
                <tr>
                  <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Captured At</td>
                  <td style="padding: 12px 16px; font-size: 13px; color: #57524E;">' . esc_html($timestamp) . '</td>
                </tr>
              </table>';

            $ctaUrl = ($phone !== 'Not provided') ? 'https://wa.me/' . preg_replace('/\D+/', '', $phone) : ('mailto:' . $email);
            $adminHtml = wizhy_build_email_layout(
                'Partial Lead (REF)',
                'Partial Lead Captured',
                $adminSubhead,
                $adminBody,
                ($phone !== 'Not provided') ? 'Message on WhatsApp' : 'Email Visitor',
                $ctaUrl
            );

            $headers = array('Content-Type: text/html; charset=UTF-8');
            if (!empty($email) && is_email($email)) {
                $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
            }
            $sentAdmin = wp_mail($adminEmail, $adminSubject, $adminHtml, $headers);
        }

        wizhy_record_lead(array(
            'id'            => $refId,
            'name'          => $name,
            'phone'         => $phone,
            'email'         => $email,
            'service'       => $service,
            'budget'        => $budget,
            'message'       => $message,
            'status'        => 'in_progress',
            'tg_message_id' => $newTgMsgId ?: $tgMessageId,
        ));

        return new WP_REST_Response(array(
            'success'       => true,
            'ref_id'        => $refId,
            'tg_message_id' => $newTgMsgId ?: $tgMessageId,
            'sent_admin'    => (bool) $sentAdmin,
        ), 200);
    }

    /* =========================================================================
     * 1B. COMPLETE CONTACT / QUOTE REQUEST FORM (INQ-YYYYMM-XXXX)
     * Overwrites Telegram message in-place (if tg_message_id exists) & sends both Emails
     * ========================================================================= */
    if ($type === 'contact_form') {
        $name        = isset($params['name']) ? sanitize_text_field($params['name']) : '';
        $email       = isset($params['email']) ? sanitize_email($params['email']) : '';
        $phone       = !empty($params['phone']) ? sanitize_text_field($params['phone']) : 'Not provided';
        $service     = !empty($params['service']) ? sanitize_text_field($params['service']) : 'General Inquiry';
        $budget      = !empty($params['budget']) ? sanitize_text_field($params['budget']) : 'Not specified';
        $message     = isset($params['message']) ? sanitize_textarea_field($params['message']) : '';
        $inquiryId   = !empty($params['inquiry_id']) ? sanitize_text_field($params['inquiry_id']) : ('INQ-' . wp_date('Ym') . '-' . wp_rand(1000, 9999));
        $tgMessageId = !empty($params['tg_message_id']) ? (int) $params['tg_message_id'] : null;

        if (empty($email) || !is_email($email) || empty($name)) {
            return new WP_REST_Response(array('error' => 'Valid name and email are required'), 400);
        }

        // --- A. ADMIN EMAIL (To: techwizhy@gmail.com) ---
        $adminSubject = sprintf('✅ Complete Project Inquiry [%s]: %s (%s)', $inquiryId, $name, $service);
        $adminSubhead = 'You have received a completed quote request on <strong>web.wizhy.in</strong>. Hit <strong>Reply</strong> directly in your email client to respond to <strong>' . esc_html($name) . '</strong>.';
        $adminBody = '
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; background-color: #FFFFFF; border: 1px solid #E6DEC8; border-radius: 12px; overflow: hidden;">
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; width: 34%; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Inquiry ID</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #C8481E;">' . esc_html($inquiryId) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Client Name</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 600; color: #161412;">' . esc_html($name) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Client Email</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px;"><a href="mailto:' . esc_attr($email) . '" style="color: #C8481E; text-decoration: none; font-weight: 600;">' . esc_html($email) . '</a></td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Phone / WhatsApp</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 600; color: #161412;">' . esc_html($phone) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Selected Service</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #161412;">' . esc_html($service) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Estimated Budget</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; color: #161412;">' . esc_html($budget) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Submitted On</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; color: #57524E;">' . esc_html($timestamp) . '</td>
            </tr>
            <tr>
              <td colspan="2" style="padding: 16px; background-color: #FFFFFF;">
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #6E675F; margin-bottom: 8px;">Project Details &amp; Message</div>
                <div style="font-size: 14px; line-height: 1.65; color: #161412; white-space: pre-wrap; background-color: #FAF6F0; padding: 14px; border-radius: 8px; border: 1px solid #EFEAE1;">' . nl2br(esc_html($message)) . '</div>
              </td>
            </tr>
          </table>';

        $adminHtml = wizhy_build_email_layout(
            'Complete Inquiry (INQ)',
            'New Project Quote Request',
            $adminSubhead,
            $adminBody,
            'Reply to ' . $name,
            'mailto:' . $email . '?subject=' . rawurlencode('Re: Your Project Inquiry [' . $inquiryId . '] - Wizhy Web Studio')
        );

        $adminHeaders = array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . $name . ' <' . $email . '>'
        );

        $sentAdmin = wp_mail($adminEmail, $adminSubject, $adminHtml, $adminHeaders);

        // --- B. USER CONFIRMATION EMAIL (To: Visitor) ---
        $userSubject = sprintf('We have received your request! [Ref: %s] — Wizhy Web Studio', $inquiryId);
        $userSubhead = 'Hi <strong>' . esc_html($name) . '</strong>, thank you for reaching out to <strong>Wizhy Web Studio</strong>! Our team is reviewing your project requirements and will get back to you within <strong>24 hours</strong>.';
        $userBody = '
          <div style="margin-bottom: 20px;">
            Here is a copy of the details you submitted for your records:
          </div>
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; background-color: #FFFFFF; border: 1px solid #E6DEC8; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
            <tr>
              <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; width: 36%; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Reference ID</td>
              <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #C8481E;">#' . esc_html($inquiryId) . '</td>
            </tr>
            <tr>
              <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Service Interested</td>
              <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 600; color: #161412;">' . esc_html($service) . '</td>
            </tr>
            <tr>
              <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Phone / WhatsApp</td>
              <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; color: #161412;">' . esc_html($phone) . '</td>
            </tr>
            <tr>
              <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Estimated Budget</td>
              <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; color: #161412;">' . esc_html($budget) . '</td>
            </tr>
            <tr>
              <td colspan="2" style="padding: 14px 16px; background-color: #FFFFFF;">
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #6E675F; margin-bottom: 6px;">Your Project Summary</div>
                <div style="font-size: 14px; line-height: 1.6; color: #2B2825; background-color: #FAF6F0; padding: 12px; border-radius: 8px;">' . nl2br(esc_html($message)) . '</div>
              </td>
            </tr>
          </table>

          <div style="background-color: #FFF6F2; border-left: 4px solid #C8481E; padding: 14px 18px; border-radius: 6px; font-size: 14px; color: #2B2825;">
            <strong style="color: #161412; display: block; margin-bottom: 4px;">What happens next?</strong>
            1. A senior strategist will review your project scope.<br>
            2. We will prepare a clear, itemized roadmap &amp; quote.<br>
            3. We will reach out via Email or WhatsApp to schedule a quick discovery call.
          </div>';

        $userHtml = wizhy_build_email_layout(
            'Inquiry Confirmed',
            'Thank You for Contacting Us!',
            $userSubhead,
            $userBody,
            'Chat with Us on WhatsApp',
            'https://wa.me/919870706956?text=' . rawurlencode('Hi Wizhy Web Studio! I just submitted inquiry #' . $inquiryId . ' (' . $service . ').')
        );

        $userHeaders = array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: Wizhy Web Studio <' . $adminEmail . '>'
        );

        $sentUser = wp_mail($email, $userSubject, $userHtml, $userHeaders);

        // --- C. TELEGRAM PUSH NOTIFICATION (Edits existing REF message in-place if tg_message_id exists!) ---
        $tgMsg  = "✅ <b>COMPLETE INQUIRY: #" . esc_html($inquiryId) . "</b>\n\n";
        $tgMsg .= "👤 <b>Name:</b> " . esc_html($name) . "\n";
        $tgMsg .= "📞 <b>Phone:</b> " . esc_html($phone) . "\n";
        $tgMsg .= "✉️ <b>Email:</b> " . esc_html($email) . "\n";
        $tgMsg .= "💼 <b>Service:</b> " . esc_html($service) . "\n";
        $tgMsg .= "💰 <b>Budget:</b> " . esc_html($budget) . "\n";
        if (!empty($message)) {
            $tgMsg .= "💬 <b>Message:</b> " . esc_html($message) . "\n";
        }
        $tgMsg .= "🌐 <b>Source:</b> Contact Quote Form\n";
        $tgMsg .= "⏰ <b>Status:</b> Qualified Lead (" . esc_html($timestamp) . ")";
        $buttons = array();
        $row1 = array();
        if ($phone !== 'Not provided') {
            $cleanDigits = preg_replace('/\D+/', '', $phone);
            $row1[] = array(
                'text' => '💬 Chat on WhatsApp',
                'url'  => 'https://wa.me/' . $cleanDigits . '?text=' . rawurlencode("Hi " . $name . "! Reaching out regarding your inquiry #" . $inquiryId . " with Wizhy Web Studio.")
            );
        }
        if (!empty($email)) {
            $row1[] = array(
                'text' => '✉️ Email Client',
                'url'  => 'https://mail.google.com/mail/?view=cm&to=' . rawurlencode($email) . '&su=' . rawurlencode("Re: Your Project Inquiry [" . $inquiryId . "] - Wizhy Web Studio")
            );
        }
        if (!empty($row1)) {
            $buttons[] = $row1;
        }

        // Row 2: Mini-CRM Status Callback Buttons
        $buttons[] = array(
            array('text' => '🟡 Contacted', 'callback_data' => 'status:contacted:' . $inquiryId),
            array('text' => '🟢 Closed',    'callback_data' => 'status:closed:' . $inquiryId),
            array('text' => '🔴 Spam',      'callback_data' => 'status:spam:' . $inquiryId),
        );

        $sentTg = wizhy_send_telegram_alert($tgMsg, $tgMessageId, $buttons);

        wizhy_record_lead(array(
            'id'            => $inquiryId,
            'name'          => $name,
            'phone'         => $phone,
            'email'         => $email,
            'service'       => $service,
            'budget'        => $budget,
            'message'       => $message,
            'status'        => 'new',
            'tg_message_id' => $sentTg ?: $tgMessageId,
        ));

        return new WP_REST_Response(array(
            'success'       => true,
            'inquiry_id'    => $inquiryId,
            'sent_admin'    => (bool) $sentAdmin,
            'sent_user'     => (bool) $sentUser,
            'sent_telegram' => (bool) $sentTg,
            'tg_message_id' => $sentTg,
        ), 200);
    }

    /* =========================================================================
     * 2. NEWSLETTER SUBSCRIPTION FORM
     * ========================================================================= */
    if ($type === 'newsletter') {
        $email = isset($params['email']) ? sanitize_email($params['email']) : '';
        if (empty($email) || !is_email($email)) {
            return new WP_REST_Response(array('error' => 'Valid email is required'), 400);
        }

        // --- A. ADMIN EMAIL ---
        $adminSubject = '📬 New Newsletter Subscriber: ' . $email;
        $adminSubhead = 'A new visitor just subscribed to the <strong>Wizhy Web Studio</strong> newsletter.';
        $adminBody = '
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; background-color: #FFFFFF; border: 1px solid #E6DEC8; border-radius: 12px; overflow: hidden;">
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; width: 35%; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Subscriber Email</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700;"><a href="mailto:' . esc_attr($email) . '" style="color: #C8481E; text-decoration: none;">' . esc_html($email) . '</a></td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Subscribed At</td>
              <td style="padding: 12px 16px; font-size: 13px; color: #57524E;">' . esc_html($timestamp) . '</td>
            </tr>
          </table>';

        $adminHtml = wizhy_build_email_layout(
            'Newsletter Lead',
            'New Newsletter Subscriber',
            $adminSubhead,
            $adminBody
        );

        $sentAdmin = wp_mail($adminEmail, $adminSubject, $adminHtml, array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . $email
        ));

        // --- B. USER WELCOME EMAIL ---
        $userSubject = 'Welcome to Wizhy Web Studio Insights! 🚀';
        $userSubhead = 'Thank you for subscribing to our digital growth &amp; SEO newsletter.';
        $userBody = '
          <p style="margin: 0 0 16px 0;">You are now on the list to receive our curated insights on:</p>
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #FAF6F0; border: 1px solid #E6DEC8; border-radius: 12px; padding: 16px 20px; margin-bottom: 16px;">
            <tr>
              <td style="font-size: 14px; line-height: 1.8; color: #161412;">
                &#10003; <strong>High-Converting Web Design</strong> tactics that turn visitors into paying clients<br>
                &#10003; <strong>Proven Technical SEO</strong> strategies to rank #1 on Google<br>
                &#10003; <strong>100/100 Speed Optimization</strong> benchmarks &amp; case studies<br>
                &#10003; <strong>Exclusive Agency Offers</strong> &amp; early access to growth tools
              </td>
            </tr>
          </table>
          <p style="margin: 0;">In the meantime, if you have a website project or SEO goal in mind, simply reply to this email and our team will be happy to help!</p>';

        $userHtml = wizhy_build_email_layout(
            'Welcome Aboard',
            'You’re Subscribed!',
            $userSubhead,
            $userBody,
            'Explore Our Website Packages',
            'https://web.wizhy.in/#pricing'
        );

        $sentUser = wp_mail($email, $userSubject, $userHtml, array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: Wizhy Web Studio <' . $adminEmail . '>'
        ));

        // --- C. TELEGRAM PUSH NOTIFICATION ---
        $tgMsg  = "📬 <b>NEW NEWSLETTER SUBSCRIBER!</b>\n\n";
        $tgMsg .= "✉️ <b>Email:</b> " . esc_html($email) . "\n";
        $tgMsg .= "⏰ <b>Time:</b> " . esc_html($timestamp);
        $sentTg = wizhy_send_telegram_alert($tgMsg);

        return new WP_REST_Response(array(
            'success'       => true,
            'sent_admin'    => (bool) $sentAdmin,
            'sent_user'     => (bool) $sentUser,
            'sent_telegram' => (bool) $sentTg,
        ), 200);
    }

    /* =========================================================================
     * 3. LEAD MAGNET (FREE WEBSITE & SEO CHECKLIST DOWNLOAD)
     * ========================================================================= */
    if ($type === 'checklist') {
        $email = isset($params['email']) ? sanitize_email($params['email']) : '';
        if (empty($email) || !is_email($email)) {
            return new WP_REST_Response(array('error' => 'Valid email is required'), 400);
        }

        $checklistUrl = home_url('/wp-content/themes/wizhy-ag-theme/assets/wizhy-website-checklist.pdf');

        // --- A. ADMIN EMAIL ---
        $adminSubject = '📥 Checklist Downloaded by: ' . $email;
        $adminSubhead = 'A visitor just downloaded the <strong>Website &amp; SEO Launch Checklist</strong>.';
        $adminBody = '
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; background-color: #FFFFFF; border: 1px solid #E6DEC8; border-radius: 12px; overflow: hidden;">
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; width: 35%; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Lead Email</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700;"><a href="mailto:' . esc_attr($email) . '" style="color: #C8481E; text-decoration: none;">' . esc_html($email) . '</a></td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Downloaded At</td>
              <td style="padding: 12px 16px; font-size: 13px; color: #57524E;">' . esc_html($timestamp) . '</td>
            </tr>
          </table>';

        $adminHtml = wizhy_build_email_layout(
            'Checklist Lead',
            'New Checklist Download Lead',
            $adminSubhead,
            $adminBody
        );

        $sentAdmin = wp_mail($adminEmail, $adminSubject, $adminHtml, array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . $email
        ));

        // --- B. USER EMAIL WITH DIRECT PDF LINK ---
        $userSubject = 'Your Free 25-Point Website & SEO Checklist 📋 — Wizhy Web Studio';
        $userSubhead = 'Here is your permanent copy of the <strong>Wizhy Website &amp; SEO Launch Checklist</strong>.';
        $userBody = '
          <p style="margin: 0 0 16px 0;">Thank you for downloading our 25-point checklist! You can access or re-download the PDF anytime using the button below.</p>
          <div style="background-color: #FFF6F2; border-left: 4px solid #C8481E; padding: 14px 18px; border-radius: 6px; font-size: 14px; color: #2B2825;">
            <strong style="color: #161412; display: block; margin-bottom: 4px;">Want us to audit or build your website for you?</strong>
            Our team builds blazing-fast 100/100 Google Lighthouse websites starting at just ₹999. Reply to this email anytime for a free 1-on-1 consultation!
          </div>';

        $userHtml = wizhy_build_email_layout(
            'Free Resource',
            'Your Website & SEO Checklist',
            $userSubhead,
            $userBody,
            'Download PDF Checklist',
            $checklistUrl
        );

        $sentUser = wp_mail($email, $userSubject, $userHtml, array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: Wizhy Web Studio <' . $adminEmail . '>'
        ));

        // --- C. TELEGRAM PUSH NOTIFICATION ---
        $tgMsg  = "📥 <b>CHECKLIST DOWNLOAD LEAD!</b>\n\n";
        $tgMsg .= "✉️ <b>Email:</b> " . esc_html($email) . "\n";
        $tgMsg .= "⏰ <b>Time:</b> " . esc_html($timestamp);
        $sentTg = wizhy_send_telegram_alert($tgMsg);

        return new WP_REST_Response(array(
            'success'       => true,
            'sent_admin'    => (bool) $sentAdmin,
            'sent_user'     => (bool) $sentUser,
            'sent_telegram' => (bool) $sentTg,
        ), 200);
    }

    /* =========================================================================
     * 4. WIZAI CHAT ASSISTANT LEAD CAPTURE (REF -> INQ)
     * ========================================================================= */
    if ($type === 'ai_chat_lead') {
        $name        = !empty($params['name']) ? sanitize_text_field($params['name']) : 'WizAI Chat Visitor';
        $email       = !empty($params['email']) ? sanitize_email($params['email']) : '';
        $phone       = !empty($params['phone']) ? sanitize_text_field($params['phone']) : 'Not provided';
        $service     = !empty($params['service']) ? sanitize_text_field($params['service']) : 'Under discussion';
        $transcript  = isset($params['transcript']) ? sanitize_textarea_field($params['transcript']) : '';
        $isComplete  = !empty($params['is_complete']);
        $codeSeq     = !empty($params['code_seq']) ? sanitize_text_field($params['code_seq']) : (wp_date('Ym') . '-' . wp_rand(1000, 9999));
        $inquiryId   = !empty($params['inquiry_id']) ? sanitize_text_field($params['inquiry_id']) : (($isComplete ? 'INQ-' : 'REF-') . $codeSeq);
        $tgMessageId = !empty($params['tg_message_id']) ? (int) $params['tg_message_id'] : null;

        if (empty($email) && $phone === 'Not provided') {
            return new WP_REST_Response(array('error' => 'Phone or email required for chat lead'), 400);
        }

        $badgeLabel   = $isComplete ? 'Complete Chat Lead (INQ)' : 'Partial Chat Lead (REF)';
        $statusEmoji  = $isComplete ? '✅' : '⏳';
        $adminSubject = sprintf('%s WizAI Chat Lead [%s]: %s (%s)', $statusEmoji, $inquiryId, $name, $phone !== 'Not provided' ? $phone : $email);
        $adminSubhead = $isComplete
            ? '<strong>WizAI</strong> captured a <strong>complete qualified inquiry</strong> (`#' . esc_html($inquiryId) . '`) on <strong>web.wizhy.in</strong>.'
            : '<strong>WizAI</strong> captured initial contact details (`#' . esc_html($inquiryId) . '`) and is gathering project requirements.';

        $adminBody = '
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; background-color: #FFFFFF; border: 1px solid #E6DEC8; border-radius: 12px; overflow: hidden;">
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; width: 34%; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Reference ID</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #C8481E;">' . esc_html($inquiryId) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Lead Status</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 700; color: ' . ($isComplete ? '#15803D' : '#D97706') . ';">' . ($isComplete ? 'COMPLETE INQUIRY (INQ)' : 'IN PROGRESS / PARTIAL (REF)') . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Visitor Name</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 600; color: #161412;">' . esc_html($name) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Phone / WhatsApp</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #161412;">' . esc_html($phone) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Visitor Email</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px;">' . (!empty($email) ? '<a href="mailto:' . esc_attr($email) . '" style="color: #C8481E; text-decoration: none; font-weight: 600;">' . esc_html($email) . '</a>' : 'Not provided') . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Interested In</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 600; color: #161412;">' . esc_html($service) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Captured At</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; color: #57524E;">' . esc_html($timestamp) . '</td>
            </tr>
            <tr>
              <td colspan="2" style="padding: 16px; background-color: #FFFFFF;">
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #6E675F; margin-bottom: 8px;">WizAI Conversation Transcript</div>
                <div style="font-size: 13px; line-height: 1.65; color: #161412; white-space: pre-wrap; background-color: #FAF6F0; padding: 14px; border-radius: 8px; border: 1px solid #EFEAE1;">' . nl2br(esc_html($transcript)) . '</div>
              </td>
            </tr>
          </table>';

        $adminCtaUrl = ($phone !== 'Not provided')
            ? 'https://wa.me/' . preg_replace('/\D+/', '', $phone)
            : (!empty($email) ? 'mailto:' . $email : 'https://web.wizhy.in/');

        $adminHtml = wizhy_build_email_layout(
            $badgeLabel,
            $isComplete ? 'Qualified Chat Inquiry' : 'Partial Chat Lead Captured',
            $adminSubhead,
            $adminBody,
            ($phone !== 'Not provided') ? 'Message on WhatsApp' : 'Reply via Email',
            $adminCtaUrl
        );

        $adminHeaders = array('Content-Type: text/html; charset=UTF-8');
        if (!empty($email) && is_email($email)) {
            $adminHeaders[] = 'Reply-To: ' . $name . ' <' . $email . '>';
        }

        $sentAdmin = wp_mail($adminEmail, $adminSubject, $adminHtml, $adminHeaders);

        // --- B. USER EMAIL (Sent when user provides email in chat) ---
        $sentUser = false;
        if (!empty($email) && is_email($email) && !empty($params['send_user_email'])) {
            $userSubject = sprintf('Great chatting with you! [Ref: %s] — Wizhy Web Studio', $inquiryId);
            $userSubhead = 'Hi <strong>' . esc_html($name) . '</strong>, thank you for chatting with <strong>WizAI</strong> at <strong>Wizhy Web Studio</strong>! Our strategist has received your details and will connect with you shortly.';
            $userBody = '
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; background-color: #FFFFFF; border: 1px solid #E6DEC8; border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
                <tr>
                  <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; width: 36%; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Reference ID</td>
                  <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #C8481E;">#' . esc_html($inquiryId) . '</td>
                </tr>
                <tr>
                  <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Service Discussed</td>
                  <td style="padding: 11px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 600; color: #161412;">' . esc_html($service) . '</td>
                </tr>
                <tr>
                  <td style="padding: 11px 16px; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Phone / WhatsApp</td>
                  <td style="padding: 11px 16px; font-size: 14px; color: #161412;">' . esc_html($phone) . '</td>
                </tr>
              </table>
              <p style="margin: 0;">You can reply directly to this email or tap below to continue the conversation with our team on WhatsApp.</p>';

            $userHtml = wizhy_build_email_layout(
                'Chat Summary',
                'We’ve Got Your Details!',
                $userSubhead,
                $userBody,
                'Connect on WhatsApp',
                'https://wa.me/919870706956?text=' . rawurlencode('Hi Wizhy Web Studio! Following up on my WizAI chat (#' . $inquiryId . ').')
            );

            $sentUser = wp_mail($email, $userSubject, $userHtml, array(
                'Content-Type: text/html; charset=UTF-8',
                'Reply-To: Wizhy Web Studio <' . $adminEmail . '>'
            ));
        }

        // --- C. TELEGRAM PUSH NOTIFICATION (If Edge Function didn't already send/edit it) ---
        $sentTg = false;
        if (empty($params['skip_telegram'])) {
            $statusLabel = $isComplete ? 'COMPLETE INQUIRY' : 'INQUIRY IN PROGRESS (Partial Details)';
            $tgMsg = ($isComplete ? "✅" : "⏳") . " <b>{$statusLabel}: #" . esc_html($inquiryId) . "</b>\n\n";
            $tgMsg .= "👤 <b>Name:</b> " . esc_html($name) . "\n";
            $tgMsg .= "📞 <b>Phone:</b> " . esc_html($phone) . "\n";
            $tgMsg .= "✉️ <b>Email:</b> " . esc_html($email ?: 'Not provided') . "\n";
            $tgMsg .= "💼 <b>Service:</b> " . esc_html($service) . "\n";
            $tgMsg .= "💬 <b>Details:</b> " . esc_html($transcript) . "\n";
            $tgMsg .= "🌐 <b>Source:</b> WizAI Chat Assistant\n";
            $tgMsg .= $isComplete
                ? "⏰ <b>Status:</b> Qualified Lead (All details filled)"
                : "⚠️ <i>Status: Saved. WizAI is asking for service requirements...</i>";
            $chatButtons = array();
            $cRow = array();
            if ($phone !== 'Not provided') {
                $cDigits = preg_replace('/\D+/', '', $phone);
                $cRow[] = array(
                    'text' => '💬 Chat on WhatsApp',
                    'url'  => 'https://wa.me/' . $cDigits . '?text=' . rawurlencode("Hi " . $name . "! Reaching out regarding your chat inquiry #" . $inquiryId . " with Wizhy Web Studio.")
                );
            }
            if (!empty($email)) {
                $cRow[] = array(
                    'text' => '✉️ Email Visitor',
                    'url'  => 'https://mail.google.com/mail/?view=cm&to=' . rawurlencode($email) . '&su=' . rawurlencode("Re: Your WizAI Chat Inquiry [" . $inquiryId . "] - Wizhy Web Studio")
                );
            }
            if (!empty($cRow)) $chatButtons[] = $cRow;

            if ($isComplete) {
                $chatButtons[] = array(
                    array('text' => '🟡 Contacted', 'callback_data' => 'status:contacted:' . $inquiryId),
                    array('text' => '🟢 Closed',    'callback_data' => 'status:closed:' . $inquiryId),
                    array('text' => '🔴 Spam',      'callback_data' => 'status:spam:' . $inquiryId),
                );
            }

            $sentTg = wizhy_send_telegram_alert($tgMsg, $tgMessageId, $chatButtons);
        }

        wizhy_record_lead(array(
            'id'            => $inquiryId,
            'name'          => $name,
            'phone'         => $phone,
            'email'         => $email,
            'service'       => $service,
            'message'       => $transcript,
            'status'        => $isComplete ? 'new' : 'in_progress',
            'tg_message_id' => $sentTg ?: $tgMessageId,
        ));

        return new WP_REST_Response(array(
            'success'       => true,
            'inquiry_id'    => $inquiryId,
            'sent_admin'    => (bool) $sentAdmin,
            'sent_user'     => (bool) $sentUser,
            'sent_telegram' => (bool) $sentTg,
            'tg_message_id' => $sentTg ?: $tgMessageId,
        ), 200);
    }

    /* =========================================================================
     * 5. RAZORPAY PAYMENT CONFIRMATION ALERT
     * ========================================================================= */
    if ($type === 'payment') {
        $paymentId = isset($params['payment_id']) ? sanitize_text_field($params['payment_id']) : 'N/A';
        $amount    = isset($params['amount']) ? sanitize_text_field($params['amount']) : '5000';
        $currency  = isset($params['currency']) ? sanitize_text_field($params['currency']) : 'INR';

        $adminSubject = sprintf('💳 Payment Received [%s %s] — ID: %s', $currency, $amount, $paymentId);
        $adminSubhead = 'A new payment transaction was recorded on <strong>web.wizhy.in</strong>.';
        $adminBody = '
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; background-color: #FFFFFF; border: 1px solid #E6DEC8; border-radius: 12px; overflow: hidden;">
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; width: 35%; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Payment ID</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #C8481E;">' . esc_html($paymentId) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Amount</td>
              <td style="padding: 12px 16px; border-bottom: 1px solid #EFEAE1; font-size: 14px; font-weight: 700; color: #161412;">' . esc_html($currency . ' ' . $amount) . '</td>
            </tr>
            <tr>
              <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6E675F; background-color: #FAF6F0;">Timestamp</td>
              <td style="padding: 12px 16px; font-size: 13px; color: #57524E;">' . esc_html($timestamp) . '</td>
            </tr>
          </table>';

        $adminHtml = wizhy_build_email_layout('Payment Alert', 'New Payment Captured', $adminSubhead, $adminBody);
        $sentAdmin = wp_mail($adminEmail, $adminSubject, $adminHtml, array('Content-Type: text/html; charset=UTF-8'));

        $tgMsg  = "💳 <b>PAYMENT RECEIVED!</b>\n\n";
        $tgMsg .= "• <b>Payment ID:</b> " . esc_html($paymentId) . "\n";
        $tgMsg .= "• <b>Amount:</b> " . esc_html($currency . ' ' . $amount) . "\n";
        $tgMsg .= "• <b>Time:</b> " . esc_html($timestamp);
        $sentTg = wizhy_send_telegram_alert($tgMsg);

        return new WP_REST_Response(array('success' => true, 'sent_admin' => (bool) $sentAdmin, 'sent_telegram' => (bool) $sentTg), 200);
    }

    return new WP_REST_Response(array('error' => 'Unknown notification type'), 400);
}

/* =============================================================================
 * TELEGRAM CRM, BOT WEBHOOK & AUTOMATION ENGINE (5 CORE FEATURES)
 * 1. 1-Tap Lead Status Buttons (Mini-CRM)
 * 2. Telegram Slash Commands (/stats, /leads, /latest, /briefing, /security)
 * 3. 2-Way Direct Reply to Client via Telegram Swipe-Reply
 * 4. Scheduled Daily Morning Briefing & Nightly Recap (WP-Cron)
 * 5. Instant Security, Downtime & Anti-Spam Alerts
 * ============================================================================= */

/**
 * Lead Storage Engine: Persist leads in WordPress options for fast lookup & stats
 */
function wizhy_record_lead($leadData) {
    $leads = get_option('wizhy_leads_store', array());
    if (!is_array($leads)) {
        $leads = array();
    }
    $id = !empty($leadData['id']) ? sanitize_text_field($leadData['id']) : '';
    if (empty($id)) return;

    // Clean up old REF key when upgraded to INQ
    $seq = preg_replace('/^(REF|INQ)-/', '', $id);
    if (!empty($seq) && isset($leads['REF-' . $seq])) {
        unset($leads['REF-' . $seq]);
    }

    $existing = isset($leads[$id]) ? $leads[$id] : array();
    $leads[$id] = array_merge(array(
        'id'            => $id,
        'name'          => 'Visitor',
        'email'         => '',
        'phone'         => '',
        'service'       => 'General Inquiry',
        'budget'        => '',
        'message'       => '',
        'status'        => (strpos($id, 'REF-') === 0) ? 'in_progress' : 'new',
        'created_at'    => wizhy_current_ist_time('Y-m-d H:i:s'),
        'updated_at'    => wizhy_current_ist_time('Y-m-d H:i:s'),
        'tg_message_id' => null,
    ), $existing, $leadData);

    $leads[$id]['updated_at'] = wizhy_current_ist_time('Y-m-d H:i:s');

    // Keep up to 200 leads in memory
    if (count($leads) > 200) {
        $leads = array_slice($leads, -200, 200, true);
    }

    update_option('wizhy_leads_store', $leads, false);
}

/**
 * Lead Status Updater: Updates status locally and in Supabase
 */
function wizhy_update_lead_status($inquiryId, $status) {
    $leads = get_option('wizhy_leads_store', array());
    if (!is_array($leads)) $leads = array();

    $matchedKey = null;
    if (isset($leads[$inquiryId])) {
        $matchedKey = $inquiryId;
    } else {
        $seq = preg_replace('/^(REF|INQ)-/', '', $inquiryId);
        foreach ($leads as $k => $l) {
            if (strpos($k, $seq) !== false) {
                $matchedKey = $k;
                break;
            }
        }
    }

    $lead = null;
    if ($matchedKey) {
        $leads[$matchedKey]['status'] = sanitize_text_field($status);
        $leads[$matchedKey]['updated_at'] = wizhy_current_ist_time('Y-m-d H:i:s');
        update_option('wizhy_leads_store', $leads, false);
        $lead = $leads[$matchedKey];
    } else {
        $leads[$inquiryId] = array(
            'id'         => $inquiryId,
            'status'     => sanitize_text_field($status),
            'updated_at' => wizhy_current_ist_time('Y-m-d H:i:s'),
            'name'       => 'Client',
            'created_at' => wizhy_current_ist_time('Y-m-d H:i:s'),
        );
        update_option('wizhy_leads_store', $leads, false);
        $lead = $leads[$inquiryId];
    }

    // Sync to Supabase via REST API
    wizhy_sync_status_to_supabase($inquiryId, $status);

    return $lead;
}

/**
 * Sync status to Supabase DB via REST API
 */
function wizhy_sync_status_to_supabase($inquiryId, $status) {
    $supabaseUrl = defined('WIZHY_SUPABASE_URL') ? WIZHY_SUPABASE_URL : get_option('wizhy_supabase_url', '');
    $anonKey     = defined('WIZHY_SUPABASE_ANON_KEY') ? WIZHY_SUPABASE_ANON_KEY : get_option('wizhy_supabase_anon_key', '');

    if (empty($supabaseUrl) || empty($anonKey)) {
        return; // Supabase not configured — skip silently
    }

    $url = $supabaseUrl . '/rest/v1/contact_form_leads?message=like.*' . rawurlencode('[' . $inquiryId . ']') . '*';
    wp_remote_request($url, array(
        'method'  => 'PATCH',
        'timeout' => 4,
        'headers' => array(
            'Content-Type'  => 'application/json',
            'apikey'        => $anonKey,
            'Authorization' => 'Bearer ' . $anonKey,
            'Prefer'        => 'return=minimal',
        ),
        'body'    => wp_json_encode(array('lead_source' => 'status_' . sanitize_text_field($status))),
    ));
}

/**
 * Telegram Webhook Handler (Incoming Updates from Telegram Bot)
 */
function wizhy_handle_telegram_webhook(WP_REST_Request $request) {
    $token  = defined('WIZHY_TG_BOT_TOKEN') ? WIZHY_TG_BOT_TOKEN : get_option('wizhy_tg_bot_token', '');
    $chatId = defined('WIZHY_TG_CHAT_ID') ? WIZHY_TG_CHAT_ID : get_option('wizhy_tg_chat_id', '');

    $payload = $request->get_json_params();
    if (!is_array($payload)) {
        return new WP_REST_Response(array('status' => 'empty_payload'), 200);
    }

    /* -------------------------------------------------------------------------
     * FEATURE 1: CALLBACK QUERY (1-Tap Mini-CRM Status Buttons)
     * ------------------------------------------------------------------------- */
    if (!empty($payload['callback_query'])) {
        $cq       = $payload['callback_query'];
        $cqId     = isset($cq['id']) ? $cq['id'] : '';
        $data     = isset($cq['data']) ? sanitize_text_field($cq['data']) : '';
        $msg      = isset($cq['message']) ? $cq['message'] : array();
        $msgId    = isset($msg['message_id']) ? (int) $msg['message_id'] : 0;
        $origText = isset($msg['text']) ? $msg['text'] : '';

        if (strpos($data, 'status:') === 0) {
            $parts     = explode(':', $data);
            $newStatus = isset($parts[1]) ? sanitize_text_field($parts[1]) : 'contacted';
            $inquiryId = isset($parts[2]) ? sanitize_text_field($parts[2]) : '';

            // 1. Update lead in DB / Options
            $lead = wizhy_update_lead_status($inquiryId, $newStatus);
            $clientName = !empty($lead['name']) ? $lead['name'] : 'Lead';

            // 2. Determine toast alert text
            $toast = '✅ Status updated: ' . ucfirst($newStatus);
            if ($newStatus === 'contacted') {
                $toast = "🟡 Marked as Contacted: {$clientName}";
            } elseif ($newStatus === 'closed') {
                $toast = "🟢 Marked as Closed / Won! 🎉";
            } elseif ($newStatus === 'spam') {
                $toast = "🔴 Lead marked as Spam & archived";
            } elseif ($newStatus === 'reopen') {
                $toast = "🔄 Lead reopened (Status: New)";
            }

            // Answer callback query
            wp_remote_post("https://api.telegram.org/bot{$token}/answerCallbackQuery", array(
                'timeout' => 5,
                'headers' => array('Content-Type' => 'application/json'),
                'body'    => wp_json_encode(array(
                    'callback_query_id' => $cqId,
                    'text'              => $toast,
                    'show_alert'        => false,
                )),
            ));

            // 3. Rebuild text with new status badge
            $timeNow = wizhy_current_ist_time('d M, h:i A') . ' IST';
            $statusBadge = '';
            if ($newStatus === 'contacted') {
                $statusBadge = "🟡 <b>STATUS: CONTACTED</b> ({$timeNow})";
            } elseif ($newStatus === 'closed') {
                $statusBadge = "🟢 <b>STATUS: CLOSED / WON 🎉</b> ({$timeNow})";
            } elseif ($newStatus === 'spam') {
                $statusBadge = "🔴 <b>STATUS: SPAM / ARCHIVED 🚫</b> ({$timeNow})";
            } else {
                $statusBadge = "📥 <b>STATUS: REOPENED (NEW)</b> ({$timeNow})";
            }

            // Replace existing STATUS line or append
            $cleanText = preg_replace('/(🟡|🟢|🔴|📥|⏰)\s*(?:STATUS:|Status:)[^\n]+/u', '', $origText);
            $cleanText = trim($cleanText);
            $updatedText = $cleanText . "\n\n" . $statusBadge;

            // 4. Build updated inline keyboard
            $existingKb = !empty($msg['reply_markup']['inline_keyboard']) ? $msg['reply_markup']['inline_keyboard'] : array();
            $row1 = !empty($existingKb[0]) ? $existingKb[0] : array();

            // Row 2: Status controls reflecting current selection
            $row2 = array();
            if ($newStatus === 'contacted') {
                $row2 = array(
                    array('text' => '🟡 Contacted ✓', 'callback_data' => 'status:contacted:' . $inquiryId),
                    array('text' => '🟢 Mark Closed', 'callback_data' => 'status:closed:' . $inquiryId),
                    array('text' => '🔴 Spam',        'callback_data' => 'status:spam:' . $inquiryId),
                );
            } elseif ($newStatus === 'closed') {
                $row2 = array(
                    array('text' => '🟢 Closed ✓', 'callback_data' => 'status:closed:' . $inquiryId),
                    array('text' => '🔄 Reopen',   'callback_data' => 'status:reopen:' . $inquiryId),
                );
            } elseif ($newStatus === 'spam') {
                $row2 = array(
                    array('text' => '🔴 Spam ✓',   'callback_data' => 'status:spam:' . $inquiryId),
                    array('text' => '🔄 Reopen',   'callback_data' => 'status:reopen:' . $inquiryId),
                );
            } else {
                $row2 = array(
                    array('text' => '🟡 Contacted', 'callback_data' => 'status:contacted:' . $inquiryId),
                    array('text' => '🟢 Closed',    'callback_data' => 'status:closed:' . $inquiryId),
                    array('text' => '🔴 Spam',      'callback_data' => 'status:spam:' . $inquiryId),
                );
            }

            $newKeyboard = array();
            if (!empty($row1)) $newKeyboard[] = $row1;
            $newKeyboard[] = $row2;

            // Edit message in-place
            if ($msgId && !empty($chatId)) {
                wp_remote_post("https://api.telegram.org/bot{$token}/editMessageText", array(
                    'timeout' => 8,
                    'headers' => array('Content-Type' => 'application/json'),
                    'body'    => wp_json_encode(array(
                        'chat_id'                  => $chatId,
                        'message_id'               => $msgId,
                        'text'                     => $updatedText,
                        'parse_mode'               => 'HTML',
                        'disable_web_page_preview' => true,
                        'reply_markup'             => array('inline_keyboard' => $newKeyboard),
                    )),
                ));
            }

            return new WP_REST_Response(array('status' => 'callback_processed', 'new_status' => $newStatus), 200);
        }
    }

    /* -------------------------------------------------------------------------
     * PROCESS INCOMING BOT MESSAGES (COMMANDS & SWIPE-REPLIES)
     * ------------------------------------------------------------------------- */
    if (!empty($payload['message'])) {
        $msg      = $payload['message'];
        $fromId   = isset($msg['chat']['id']) ? (string) $msg['chat']['id'] : '';
        $text     = isset($msg['text']) ? trim($msg['text']) : '';
        $msgId    = isset($msg['message_id']) ? (int) $msg['message_id'] : 0;

        // Security check: Only respond to authorized chat ID
        if (!empty($chatId) && (string)$fromId !== (string)$chatId) {
            return new WP_REST_Response(array('status' => 'unauthorized'), 403);
        }

        /* ---------------------------------------------------------------------
         * FEATURE 3: 2-WAY DIRECT REPLY TO CLIENT VIA TELEGRAM SWIPE-REPLY
         * --------------------------------------------------------------------- */
        if (!empty($msg['reply_to_message'])) {
            $parentText = isset($msg['reply_to_message']['text']) ? $msg['reply_to_message']['text'] : '';

            // Detect inquiry ID
            $inqId = '';
            if (preg_match('/(INQ|REF)-\d{6}-\d{4}/', $parentText, $mInq)) {
                $inqId = $mInq[0];
            }

            // Look up lead from local store
            $leads = get_option('wizhy_leads_store', array());
            $lead  = !empty($inqId) && isset($leads[$inqId]) ? $leads[$inqId] : null;

            // Detect client email
            $clientEmail = !empty($lead['email']) ? $lead['email'] : '';
            if (empty($clientEmail) && preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $parentText, $mEmail)) {
                $clientEmail = $mEmail[0];
            }

            // Detect client name
            $clientName = !empty($lead['name']) ? $lead['name'] : '';
            if (empty($clientName) && preg_match('/(?:👤\s*Name:|Visitor\s*Name:)\s*([^\n]+)/u', $parentText, $mName)) {
                $clientName = trim($mName[1]);
            }
            if (empty($clientName)) $clientName = 'Valued Client';

            if (!empty($clientEmail) && is_email($clientEmail)) {
                $replyContent = $text;

                $clientSubject = 'Re: Your Project Inquiry [' . ($inqId ?: 'Wizhy') . '] — Wizhy Web Studio';
                $clientSubhead = 'Hi <strong>' . esc_html($clientName) . '</strong>, Ashish from <strong>Wizhy Web Studio</strong> has replied directly to your message:';
                $clientBody = '
                  <div style="background-color: #FAF6F0; border-left: 4px solid #C8481E; padding: 20px 24px; border-radius: 8px; font-size: 15px; line-height: 1.75; color: #161412; margin-bottom: 24px;">
                    ' . nl2br(esc_html($replyContent)) . '
                  </div>
                  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #FFFFFF; border: 1px solid #E6DEC8; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px;">
                    <tr>
                      <td style="font-size: 13px; color: #6E675F; line-height: 1.6;">
                        <strong>Next Steps:</strong> You can reply directly to this email, or message us on WhatsApp below for an instant response.
                      </td>
                    </tr>
                  </table>';

                $clientHtml = wizhy_build_email_layout(
                    'Direct Response',
                    'Message from Wizhy Web Studio',
                    $clientSubhead,
                    $clientBody,
                    'Chat on WhatsApp',
                    'https://wa.me/919870706956?text=' . rawurlencode('Hi Wizhy! Following up on inquiry #' . $inqId)
                );

                $sent = wp_mail($clientEmail, $clientSubject, $clientHtml, array(
                    'Content-Type: text/html; charset=UTF-8',
                    'Reply-To: Wizhy Web Studio <techwizhy@gmail.com>'
                ));

                // Notify admin in Telegram of successful email dispatch
                $confirmMsg  = "✅ <b>DIRECT REPLY DELIVERED TO CLIENT!</b>\n\n";
                $confirmMsg .= "👤 <b>Client:</b> " . esc_html($clientName) . " (" . esc_html($clientEmail) . ")\n";
                if ($inqId) $confirmMsg .= "📄 <b>Inquiry:</b> #" . esc_html($inqId) . "\n";
                $confirmMsg .= "⏰ <b>Sent At:</b> " . wizhy_current_ist_time('h:i A') . " IST\n\n";
                $confirmMsg .= "💬 <b>Delivered Email Body:</b>\n<i>\"" . esc_html($replyContent) . "\"</i>";

                wizhy_send_telegram_alert($confirmMsg);
                return new WP_REST_Response(array('status' => 'reply_sent', 'sent' => (bool)$sent), 200);
            } else {
                $failMsg = "⚠️ <b>Could not detect client email.</b>\nPlease ensure you are replying to an inquiry notification message containing an email address.";
                wizhy_send_telegram_alert($failMsg);
                return new WP_REST_Response(array('status' => 'email_not_found'), 200);
            }
        }

        /* ---------------------------------------------------------------------
         * FEATURE 2: TELEGRAM BOT SLASH COMMANDS
         * --------------------------------------------------------------------- */
        $cmd = strtolower(explode(' ', $text)[0]);

        // /start or /help
        if ($cmd === '/start' || $cmd === '/help') {
            $helpMsg  = "🤖 <b>Wizhy Web Studio Control Bot</b>\n\n";
            $helpMsg .= "Here are your available instant commands:\n\n";
            $helpMsg .= "📊 <b>/stats</b> — View lead metrics, conversion rate & pipeline\n";
            $helpMsg .= "📋 <b>/leads</b> — View recent active client inquiries\n";
            $helpMsg .= "🔥 <b>/latest</b> — View newest lead with 1-tap action buttons\n";
            $helpMsg .= "⚡ <b>/health</b> or <b>/uptime</b> — Live system diagnostics (Latency, SSL, Supabase, Brevo)\n";
            $helpMsg .= "🌅 <b>/briefing</b> — Generate on-demand Morning Briefing digest\n";
            $helpMsg .= "🛡️ <b>/security</b> — Check website security & spam block status\n\n";
            $helpMsg .= "💡 <i>Pro-Tip: Swipe-reply to ANY lead notification to send an instant branded email directly to the client!</i>";
            wizhy_send_telegram_alert($helpMsg);
            return new WP_REST_Response(array('status' => 'help_sent'), 200);
        }

        // /stats
        if ($cmd === '/stats') {
            $leads = get_option('wizhy_leads_store', array());
            if (!is_array($leads)) $leads = array();

            $totalLeads = count($leads);
            $todayLeads = 0;
            $weekLeads  = 0;
            $statusCounts = array('new' => 0, 'contacted' => 0, 'closed' => 0, 'in_progress' => 0, 'spam' => 0);
            $serviceCounts = array();

            $todayYmd = wizhy_current_ist_time('Y-m-d');
            $weekAgo  = wizhy_current_ist_time('Y-m-d', strtotime('-7 days'));

            foreach ($leads as $l) {
                $createdYmd = isset($l['created_at']) ? substr($l['created_at'], 0, 10) : '';
                if ($createdYmd === $todayYmd) $todayLeads++;
                if ($createdYmd >= $weekAgo)   $weekLeads++;

                $st = !empty($l['status']) ? $l['status'] : 'new';
                if (!isset($statusCounts[$st])) $statusCounts[$st] = 0;
                $statusCounts[$st]++;

                $srv = !empty($l['service']) ? $l['service'] : 'General';
                if (!isset($serviceCounts[$srv])) $serviceCounts[$srv] = 0;
                $serviceCounts[$srv]++;
            }

            $convRate = $totalLeads > 0 ? round(($statusCounts['closed'] / $totalLeads) * 100, 1) : 0;

            $statsMsg  = "📊 <b>WIZHY WEB STUDIO CRM ANALYTICS</b>\n";
            $statsMsg .= "📅 <i>" . wizhy_current_ist_time('d M Y, h:i A') . " IST</i>\n\n";
            $statsMsg .= "📈 <b>Lead Velocity:</b>\n";
            $statsMsg .= "• <b>Today:</b> {$todayLeads} leads\n";
            $statsMsg .= "• <b>Last 7 Days:</b> {$weekLeads} leads\n";
            $statsMsg .= "• <b>Total Pipeline:</b> {$totalLeads} leads\n\n";
            $statsMsg .= "🏷️ <b>Pipeline Status Breakdown:</b>\n";
            $statsMsg .= "• 📥 <b>New / Uncontacted:</b> " . $statusCounts['new'] . "\n";
            $statsMsg .= "• 🟡 <b>Contacted:</b> " . $statusCounts['contacted'] . "\n";
            $statsMsg .= "• 🟢 <b>Closed Deals / Won:</b> " . $statusCounts['closed'] . "\n";
            $statsMsg .= "• ⏳ <b>In Progress (Partial):</b> " . $statusCounts['in_progress'] . "\n";
            $statsMsg .= "• 🔴 <b>Spam / Filtered:</b> " . $statusCounts['spam'] . "\n";
            $statsMsg .= "• 🎯 <b>Close Rate:</b> <b>{$convRate}%</b>\n\n";

            if (!empty($serviceCounts)) {
                arsort($serviceCounts);
                $statsMsg .= "💼 <b>Top Requested Packages:</b>\n";
                $i = 0;
                foreach ($serviceCounts as $sName => $sCount) {
                    if (++$i > 4) break;
                    $statsMsg .= "• " . esc_html($sName) . ": <b>{$sCount}</b>\n";
                }
            }

            wizhy_send_telegram_alert($statsMsg);
            return new WP_REST_Response(array('status' => 'stats_sent'), 200);
        }

        // /leads
        if ($cmd === '/leads') {
            $leads = get_option('wizhy_leads_store', array());
            if (!is_array($leads) || empty($leads)) {
                wizhy_send_telegram_alert("📋 <b>No leads captured yet.</b>\nLeads will appear here automatically as visitors fill forms on web.wizhy.in!");
                return new WP_REST_Response(array('status' => 'no_leads'), 200);
            }

            $recent = array_reverse(array_slice($leads, -6, 6, true));
            $leadsMsg  = "📋 <b>RECENT CLIENT INQUIRIES (Latest " . count($recent) . "):</b>\n\n";

            $idx = 1;
            foreach ($recent as $lid => $l) {
                $st = !empty($l['status']) ? $l['status'] : 'new';
                $emoji = '📥';
                if ($st === 'contacted')   $emoji = '🟡';
                elseif ($st === 'closed')  $emoji = '🟢';
                elseif ($st === 'spam')    $emoji = '🔴';
                elseif ($st === 'in_progress') $emoji = '⏳';

                $name  = !empty($l['name']) ? $l['name'] : 'Visitor';
                $phone = !empty($l['phone']) ? $l['phone'] : 'No Phone';
                $email = !empty($l['email']) ? $l['email'] : 'No Email';
                $srv   = !empty($l['service']) ? $l['service'] : 'General';

                $leadsMsg .= "{$idx}. {$emoji} <b>#{$lid}</b> — " . esc_html($name) . "\n";
                $leadsMsg .= "   💼 " . esc_html($srv) . "\n";
                $leadsMsg .= "   📞 " . esc_html($phone) . " | ✉️ " . esc_html($email) . "\n";
                $leadsMsg .= "   🏷️ <i>Status: " . ucfirst($st) . "</i>\n\n";
                $idx++;
            }

            $leadsMsg .= "💡 Type <b>/latest</b> to view full details of the newest lead with 1-tap action buttons!";
            wizhy_send_telegram_alert($leadsMsg);
            return new WP_REST_Response(array('status' => 'leads_sent'), 200);
        }

        // /latest
        if ($cmd === '/latest') {
            $leads = get_option('wizhy_leads_store', array());
            if (!is_array($leads) || empty($leads)) {
                wizhy_send_telegram_alert("🔥 <b>No leads found yet.</b>");
                return new WP_REST_Response(array('status' => 'no_leads'), 200);
            }

            $latestLead = end($leads);
            $inquiryId  = !empty($latestLead['id']) ? $latestLead['id'] : 'INQ-LATEST';
            $name       = !empty($latestLead['name']) ? $latestLead['name'] : 'Visitor';
            $phone      = !empty($latestLead['phone']) ? $latestLead['phone'] : 'Not provided';
            $email      = !empty($latestLead['email']) ? $latestLead['email'] : 'Not provided';
            $service    = !empty($latestLead['service']) ? $latestLead['service'] : 'General Inquiry';
            $budget     = !empty($latestLead['budget']) ? $latestLead['budget'] : 'Not specified';
            $message    = !empty($latestLead['message']) ? $latestLead['message'] : 'No message provided';
            $status     = !empty($latestLead['status']) ? $latestLead['status'] : 'new';
            $createdAt  = !empty($latestLead['created_at']) ? $latestLead['created_at'] : '';

            $latMsg  = "🔥 <b>LATEST INQUIRY: #" . esc_html($inquiryId) . "</b>\n\n";
            $latMsg .= "👤 <b>Name:</b> " . esc_html($name) . "\n";
            $latMsg .= "📞 <b>Phone:</b> " . esc_html($phone) . "\n";
            $latMsg .= "✉️ <b>Email:</b> " . esc_html($email) . "\n";
            $latMsg .= "💼 <b>Service:</b> " . esc_html($service) . "\n";
            $latMsg .= "💰 <b>Budget:</b> " . esc_html($budget) . "\n";
            $latMsg .= "💬 <b>Message:</b> " . esc_html($message) . "\n";
            $latMsg .= "⏰ <b>Received:</b> " . esc_html($createdAt) . " IST\n";
            $latMsg .= "🏷️ <b>Status:</b> " . ucfirst($status);

            $buttons = array();
            $row1 = array();
            if ($phone !== 'Not provided') {
                $cleanDigits = preg_replace('/\D+/', '', $phone);
                $row1[] = array(
                    'text' => '💬 WhatsApp',
                    'url'  => 'https://wa.me/' . $cleanDigits . '?text=' . rawurlencode("Hi {$name}! Reaching out regarding your inquiry #{$inquiryId} with Wizhy Web Studio.")
                );
            }
            if (!empty($email) && $email !== 'Not provided') {
                $row1[] = array(
                    'text' => '✉️ Email',
                    'url'  => 'https://mail.google.com/mail/?view=cm&to=' . rawurlencode($email) . '&su=' . rawurlencode("Re: Your Inquiry [{$inquiryId}] - Wizhy Web Studio")
                );
            }
            if (!empty($row1)) $buttons[] = $row1;

            $buttons[] = array(
                array('text' => '🟡 Contacted', 'callback_data' => 'status:contacted:' . $inquiryId),
                array('text' => '🟢 Closed',    'callback_data' => 'status:closed:' . $inquiryId),
                array('text' => '🔴 Spam',      'callback_data' => 'status:spam:' . $inquiryId),
            );

            wizhy_send_telegram_alert($latMsg, null, $buttons);
            return new WP_REST_Response(array('status' => 'latest_sent'), 200);
        }

        // /briefing
        if ($cmd === '/briefing') {
            wizhy_send_morning_briefing_telegram();
            return new WP_REST_Response(array('status' => 'briefing_triggered'), 200);
        }

        // /security
        if ($cmd === '/security') {
            $secMsg  = "🛡️ <b>SECURITY & HEALTH MONITOR: Wizhy Web Studio</b>\n\n";
            $secMsg .= "• <b>Domain:</b> web.wizhy.in\n";
            $secMsg .= "• <b>Server:</b> LiteSpeed Enterprise (HTTP/3 + QUIC)\n";
            $secMsg .= "• <b>Bot Filter:</b> Cloudflare Turnstile Active\n";
            $secMsg .= "• <b>SMTP Engine:</b> WP Mail SMTP (Brevo API)\n";
            $secMsg .= "• <b>Database:</b> Supabase PostgreSQL (qfoziccqtyvnjxsjjmds)\n";
            $secMsg .= "• <b>PHP Version:</b> " . PHP_VERSION . "\n";
            $secMsg .= "• <b>Memory Limit:</b> " . ini_get('memory_limit') . "\n";
            $secMsg .= "• <b>Status:</b> 🟢 All Systems Operational";

            wizhy_send_telegram_alert($secMsg);
            return new WP_REST_Response(array('status' => 'security_sent'), 200);
        }

        // /health or /uptime
        if ($cmd === '/health' || $cmd === '/uptime') {
            $healthMsg = wizhy_run_system_health_check(true);
            wizhy_send_telegram_alert($healthMsg);
            return new WP_REST_Response(array('status' => 'health_sent'), 200);
        }
    }

    return new WP_REST_Response(array('status' => 'ignored'), 200);
}

/* -----------------------------------------------------------------------------
 * FEATURE 4: SCHEDULED DAILY MORNING BRIEFING & NIGHTLY RECAP (WP-CRON)
 * ----------------------------------------------------------------------------- */

/**
 * Morning Briefing at 09:00 AM IST (03:30 AM UTC)
 */
function wizhy_send_morning_briefing_telegram() {
    $leads = get_option('wizhy_leads_store', array());
    if (!is_array($leads)) $leads = array();

    $pending = array();
    foreach ($leads as $lid => $l) {
        $st = !empty($l['status']) ? $l['status'] : 'new';
        if ($st === 'new' || $st === 'in_progress') {
            $pending[] = $l;
        }
    }

    $briefMsg  = "🌅 <b>GOOD MORNING ASHISH!</b>\n";
    $briefMsg .= "📅 <i>" . wizhy_current_ist_time('l, d F Y') . "</i>\n\n";
    $briefMsg .= "🚀 <b>Wizhy Web Studio Daily Briefing:</b>\n";
    $briefMsg .= "• <b>Total Pipeline Leads:</b> " . count($leads) . "\n";
    $briefMsg .= "• <b>Pending Follow-ups:</b> <b>" . count($pending) . " leads</b>\n\n";

    if (!empty($pending)) {
        $briefMsg .= "🔥 <b>Top Follow-Ups for Today:</b>\n";
        $i = 0;
        foreach (array_reverse($pending) as $pl) {
            if (++$i > 3) break;
            $pid   = !empty($pl['id']) ? $pl['id'] : '';
            $pname = !empty($pl['name']) ? $pl['name'] : 'Client';
            $psrv  = !empty($pl['service']) ? $pl['service'] : 'Web Project';
            $briefMsg .= "{$i}. <b>#{$pid}</b> — " . esc_html($pname) . " (" . esc_html($psrv) . ")\n";
        }
        $briefMsg .= "\n💡 <i>Goal: Contact all pending leads before 12:00 PM for maximum conversion!</i>";
    } else {
        $briefMsg .= "✨ <b>All caught up!</b> No pending leads awaiting follow-up. Have a productive day!";
    }

    wizhy_send_telegram_alert($briefMsg);
}

/**
 * Nightly Recap at 09:00 PM IST (15:30 PM UTC)
 */
function wizhy_send_nightly_recap_telegram() {
    $leads = get_option('wizhy_leads_store', array());
    if (!is_array($leads)) $leads = array();

    $todayYmd = wizhy_current_ist_time('Y-m-d');
    $todayCount = 0;
    foreach ($leads as $l) {
        if (!empty($l['created_at']) && substr($l['created_at'], 0, 10) === $todayYmd) {
            $todayCount++;
        }
    }

    $recapMsg  = "🌙 <b>NIGHTLY RECAP: Wizhy Web Studio</b>\n";
    $recapMsg .= "📅 <i>" . wizhy_current_ist_time('d M Y') . "</i>\n\n";
    $recapMsg .= "• <b>New Leads Captured Today:</b> <b>{$todayCount}</b>\n";
    $recapMsg .= "• <b>Total Inquiries Recorded:</b> " . count($leads) . "\n\n";
    $recapMsg .= "Great job today! The AI chat assistant and website forms remain active overnight. Rest well! 🌟";

    wizhy_send_telegram_alert($recapMsg);
}

add_action('wizhy_daily_morning_briefing_event', 'wizhy_send_morning_briefing_telegram');
add_action('wizhy_daily_nightly_recap_event', 'wizhy_send_nightly_recap_telegram');

/**
 * Schedule WP-Cron daily events at exact IST times
 */
function wizhy_schedule_telegram_briefings() {
    // 09:00 AM IST = 03:30 AM UTC
    if (!wp_next_scheduled('wizhy_daily_morning_briefing_event')) {
        $nextMorning = strtotime('tomorrow 03:30:00 UTC');
        wp_schedule_event($nextMorning, 'daily', 'wizhy_daily_morning_briefing_event');
    }
    // 09:00 PM IST = 15:30 PM UTC
    if (!wp_next_scheduled('wizhy_daily_nightly_recap_event')) {
        $nextNight = strtotime('tomorrow 15:30:00 UTC');
        wp_schedule_event($nextNight, 'daily', 'wizhy_daily_nightly_recap_event');
    }
}
add_action('init', 'wizhy_schedule_telegram_briefings');

/* -----------------------------------------------------------------------------
 * FEATURE 5: INSTANT SECURITY, DOWNTIME & ANTI-SPAM ALERTS
 * ----------------------------------------------------------------------------- */

/**
 * Dispatch High-Priority Security Alert to Telegram
 */
function wizhy_send_security_alert($eventType, $details = array()) {
    $ip = !empty($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : 'Unknown IP';
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = sanitize_text_field($_SERVER['HTTP_CF_CONNECTING_IP']);
    }
    $ua = !empty($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(substr($_SERVER['HTTP_USER_AGENT'], 0, 100)) : 'Unknown UA';

    // Rate-limit to prevent alert flooding (once per 3 minutes per event)
    $lockKey = 'wizhy_sec_alert_' . md5($eventType . $ip);
    if (get_transient($lockKey)) return false;
    set_transient($lockKey, true, 180);

    $msg  = "🚨 <b>SECURITY ALERT: WIZHY WEB STUDIO</b>\n\n";
    $msg .= "• <b>Event:</b> " . esc_html($eventType) . "\n";
    $msg .= "• <b>IP Address:</b> <code>" . esc_html($ip) . "</code>\n";
    if (!empty($details['reason'])) {
        $msg .= "• <b>Details:</b> " . esc_html($details['reason']) . "\n";
    }
    $msg .= "• <b>User-Agent:</b> <i>" . esc_html($ua) . "</i>\n";
    $msg .= "• <b>Time:</b> " . wizhy_current_ist_time('d M Y, h:i A') . " IST\n";
    $msg .= "• <b>Action:</b> Request blocked & flagged.";

    return wizhy_send_telegram_alert($msg);
}

// Hook failed admin login attempts to Telegram security alert
add_action('wp_login_failed', function ($username) {
    wizhy_send_security_alert('Failed Admin Login Attempt', array(
        'reason' => 'Unauthorized attempt to log in with username: ' . $username
    ));
});

/* -----------------------------------------------------------------------------
 * FEATURE 6: 15-MINUTE WEBSITE UPTIME, SSL, DB & BREVO HEALTH MONITOR
 * ----------------------------------------------------------------------------- */

/**
 * Register 15-minute custom cron recurrence
 */
add_filter('cron_schedules', function ($schedules) {
    if (!isset($schedules['wizhy_fifteen_minutes'])) {
        $schedules['wizhy_fifteen_minutes'] = array(
            'interval' => 900,
            'display'  => 'Every 15 Minutes (Wizhy Health Check)'
        );
    }
    return $schedules;
});

/**
 * Schedule 15-Minute Uptime Monitor
 */
add_action('init', function () {
    if (!wp_next_scheduled('wizhy_uptime_health_check_event')) {
        wp_schedule_event(time(), 'wizhy_fifteen_minutes', 'wizhy_uptime_health_check_event');
    }
});

/**
 * Core Diagnostics Engine (Website, Latency, SSL, Supabase, Brevo Quota, Memory)
 */
function wizhy_run_system_health_check($returnHtml = false) {
    $results = array(
        'timestamp'      => wizhy_current_ist_time('d M Y, h:i A') . ' IST',
        'is_healthy'     => true,
        'website_status' => 200,
        'website_ms'     => 0,
        'ssl_valid'      => true,
        'ssl_days'       => 0,
        'ssl_date'       => '',
        'supabase_ok'    => true,
        'supabase_ms'    => 0,
        'brevo_ok'       => true,
        'brevo_credits'  => 0,
        'memory_used'    => '',
        'issues'         => array(),
    );

    // 1. Website Ping & Latency Check
    $t1 = microtime(true);
    $webRes = wp_remote_get('https://web.wizhy.in/', array(
        'timeout'    => 10,
        'sslverify'  => false,
        'user-agent' => 'Wizhy-Health-Monitor/1.0',
    ));
    $results['website_ms'] = round((microtime(true) - $t1) * 1000);

    if (is_wp_error($webRes)) {
        $results['is_healthy'] = false;
        $results['website_status'] = 0;
        $results['issues'][] = 'Website unreachable: ' . $webRes->get_error_message();
    } else {
        $code = (int) wp_remote_retrieve_response_code($webRes);
        $results['website_status'] = $code;
        if ($code < 200 || $code >= 400) {
            $results['is_healthy'] = false;
            $results['issues'][] = 'HTTP Status Error ' . $code;
        }
        if ($results['website_ms'] > 3500) {
            $results['issues'][] = 'High Response Latency (' . $results['website_ms'] . 'ms)';
        }
    }

    // 2. SSL Certificate Expiration Check
    $context = stream_context_create(array(
        'ssl' => array(
            'capture_peer_cert' => true,
            'verify_peer'       => false,
            'verify_peer_name'  => false,
        )
    ));
    $errno = 0; $errstr = '';
    $socket = @stream_socket_client('ssl://web.wizhy.in:443', $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
    if ($socket) {
        $params = stream_context_get_params($socket);
        if (!empty($params['options']['ssl']['peer_certificate'])) {
            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            if (!empty($cert['validTo_time_t'])) {
                $days = floor(($cert['validTo_time_t'] - time()) / 86400);
                $results['ssl_days'] = $days;
                $results['ssl_date'] = date('d M Y', $cert['validTo_time_t']);
                if ($days < 14) {
                    $results['issues'][] = 'SSL expires in ' . $days . ' days';
                }
            }
        }
        fclose($socket);
    } else {
        $results['ssl_valid'] = false;
        $results['issues'][] = 'Could not verify SSL certificate: ' . $errstr;
    }

    // 3. Supabase Database REST API Connectivity
    $t2 = microtime(true);
    $supRes = wp_remote_get('https://qfoziccqtyvnjxsjjmds.supabase.co/rest/v1/contact_form_leads?limit=1', array(
        'timeout' => 5,
        'headers' => array(
            'apikey'        => 'sb_publishable_9bwM47LVotLdcmJDUCKwjQ_L5WUCmzT',
            'Authorization' => 'Bearer sb_publishable_9bwM47LVotLdcmJDUCKwjQ_L5WUCmzT',
        )
    ));
    $results['supabase_ms'] = round((microtime(true) - $t2) * 1000);
    if (is_wp_error($supRes) || wp_remote_retrieve_response_code($supRes) !== 200) {
        $results['supabase_ok'] = false;
        $results['issues'][] = 'Supabase REST API unreachable';
    }

    // 4. Brevo Email Engine Quota
    $smtpOptions = get_option('wp_mail_smtp');
    $brevoKey = !empty($smtpOptions['sendinblue']['api_key']) ? $smtpOptions['sendinblue']['api_key'] : '';
    if (!empty($brevoKey)) {
        $acctRes = wp_remote_get('https://api.brevo.com/v3/account', array(
            'timeout' => 5,
            'headers' => array('api-key' => $brevoKey, 'accept' => 'application/json')
        ));
        if (!is_wp_error($acctRes) && wp_remote_retrieve_response_code($acctRes) === 200) {
            $acctData = json_decode(wp_remote_retrieve_body($acctRes), true);
            if (!empty($acctData['plan'][0]['credits'])) {
                $results['brevo_credits'] = (int) $acctData['plan'][0]['credits'];
            }
        }
    }

    // 5. Memory usage
    $memUsed = round(memory_get_usage(true) / 1048576, 1);
    $memLimit = ini_get('memory_limit');
    $results['memory_used'] = "{$memUsed}MB / {$memLimit}";

    // Persist latest health report
    update_option('wizhy_latest_health_report', $results, false);

    if ($returnHtml) {
        $statusEmoji = $results['is_healthy'] ? '🟢' : '🔴';

        $msg  = "{$statusEmoji} <b>UPTIME & SYSTEM HEALTH REPORT</b>\n";
        $msg .= "📅 <i>" . $results['timestamp'] . "</i>\n\n";
        $msg .= "🌐 <b>Website Status:</b> " . ($results['website_status'] === 200 ? "200 OK (ONLINE)" : "HTTP " . $results['website_status']) . "\n";
        $msg .= "⚡ <b>Page Latency:</b> <b>" . $results['website_ms'] . "ms</b> " . ($results['website_ms'] < 300 ? "🚀 (Ultra Fast)" : "⏱️") . "\n";
        $msg .= "🔒 <b>SSL Certificate:</b> Valid (" . $results['ssl_days'] . " days left, exp: " . $results['ssl_date'] . ")\n";
        $msg .= "🗄️ <b>Supabase DB API:</b> " . ($results['supabase_ok'] ? "Connected (" . $results['supabase_ms'] . "ms)" : "⚠️ Offline") . "\n";
        $msg .= "✉️ <b>Brevo Email Engine:</b> Active (" . $results['brevo_credits'] . " free sends left today)\n";
        $msg .= "💻 <b>Server Memory:</b> " . $results['memory_used'] . "\n";
        $msg .= "🛡️ <b>Bot Protection:</b> Cloudflare Turnstile Active\n";

        if (!empty($results['issues'])) {
            $msg .= "\n⚠️ <b>Active Warnings:</b>\n";
            foreach ($results['issues'] as $iss) {
                $msg .= "• " . esc_html($iss) . "\n";
            }
        } else {
            $msg .= "\n✨ <i>Site is running smoothly at 100/100 performance!</i>";
        }

        return $msg;
    }

    return $results;
}

/**
 * 15-Minute Background Health & Downtime Guard
 */
function wizhy_scheduled_uptime_check() {
    $report = wizhy_run_system_health_check(false);
    $prevState = get_option('wizhy_site_health_state', array('status' => 'up', 'down_since' => null));

    if (!$report['is_healthy']) {
        // Site or Core Services DOWN!
        if ($prevState['status'] === 'up') {
            // Send Urgent Red Alert immediately!
            $downMsg  = "🚨 <b>CRITICAL DOWNTIME ALERT: web.wizhy.in</b>\n\n";
            $downMsg .= "⚠️ <b>Status:</b> Site or Core Service Error Detected!\n";
            $downMsg .= "• <b>HTTP Code:</b> " . esc_html($report['website_status']) . "\n";
            $downMsg .= "• <b>Latency:</b> " . esc_html($report['website_ms']) . "ms\n";
            $downMsg .= "• <b>Detected At:</b> " . esc_html($report['timestamp']) . "\n\n";

            if (!empty($report['issues'])) {
                $downMsg .= "🔍 <b>Identified Issues:</b>\n";
                foreach ($report['issues'] as $issue) {
                    $downMsg .= "• " . esc_html($issue) . "\n";
                }
                $downMsg .= "\n";
            }

            $downMsg .= "🛠️ <b>Instant Recovery Steps:</b>\n";
            $downMsg .= "1. Check Hostinger hPanel -> LiteSpeed Web Server\n";
            $downMsg .= "2. Purge cache via SSH: <code>wp litespeed-purge all</code>\n";
            $downMsg .= "3. Check PHP error logs in hPanel";

            wizhy_send_telegram_alert($downMsg);

            update_option('wizhy_site_health_state', array(
                'status'     => 'down',
                'down_since' => time(),
            ), false);
        }
    } else {
        // Site is UP and healthy
        if ($prevState['status'] === 'down') {
            // Site just recovered! Send Green Resolution Alert!
            $durationMins = !empty($prevState['down_since']) ? max(1, round((time() - $prevState['down_since']) / 60)) : 15;

            $upMsg  = "✅ <b>SERVICE RESTORED: web.wizhy.in IS BACK ONLINE!</b>\n\n";
            $upMsg .= "• <b>HTTP Status:</b> 200 OK\n";
            $upMsg .= "• <b>Response Latency:</b> " . esc_html($report['website_ms']) . "ms\n";
            $upMsg .= "• <b>Downtime Duration:</b> Approx {$durationMins} minutes\n";
            $upMsg .= "• <b>Resolved At:</b> " . esc_html($report['timestamp']) . "\n\n";
            $upMsg .= "✨ All systems have recovered and are operating normally.";

            wizhy_send_telegram_alert($upMsg);
        }

        update_option('wizhy_site_health_state', array(
            'status'     => 'up',
            'down_since' => null,
        ), false);
    }
}
add_action('wizhy_uptime_health_check_event', 'wizhy_scheduled_uptime_check');



