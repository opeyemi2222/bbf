<?php
/**
 * POST /api/newsletter.php
 * Subscribe an email to the BBF newsletter
 */

require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/db.php';

bbf_headers();
bbf_method('POST');

$data  = bbf_body();
$email = bbf_email($data['email'] ?? '');

if (!$email) bbf_error(422, 'Please enter a valid email address.');

$pdo  = bbf_db();
$stmt = $pdo->prepare("SELECT id, status FROM newsletter_subscribers WHERE email = ?");
$stmt->execute([$email]);
$existing = $stmt->fetch();

if ($existing) {
    if ($existing['status'] === 'active') {
        bbf_success([], 'You are already subscribed to BBF updates.');
        return;
    }
    $pdo->prepare("UPDATE newsletter_subscribers SET status = 'active' WHERE email = ?")
        ->execute([$email]);
} else {
    $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)")
        ->execute([$email]);
}

// ── Return success immediately ────────────────────────────────────────────────
$response = json_encode(['success'=>true,'message'=>'Subscribed successfully! You will receive BBF updates by email.','data'=>[]], JSON_UNESCAPED_UNICODE);
http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
header('Content-Length: ' . strlen($response));
echo $response;
if (ob_get_level()) ob_end_flush();
flush();

// ── Emails after response ─────────────────────────────────────────────────────
if (MAIL_PASSWORD !== '') {
    $confirmBody = "
<h2>You're Subscribed!</h2>
<p>Thank you for subscribing to BBF updates. You will now receive news, event announcements, and fellowship updates from Brigade Brotherhood Fellowship.</p>
<p>To unsubscribe at any time, reply to this email with the subject <strong>Unsubscribe</strong>.</p>
<p>One Brigade · One Brotherhood · One Purpose</p>
";
    bbf_mail($email, 'BBF Subscriber', 'You are subscribed to BBF Updates', $confirmBody);

    $total = (int) $pdo->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'")->fetchColumn();
    $adminBody = "<h2>New Newsletter Subscriber</h2><p><strong>{$email}</strong> subscribed. Total active: <strong>{$total}</strong></p>";
    bbf_mail(BBF_EMAIL, 'BBF Admin', 'New Newsletter Subscriber — BBF', $adminBody);
}

exit;
