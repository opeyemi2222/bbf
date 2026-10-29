<?php
/**
 * Admin Events API
 * GET    /api/admin/events.php          — list all
 * GET    /api/admin/events.php?id=X     — single event
 * POST   /api/admin/events.php          — create
 * PATCH  /api/admin/events.php?id=X     — update
 * DELETE /api/admin/events.php?id=X     — delete
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

function eventRow(array $e): array {
    return [
        'id'           => $e['id'],
        'title'        => $e['title'],
        'category'     => $e['category'],
        'description'  => $e['description'],
        'date'         => $e['event_date'],
        'endDate'      => $e['end_date'],
        'location'     => $e['location'],
        'status'       => $e['status'],
        'featured'     => (bool) $e['featured'],
        'capacity'     => $e['capacity'],
        'image'        => $e['image'],
        'registrations'=> (int) ($e['reg_count'] ?? 0),
        'createdAt'    => $e['created_at'],
    ];
}

// ── LIST ──────────────────────────────────────────────────────────────────────
if ($method === 'GET' && !$id) {
    $where = ['1=1']; $params = [];
    if (!empty($_GET['status'])) { $where[] = 'e.status = ?'; $params[] = $_GET['status']; }
    if (!empty($_GET['q']))      { $where[] = 'e.title LIKE ?'; $params[] = '%' . $_GET['q'] . '%'; }

    $stmt = $pdo->prepare("
        SELECT e.*,
            (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id) AS reg_count
        FROM events e
        WHERE " . implode(' AND ', $where) . "
        ORDER BY e.event_date DESC
    ");
    $stmt->execute($params);
    bbf_success(array_map('eventRow', $stmt->fetchAll()));
}

// ── SINGLE ────────────────────────────────────────────────────────────────────
if ($method === 'GET' && $id) {
    $stmt = $pdo->prepare("
        SELECT e.*, (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id) AS reg_count
        FROM events e WHERE e.id = ?");
    $stmt->execute([$id]);
    $e = $stmt->fetch();
    if (!$e) bbf_error(404, 'Event not found.');
    bbf_success(eventRow($e));
}

// ── CREATE ────────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $d = bbf_body();
    bbf_require($d, ['title', 'date']);
    $stmt = $pdo->prepare("
        INSERT INTO events (title, category, description, event_date, end_date, location, status, featured, capacity, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        bbf_clean($d['title']),
        bbf_clean($d['category'] ?? 'Fellowship'),
        bbf_clean($d['description'] ?? ''),
        $d['date'],
        $d['endDate'] ?? null,
        bbf_clean($d['location'] ?? 'To Be Confirmed'),
        in_array($d['status'] ?? '', ['upcoming','past','cancelled']) ? $d['status'] : 'upcoming',
        !empty($d['featured']) ? 1 : 0,
        !empty($d['capacity']) ? (int)$d['capacity'] : null,
        $admin['id'],
    ]);
    bbf_success(['id' => $pdo->lastInsertId()], 'Event created.');
}

// ── UPDATE ────────────────────────────────────────────────────────────────────
if ($method === 'PATCH') {
    if (!$id) bbf_error(400, 'Event ID required.');
    $d    = bbf_body();
    $sets = []; $vals = [];
    $map  = ['title'=>'title','category'=>'category','description'=>'description',
             'date'=>'event_date','endDate'=>'end_date','location'=>'location',
             'status'=>'status','capacity'=>'capacity'];
    foreach ($map as $k => $col) {
        if (array_key_exists($k, $d)) {
            $sets[] = "`{$col}` = ?";
            $vals[] = $d[$k] !== null ? bbf_clean((string)$d[$k]) : null;
        }
    }
    if (array_key_exists('featured', $d)) { $sets[] = '`featured` = ?'; $vals[] = $d['featured'] ? 1 : 0; }
    if (empty($sets)) bbf_error(422, 'Nothing to update.');
    $vals[] = $id;
    $pdo->prepare("UPDATE events SET " . implode(', ', $sets) . " WHERE id = ?")->execute($vals);
    bbf_success([], 'Event updated.');
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($method === 'DELETE') {
    if (!$id) bbf_error(400, 'Event ID required.');
    $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([$id]);
    bbf_success([], 'Event deleted.');
}

bbf_error(405, 'Method not allowed.');
