<?php
/**
 * POST /api/auth/login
 * Member login — returns session
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';

bbf_headers();
bbf_method('POST');

$data  = bbf_body();
$email = bbf_email($data['email'] ?? '');
$pass  = $data['password'] ?? '';

if (!$email || !$pass) {
    bbf_error(422, 'Email and password are required.');
}

$pdo  = bbf_db();
$stmt = $pdo->prepare("SELECT * FROM members WHERE email = ?");
$stmt->execute([$email]);
$member = $stmt->fetch();

// Log attempt
$logStmt = $pdo->prepare("
    INSERT INTO login_logs (member_id, email, ip_address, user_agent, status)
    VALUES (?, ?, ?, ?, ?)
");

if (!$member || !password_verify($pass, $member['password_hash'])) {
    $logStmt->execute([
        $member['id'] ?? null,
        $email,
        bbf_ip(),
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        'failed'
    ]);
    bbf_error(401, 'Incorrect email or password. Please try again.');
}

if ($member['membership_status'] === 'Pending') {
    bbf_error(403, 'Your membership application is still pending approval. You will be notified by email once approved.');
}

if ($member['membership_status'] === 'Suspended') {
    bbf_error(403, 'Your account has been suspended. Please contact the BBF Secretariat for assistance.');
}

// ── Start session ─────────────────────────────────────────────────────────────
session_name(SESSION_NAME_MEMBER);
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'secure'   => false, // true in production (HTTPS)
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$_SESSION['member_id']    = $member['id'];
$_SESSION['member_uid']   = $member['member_id'];
$_SESSION['member_email'] = $member['email'];
$_SESSION['member_name']  = $member['first_name'] . ' ' . $member['last_name'];
$_SESSION['logged_in']    = true;

// Update last login log
$logStmt->execute([$member['id'], $email, bbf_ip(), $_SERVER['HTTP_USER_AGENT'] ?? '', 'success']);

bbf_success([
    'id'               => $member['member_id'],
    'name'             => $member['first_name'] . ' ' . $member['last_name'],
    'email'            => $member['email'],
    'phone'            => $member['phone'],
    'location'         => $member['state'],
    'company'          => $member['company'],
    'council'          => $member['council'],
    'brigadeSchoolCert'=> $member['bsc'] === 'yes',
    'memberSince'      => $member['created_at'],
    'membershipStatus' => $member['membership_status'],
    'profilePhoto'     => $member['profile_photo'] ?? null,
], 'Login successful. Welcome back!');
