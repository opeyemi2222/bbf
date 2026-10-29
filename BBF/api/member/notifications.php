<?php
/**
 * GET /api/member/notifications
 * Returns recent news items as member notifications (until dedicated notifications table added)
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';

bbf_headers();
bbf_method('GET');

session_name(SESSION_NAME_MEMBER);
session_start();

if (empty($_SESSION['logged_in'])) bbf_error(401, 'Not authenticated.');

$pdo = bbf_db();

// Pull latest 5 published news items as notifications
$stmt = $pdo->query("
    SELECT id, title AS message, published_at AS date
    FROM news
    WHERE status = 'published'
    ORDER BY published_at DESC
    LIMIT 5
");
$rows = $stmt->fetchAll();

$notifications = array_map(fn($r) => [
    'id'      => $r['id'],
    'message' => $r['message'],
    'date'    => $r['date'],
    'read'    => false,
], $rows);

// Always include welcome notification
array_unshift($notifications, [
    'id'      => 0,
    'message' => 'Welcome to Brigade Brotherhood Fellowship!',
    'date'    => $_SESSION['member_since'] ?? date('Y-m-d'),
    'read'    => true,
]);

bbf_success($notifications);
