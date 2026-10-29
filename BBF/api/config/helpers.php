<?php
/**
 * BBF — Shared API helpers
 */

require_once __DIR__ . '/config.php';

// ── CORS & JSON headers ───────────────────────────────────────────────────────
function bbf_headers(): void {
    header('Content-Type: application/json; charset=utf-8');
    // Allow any localhost origin (works for both http://localhost and http://localhost/BBF)
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

// ── JSON response helpers ─────────────────────────────────────────────────────
function bbf_json(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function bbf_success(array $data = [], string $message = 'Success'): void {
    bbf_json(['success' => true, 'message' => $message, 'data' => $data]);
}

function bbf_error(int $code, string $message, array $extra = []): void {
    bbf_json(array_merge(['success' => false, 'message' => $message], $extra), $code);
}

// ── Request helpers ───────────────────────────────────────────────────────────
function bbf_body(): array {
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function bbf_method(string $expected): void {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($expected)) {
        bbf_error(405, 'Method not allowed.');
    }
}

function bbf_require(array $data, array $fields): void {
    foreach ($fields as $f) {
        if (empty($data[$f])) {
            bbf_error(422, "Field '{$f}' is required.");
        }
    }
}

// ── Sanitize ──────────────────────────────────────────────────────────────────
function bbf_clean(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function bbf_email(string $email): string|false {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}

// ── Member ID generator ───────────────────────────────────────────────────────
function bbf_generate_member_id(): string {
    $pdo = bbf_db();
    $pdo->beginTransaction();
    try {
        $pdo->exec("UPDATE member_id_seq SET last_id = last_id + 1");
        $row = $pdo->query("SELECT last_id FROM member_id_seq")->fetch();
        $pdo->commit();
        return 'BBF-' . str_pad($row['last_id'], 4, '0', STR_PAD_LEFT);
    } catch (Exception $e) {
        $pdo->rollBack();
        bbf_error(500, 'Could not generate member ID.');
    }
}

// ── PHPMailer wrapper ─────────────────────────────────────────────────────────
function bbf_mail(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
    // Skip entirely if no password set — avoids SMTP timeout delays
    if (MAIL_PASSWORD === '') {
        error_log('BBF Mail skipped — MAIL_PASSWORD not set. To: ' . $toEmail . ' Subject: ' . $subject);
        return false;
    }

    $mailerPath = __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    if (file_exists($mailerPath)) {
        require_once $mailerPath;
        require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
        require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = MAIL_PORT;
            $mail->Timeout    = 5;   // fail fast — 5 seconds max

            $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            $mail->addReplyTo(MAIL_REPLY_TO, MAIL_FROM_NAME);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = bbf_email_wrap($subject, $htmlBody);
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('BBF Mail Error: ' . $e->getMessage());
            return false;
        }
    } else {
        // Fallback: PHP mail()
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
        return mail($toEmail, $subject, bbf_email_wrap($subject, $htmlBody), $headers);
    }
}

// ── Email HTML wrapper ────────────────────────────────────────────────────────
function bbf_email_wrap(string $title, string $body): string {
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{$title}</title>
  <style>
    body { margin:0; padding:0; background:#f0f2f5; font-family: 'Inter', Arial, sans-serif; }
    .wrap { max-width:600px; margin:32px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
    .header { background:#0a1628; padding:28px 32px; text-align:center; }
    .header img { width:72px; height:72px; border-radius:50%; object-fit:contain; }
    .header h1 { color:#c9a84c; font-size:18px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; margin:12px 0 4px; }
    .header p { color:rgba(255,255,255,0.5); font-size:11px; letter-spacing:0.12em; text-transform:uppercase; margin:0; }
    .body { padding:32px; color:#1a1e2c; font-size:15px; line-height:1.75; }
    .body h2 { color:#0a1628; font-size:20px; margin-top:0; }
    .btn { display:inline-block; padding:12px 28px; background:#c9a84c; color:#0a1628; border-radius:9999px; font-weight:700; font-size:13px; letter-spacing:0.08em; text-transform:uppercase; text-decoration:none; margin:16px 0; }
    .footer { background:#f7f8fa; border-top:1px solid #eef0f4; padding:20px 32px; text-align:center; font-size:12px; color:#8a92a0; }
    .footer a { color:#0a1628; }
    .divider { border:none; border-top:1px solid #eef0f4; margin:24px 0; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1>Brigade Brotherhood Fellowship</h1>
      <p>One Brigade · One Brotherhood · One Purpose</p>
    </div>
    <div class="body">
      {$body}
    </div>
    <div class="footer">
      &copy; 2026 Brigade Brotherhood Fellowship &nbsp;·&nbsp;
      <a href="mailto:bbfellowship26@gmail.com">bbfellowship26@gmail.com</a>
      &nbsp;·&nbsp; 09066681922
    </div>
  </div>
</body>
</html>
HTML;
}

// ── IP address ────────────────────────────────────────────────────────────────
function bbf_ip(): string {
    return $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['REMOTE_ADDR']
        ?? 'unknown';
}
