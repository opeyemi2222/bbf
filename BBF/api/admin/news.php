<?php
/**
 * Admin News API
 * GET    /api/admin/news.php          — list
 * GET    /api/admin/news.php?id=X     — single
 * POST   /api/admin/news.php          — create
 * PATCH  /api/admin/news.php?id=X     — update / publish / draft
 * DELETE /api/admin/news.php?id=X     — delete
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth/middleware.php';

bbf_headers();
$admin  = bbf_admin_auth();
$pdo    = bbf_db();
$id     = isset($_GET['id']) ? (int) $_GET['id'] : null;
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && $id) $method = 'PATCH';

function newsRow(array $n): array {
    return [
        'id'          => $n['id'],
        'title'       => $n['title'],
        'category'    => $n['category'],
        'author'      => $n['author'],
        'summary'     => $n['summary'],
        'content'     => $n['content'],
        'image'       => $n['image'],
        'status'      => $n['status'],
        'featured'    => (bool) $n['featured'],
        'views'       => (int) $n['views'],
        'publishedAt' => $n['published_at'],
        'date'        => $n['published_at'] ?? $n['created_at'],
        'createdAt'   => $n['created_at'],
    ];
}

// ── LIST ──────────────────────────────────────────────────────────────────────
if ($method === 'GET' && !$id) {
    $where = ['1=1']; $params = [];
    if (!empty($_GET['status']))   { $where[] = 'status = ?';   $params[] = $_GET['status']; }
    if (!empty($_GET['category'])) { $where[] = 'category = ?'; $params[] = $_GET['category']; }
    if (!empty($_GET['q']))        { $where[] = 'title LIKE ?'; $params[] = '%' . $_GET['q'] . '%'; }

    $stmt = $pdo->prepare("SELECT * FROM news WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC");
    $stmt->execute($params);
    bbf_success(array_map('newsRow', $stmt->fetchAll()));
}

// ── SINGLE ────────────────────────────────────────────────────────────────────
if ($method === 'GET' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $n = $stmt->fetch();
    if (!$n) bbf_error(404, 'Article not found.');
    bbf_success(newsRow($n));
}

// ── CREATE ────────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $d = bbf_body();
    bbf_require($d, ['title']);
    $status      = in_array($d['status'] ?? '', ['published','draft']) ? $d['status'] : 'draft';
    $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : null;

    $stmt = $pdo->prepare("
        INSERT INTO news (title, category, author, summary, content, status, featured, published_at, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        bbf_clean($d['title']),
        bbf_clean($d['category'] ?? 'Announcement'),
        bbf_clean($d['author']   ?? 'BBF Secretariat'),
        bbf_clean($d['summary']  ?? ''),
        $d['content'] ?? '',
        $status,
        !empty($d['featured']) ? 1 : 0,
        $publishedAt,
        $admin['id'],
    ]);
    bbf_success(['id' => $pdo->lastInsertId()], 'Article ' . ($status === 'published' ? 'published' : 'saved as draft') . '.');
}

// ── UPDATE ────────────────────────────────────────────────────────────────────
if ($method === 'PATCH') {
    if (!$id) bbf_error(400, 'Article ID required.');
    $d = bbf_body();

    $sets = []; $vals = [];
    $map  = ['title'=>'title','category'=>'category','author'=>'author',
             'summary'=>'summary','content'=>'content','image'=>'image'];
    foreach ($map as $k => $col) {
        if (array_key_exists($k, $d)) { $sets[] = "`{$col}` = ?"; $vals[] = bbf_clean((string)($d[$k] ?? '')); }
    }
    if (array_key_exists('featured', $d)) { $sets[] = '`featured` = ?'; $vals[] = $d['featured'] ? 1 : 0; }
    if (array_key_exists('status', $d) && in_array($d['status'], ['published','draft'])) {
        $sets[] = '`status` = ?'; $vals[] = $d['status'];
        if ($d['status'] === 'published') { $sets[] = '`published_at` = NOW()'; }
    }
    if (empty($sets)) bbf_error(422, 'Nothing to update.');
    $vals[] = $id;
    $pdo->prepare("UPDATE news SET " . implode(', ', $sets) . " WHERE id = ?")->execute($vals);
    bbf_success([], 'Article updated.');
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($method === 'DELETE') {
    if (!$id) bbf_error(400, 'Article ID required.');
    $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([$id]);
    bbf_success([], 'Article deleted.');
}

bbf_error(405, 'Method not allowed.');
