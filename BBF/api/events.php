<?php
/**
 * GET /api/events.php        — public event listing
 * GET /api/events.php?id=X   — single event
 */

require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/db.php';

bbf_headers();
bbf_method('GET');

$pdo = bbf_db();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $e = $stmt->fetch();
    if (!$e) bbf_error(404, 'Event not found.');
    bbf_success($e);
}

$where = ['1=1']; $params = [];
if (!empty($_GET['status']))   { $where[] = 'status = ?';   $params[] = $_GET['status']; }
if (!empty($_GET['featured'])) { $where[] = 'featured = 1'; }
$limit = min((int)($_GET['limit'] ?? 20), 100);

$stmt = $pdo->prepare("SELECT * FROM events WHERE " . implode(' AND ', $where) . " ORDER BY event_date ASC LIMIT ?");
$params[] = $limit;
$stmt->execute($params);
bbf_success($stmt->fetchAll());
