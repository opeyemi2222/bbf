<?php
/**
 * POST /api/admin/auth/login
 * Admin login — PHP session based
 */

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../config/db.php';

bbf_headers();
bbf_method('POST');

$data  = bbf_body();
$email = bbf_email($data['email'] ?? '');
$pass  = $data['password'] ?? '';

if (!$email || !$pass) bbf_error(422, 'Email and password are required.');

$pdo  = bbf_db();
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($pass, $admin['password_hash'])) {
    // Brief delay to slow brute force
    sleep(1);
    bbf_error(401, 'Incorrect email or password. Access denied.');
}

session_name(SESSION_NAME_ADMIN);
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$_SESSION['admin_id']    = $admin['id'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_name']  = $admin['name'];
$_SESSION['admin_role']  = $admin['role'];
$_SESSION['admin_auth']  = true;

// Update last login
$pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")
    ->execute([$admin['id']]);

bbf_success([
    'id'   => $admin['id'],
    'name' => $admin['name'],
    'role' => $admin['role'],
], 'Admin login successful.');
