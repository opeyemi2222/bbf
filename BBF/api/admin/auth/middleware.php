<?php
/**
 * BBF Admin Auth Middleware
 */

require_once __DIR__ . '/../../config/helpers.php';

function bbf_admin_auth(): array {
    // Method 1: PHP session (set by auth.php form login)
    session_name(SESSION_NAME_ADMIN);
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['admin_auth'])) {
        return [
            'id'    => $_SESSION['admin_id'],
            'name'  => $_SESSION['admin_name'],
            'email' => $_SESSION['admin_email'],
            'role'  => $_SESSION['admin_role'],
        ];
    }

    // Method 2: X-Admin-Token header (sent by JS fetch calls)
    $token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? '';
    if ($token === define_admin_token()) {
        return ['id'=>1,'name'=>'BBF Super Admin','email'=>'admin@bbfellowship.org','role'=>'Super Admin'];
    }

    bbf_error(401, 'Admin authentication required.');
}

function define_admin_token(): string {
    // Token derived from admin credentials — consistent between requests
    return hash('sha256', 'BBF_ADMIN_' . MAIL_USERNAME . '_' . DB_NAME);
}
