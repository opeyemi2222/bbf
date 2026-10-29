<?php
/**
 * GET /api/admin/login-logs.php — paginated login log
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth/middleware.php';

bbf_headers();
bbf_method('GET');
bbf_admin_auth();

$pdo    = bbf_db();
$limit  = min((int)($_GET['limit']  ?? 50), 200);
$offset = (int)($_GET['offset'] ?? 0);
$status = $_GET['status'] ?? null;

$where  = ['1=1']; $params = [];
if ($status) { $where[] = 'll.status = ?'; $params[] = $status; }

$stmt = $pdo->prepare("
    SELECT ll.*, CONCAT(m.first_name, ' ', m.last_name) AS member_name
    FROM login_logs ll
    LEFT JOIN members m ON m.id = ll.member_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY ll.created_at DESC
    LIMIT ? OFFSET ?
");
$params[] = $limit;
$params[] = $offset;
$stmt->execute($params);
$logs = $stmt->fetchAll();

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM login_logs ll WHERE " . implode(' AND ', $where));
$countStmt->execute(array_slice($params, 0, -2));
$total = (int) $countStmt->fetchColumn();

bbf_success(['logs' => $logs, 'total' => $total, 'limit' => $limit, 'offset' => $offset]);
