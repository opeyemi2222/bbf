<?php
/**
 * GET /api/gallery.php  — public gallery listing (published only)
 */

require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/db.php';

bbf_headers();
bbf_method('GET');

$pdo    = bbf_db();
$where  = ["status = 'published'"]; $params = [];
if (!empty($_GET['category'])) { $where[] = 'category = ?'; $params[] = $_GET['category']; }
$limit  = min((int)($_GET['limit'] ?? 50), 200);

$stmt = $pdo->prepare("SELECT * FROM gallery WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC LIMIT ?");
$params[] = $limit;
$stmt->execute($params);
$rows = $stmt->fetchAll();

$photos = array_map(fn($g) => [
    'id'       => $g['id'],
    'title'    => $g['title'],
    'category' => $g['category'],
    'image'    => str_contains($g['image'] ?? '', 'placeholder')
                    ? APP_URL . '/assets/images/placeholder-gallery.jpg'
                    : UPLOAD_URL . basename($g['image']),
    'year'     => $g['year'],
], $rows);

bbf_success($photos);
