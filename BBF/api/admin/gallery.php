<?php
/**
 * Admin Gallery API
 * GET    /api/admin/gallery.php          — list
 * GET    /api/admin/gallery.php?id=X     — single
 * POST   /api/admin/gallery.php          — upload new photo (multipart/form-data)
 * PATCH  /api/admin/gallery.php?id=X     — update / approve / reject
 * DELETE /api/admin/gallery.php?id=X     — delete (also removes file)
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth/middleware.php';

bbf_headers();
$admin  = bbf_admin_auth();
$pdo    = bbf_db();
$id     = isset($_GET['id']) ? (int) $_GET['id'] : null;
$method = $_SERVER['REQUEST_METHOD'];

function galRow(array $g): array {
    // If image is a full path or URL use it directly, otherwise build upload URL
    $img = $g['image'] ?? '';
    if (empty($img)) {
        $imgUrl = APP_URL . '/assets/images/placeholder-gallery.jpg';
    } elseif (str_starts_with($img, 'http') || str_starts_with($img, '/')) {
        $imgUrl = $img;
    } elseif (str_contains($img, 'placeholder')) {
        $imgUrl = APP_URL . '/assets/images/placeholder-gallery.jpg';
    } else {
        $imgUrl = UPLOAD_URL . basename($img);
    }
    return [
        'id'           => $g['id'],
        'title'        => $g['title'],
        'category'     => $g['category'],
        'image'        => $imgUrl,
        'year'         => $g['year'],
        'status'       => $g['status'],
        'uploadedBy'   => $g['uploader_name'],
        'createdAt'    => $g['created_at'],
    ];
}

// ── LIST ──────────────────────────────────────────────────────────────────────
if ($method === 'GET' && !$id) {
    $where = ['1=1']; $params = [];
    if (!empty($_GET['status']))   { $where[] = 'status = ?';   $params[] = $_GET['status']; }
    if (!empty($_GET['category'])) { $where[] = 'category = ?'; $params[] = $_GET['category']; }
    if (!empty($_GET['q']))        { $where[] = 'title LIKE ?'; $params[] = '%' . $_GET['q'] . '%'; }

    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC");
    $stmt->execute($params);
    bbf_success(array_map('galRow', $stmt->fetchAll()));
}

// ── SINGLE ────────────────────────────────────────────────────────────────────
if ($method === 'GET' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $g = $stmt->fetch();
    if (!$g) bbf_error(404, 'Photo not found.');
    bbf_success(galRow($g));
}

// ── UPLOAD / CREATE ───────────────────────────────────────────────────────────
if ($method === 'POST') {
    // Fields come via $_POST (multipart), file via $_FILES
    $title    = bbf_clean($_POST['title']    ?? '');
    $category = bbf_clean($_POST['category'] ?? 'Fellowship');
    $year     = (int) ($_POST['year']        ?? date('Y'));
    $status   = in_array($_POST['status'] ?? '', ['published','pending']) ? $_POST['status'] : 'pending';
    $uploader = bbf_clean($_POST['uploadedBy'] ?? $admin['name']);

    if (!$title) bbf_error(422, 'Title is required.');

    // Handle file upload
    if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        bbf_error(422, 'A valid image file is required.');
    }

    $file     = $_FILES['image'];
    $mimeType = mime_content_type($file['tmp_name']);

    if (!in_array($mimeType, UPLOAD_ALLOWED)) {
        bbf_error(422, 'Only JPG, PNG, and WEBP images are allowed.');
    }
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        bbf_error(422, 'Image must be under 5 MB.');
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'bbf_' . uniqid() . '.' . strtolower($ext);
    $destPath = UPLOAD_DIR . $filename;

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        bbf_error(500, 'Failed to save uploaded file.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO gallery (title, category, image, year, status, uploaded_by, uploader_name)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $category, $filename, $year ?: null, $status, $admin['id'], $uploader]);

    bbf_success(['id' => $pdo->lastInsertId(), 'image' => UPLOAD_URL . $filename], 'Photo uploaded.');
}

// ── UPDATE / APPROVE / REJECT ─────────────────────────────────────────────────
if ($method === 'PATCH') {
    if (!$id) bbf_error(400, 'Photo ID required.');
    $d = bbf_body();

    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $photo = $stmt->fetch();
    if (!$photo) bbf_error(404, 'Photo not found.');

    $action = $d['action'] ?? null;

    if ($action === 'approve') {
        $pdo->prepare("UPDATE gallery SET status = 'published', approved_by = ? WHERE id = ?")
            ->execute([$admin['id'], $id]);
        bbf_success([], 'Photo approved and published.');
    }

    if ($action === 'reject') {
        $pdo->prepare("UPDATE gallery SET status = 'rejected' WHERE id = ?")->execute([$id]);
        bbf_success([], 'Photo rejected.');
    }

    // Generic field update
    $sets = []; $vals = [];
    $map  = ['title'=>'title','category'=>'category','year'=>'year'];
    foreach ($map as $k => $col) {
        if (array_key_exists($k, $d)) { $sets[] = "`{$col}` = ?"; $vals[] = bbf_clean((string)$d[$k]); }
    }
    if (array_key_exists('status', $d) && in_array($d['status'], ['published','pending','rejected'])) {
        $sets[] = '`status` = ?'; $vals[] = $d['status'];
    }
    if (empty($sets)) bbf_error(422, 'Nothing to update.');
    $vals[] = $id;
    $pdo->prepare("UPDATE gallery SET " . implode(', ', $sets) . " WHERE id = ?")->execute($vals);
    bbf_success([], 'Photo updated.');
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($method === 'DELETE') {
    if (!$id) bbf_error(400, 'Photo ID required.');
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && $row['image']) {
        $filePath = UPLOAD_DIR . basename($row['image']);
        if (file_exists($filePath)) unlink($filePath);
    }
    $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$id]);
    bbf_success([], 'Photo deleted.');
}

bbf_error(405, 'Method not allowed.');
