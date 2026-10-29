<?php
/**
 * GET /api/news.php        — public news listing
 * GET /api/news.php?id=X   — single article (also increments views)
 */

require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/db.php';

bbf_headers();
bbf_method('GET');

$pdo = bbf_db();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ? AND status = 'published'");
    $stmt->execute([$id]);
    $n = $stmt->fetch();
    if (!$n) bbf_error(404, 'Article not found.');
    // Increment views
    $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?")->execute([$id]);
    bbf_success($n);
}

$where = ["status = 'published'"]; $params = [];
if (!empty($_GET['category'])) { $where[] = 'category = ?'; $params[] = $_GET['category']; }
if (!empty($_GET['featured'])) { $where[] = 'featured = 1'; }
$limit = min((int)($_GET['limit'] ?? 20), 100);

$stmt = $pdo->prepare("SELECT * FROM news WHERE " . implode(' AND ', $where) . " ORDER BY published_at DESC LIMIT ?");
$params[] = $limit;
$stmt->execute($params);
bbf_success($stmt->fetchAll());
