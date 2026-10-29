<?php
/**
 * BBF — Global Configuration
 */

// ── Environment ──────────────────────────────────────────────────────────────
define('BBF_ENV', 'development'); // 'development' | 'production'
define('BBF_DEBUG', BBF_ENV === 'development');

// ── Database ─────────────────────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'bbf_db');
define('DB_USER', 'root');
define('DB_PASS', '');          // Change for production
define('DB_CHARSET', 'utf8mb4');

// ── App ───────────────────────────────────────────────────────────────────────
define('APP_NAME',  'Brigade Brotherhood Fellowship');
define('APP_URL',   'http://localhost/BBF');   // Change to live URL on deploy
define('API_URL',   APP_URL . '/api');

// ── Email (Gmail SMTP via PHPMailer) ─────────────────────────────────────────
define('MAIL_HOST',       'smtp.gmail.com');
define('MAIL_PORT',       587);
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_USERNAME',   'bbfellowship26@gmail.com');
define('MAIL_PASSWORD',   '');   // Paste your 16-char Gmail App Password here
define('MAIL_FROM_EMAIL', 'bbfellowship26@gmail.com');
define('MAIL_FROM_NAME',  'Brigade Brotherhood Fellowship');
define('MAIL_REPLY_TO',   'bbfellowship26@gmail.com');

// ── Contact ───────────────────────────────────────────────────────────────────
define('BBF_EMAIL',   'bbfellowship26@gmail.com');
define('BBF_PHONE',   '09066681922');
define('BBF_WEBSITE', 'www.bbfellowship.org');

// ── Session ───────────────────────────────────────────────────────────────────
define('SESSION_LIFETIME', 60 * 60 * 8); // 8 hours in seconds
define('SESSION_NAME_MEMBER', 'bbf_member_session');
define('SESSION_NAME_ADMIN',  'bbf_admin_session');

// ── File Uploads ──────────────────────────────────────────────────────────────
define('UPLOAD_DIR',      __DIR__ . '/../../api/uploads/gallery/');
define('UPLOAD_URL',      APP_URL . '/api/uploads/gallery/');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5 MB
define('UPLOAD_ALLOWED',  ['image/jpeg', 'image/png', 'image/webp']);

// ── Security ──────────────────────────────────────────────────────────────────
define('BCRYPT_COST', 12);
define('RESET_TOKEN_EXPIRY', 60 * 60); // 1 hour in seconds

// ── CORS (for local dev) ──────────────────────────────────────────────────────
define('CORS_ORIGIN', 'http://localhost');
