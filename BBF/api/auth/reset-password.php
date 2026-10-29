<?php
/**
 * POST /api/auth/reset-password         — request reset link
 * POST /api/auth/reset-password?action=verify  — verify token & set new password
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';

bbf_headers();
bbf_method('POST');

$action = $_GET['action'] ?? 'request';
$data   = bbf_body();
$pdo    = bbf_db();

// ══ REQUEST RESET LINK ════════════════════════════════════════════════════════
if ($action === 'request') {
    $email = bbf_email($data['email'] ?? '');
    if (!$email) bbf_error(422, 'Please enter a valid email address.');

    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM members WHERE email = ? AND membership_status = 'Active'");
    $stmt->execute([$email]);
    $member = $stmt->fetch();

    // Always respond with success (don't reveal if email exists — security)
    if ($member) {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + RESET_TOKEN_EXPIRY);

        $pdo->prepare("UPDATE members SET reset_token = ?, reset_expires = ? WHERE id = ?")
            ->execute([$token, $expires, $member['id']]);

        $resetUrl  = APP_URL . '/reset-password.html?token=' . $token;
        $firstName = $member['first_name'];

        $emailBody = "
<h2>Password Reset Request</h2>
<p>Hi {$firstName},</p>
<p>We received a request to reset your BBF member portal password. Click the button below to set a new password:</p>
<p style='text-align:center;margin:28px 0;'>
  <a href='{$resetUrl}' class='btn'>Reset My Password</a>
</p>
<p>This link expires in <strong>1 hour</strong>. If you did not request a password reset, you can safely ignore this email.</p>
<hr class='divider' />
<p style='font-size:12px;color:#8a92a0;'>If the button doesn't work, copy and paste this link into your browser:<br>{$resetUrl}</p>
";
        bbf_mail($email, "$firstName {$member['last_name']}", 'BBF Password Reset Request', $emailBody);
    }

    bbf_success([], 'If that email is registered with BBF, a reset link has been sent. Check your inbox.');
}

// ══ VERIFY TOKEN & SET NEW PASSWORD ══════════════════════════════════════════
if ($action === 'verify') {
    bbf_require($data, ['token', 'password']);

    $token    = bbf_clean($data['token']);
    $password = $data['password'];

    if (strlen($password) < 8) bbf_error(422, 'Password must be at least 8 characters.');

    $stmt = $pdo->prepare("
        SELECT id, first_name, email FROM members
        WHERE reset_token = ? AND reset_expires > NOW() AND membership_status = 'Active'
    ");
    $stmt->execute([$token]);
    $member = $stmt->fetch();

    if (!$member) bbf_error(400, 'This reset link is invalid or has expired. Please request a new one.');

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    $pdo->prepare("UPDATE members SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?")
        ->execute([$hash, $member['id']]);

    $emailBody = "
<h2>Password Changed</h2>
<p>Hi {$member['first_name']},</p>
<p>Your BBF member portal password has been changed successfully.</p>
<p>If you did not make this change, please contact us immediately at <a href='mailto:" . BBF_EMAIL . "'>" . BBF_EMAIL . "</a> or call <strong>" . BBF_PHONE . "</strong>.</p>
";
    bbf_mail($member['email'], $member['first_name'], 'BBF Password Changed', $emailBody);

    bbf_success([], 'Password changed successfully. You can now log in with your new password.');
}

bbf_error(400, 'Invalid action.');
