<?php
/**
 * POST /api/admin/auth/logout
 */

require_once __DIR__ . '/../../config/helpers.php';

bbf_headers();
bbf_method('POST');

session_name(SESSION_NAME_ADMIN);
session_start();
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

bbf_success([], 'Admin logged out.');
