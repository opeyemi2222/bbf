<?php
/**
 * POST /api/member/change-password
 * Authenticated member changes their own password
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';

bbf_headers();
bbf_method('POST');

session_name(SESSION_NAME_MEMBER);
session_start();

if (empty($_SESSION['logged_in'])) bbf_error(401, 'Not authenticated.');

$data = bbf_body();
bbf_require($data, ['currentPassword', 'newPassword', 'confirmPassword']);

$currentPassword = $data['currentPassword'];
$newPassword     = $data['newPassword'];
$confirmPassword = $data['confirmPassword'];

if (strlen($newPassword) < 8)       bbf_error(422, 'New password must be at least 8 characters.');
if ($newPassword !== $confirmPassword) bbf_error(422, 'New passwords do not match.');

$pdo  = bbf_db();
$stmt = $pdo->prepare("SELECT password_hash, email, first_name FROM members WHERE id = ?");
$stmt->execute([(int) $_SESSION['member_id']]);
$member = $stmt->fetch();

if (!$member || !password_verify($currentPassword, $member['password_hash'])) {
    bbf_error(401, 'Current password is incorrect.');
}

$newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
$pdo->prepare("UPDATE members SET password_hash = ? WHERE id = ?")
    ->execute([$newHash, (int) $_SESSION['member_id']]);

// Notification email
$emailBody = "
<h2>Password Changed</h2>
<p>Hi {$member['first_name']},</p>
<p>Your BBF member portal password was just changed.</p>
<p>If you did not make this change, contact us immediately at <a href='mailto:" . BBF_EMAIL . "'>" . BBF_EMAIL . "</a> or call <strong>" . BBF_PHONE . "</strong>.</p>
";
bbf_mail($member['email'], $member['first_name'], 'BBF Password Changed', $emailBody);

bbf_success([], 'Password changed successfully.');
