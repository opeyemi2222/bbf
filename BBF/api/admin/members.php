<?php
/**
 * Admin Members API
 * GET    /api/admin/members.php              — list all (filterable)
 * GET    /api/admin/members.php?id=X         — single member
 * PATCH  /api/admin/members.php?id=X         — update status / fields
 * DELETE /api/admin/members.php?id=X         — delete member
 *
 * Status actions via PATCH body: { "action": "approve" | "suspend" | "reactivate" }
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth/middleware.php';

bbf_headers();
$admin = bbf_admin_auth();
$pdo   = bbf_db();
$id    = isset($_GET['id']) ? (int) $_GET['id'] : null;
$method = $_SERVER['REQUEST_METHOD'];
// Allow POST as alias for PATCH (some HTTP clients don't support PATCH)
if ($method === 'POST' && $id) $method = 'PATCH';

// ── LIST ──────────────────────────────────────────────────────────────────────
if ($method === 'GET' && !$id) {
    $where  = ['1=1'];
    $params = [];

    if (!empty($_GET['status'])) {
        $where[]  = 'm.membership_status = ?';
        $params[] = $_GET['status'];
    }
    if (!empty($_GET['state'])) {
        $where[]  = 'm.state = ?';
        $params[] = $_GET['state'];
    }
    if (!empty($_GET['bsc'])) {
        $where[]  = 'm.bsc = ?';
        $params[] = $_GET['bsc'];
    }
    if (!empty($_GET['q'])) {
        $q        = '%' . $_GET['q'] . '%';
        $where[]  = '(m.first_name LIKE ? OR m.last_name LIKE ? OR m.email LIKE ? OR m.member_id LIKE ?)';
        array_push($params, $q, $q, $q, $q);
    }

    $sql  = "SELECT m.*,
                (SELECT MAX(ll.created_at) FROM login_logs ll WHERE ll.member_id = m.id AND ll.status = 'success') AS last_login
             FROM members m
             WHERE " . implode(' AND ', $where) . "
             ORDER BY m.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $members = array_map(fn($m) => [
        'id'               => $m['member_id'],
        'dbId'             => $m['id'],
        'name'             => $m['first_name'] . ' ' . $m['last_name'],
        'firstName'        => $m['first_name'],
        'lastName'         => $m['last_name'],
        'email'            => $m['email'],
        'phone'            => $m['phone'],
        'state'            => $m['state'],
        'company'          => $m['company'],
        'council'          => $m['council'],
        'bsc'              => $m['bsc'] === 'yes',
        'bbStatus'         => $m['bb_status'],
        'status'           => $m['membership_status'],
        'joined'           => substr($m['created_at'], 0, 10),
        'lastLogin'        => $m['last_login'] ? substr($m['last_login'], 0, 10) : null,
        'profilePhoto'     => $m['profile_photo'],
    ], $rows);

    bbf_success($members);
}

// ── SINGLE ────────────────────────────────────────────────────────────────────
if ($method === 'GET' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $m = $stmt->fetch();
    if (!$m) bbf_error(404, 'Member not found.');
    bbf_success([
        'id'          => $m['member_id'],
        'dbId'        => $m['id'],
        'name'        => $m['first_name'] . ' ' . $m['last_name'],
        'email'       => $m['email'],
        'phone'       => $m['phone'],
        'state'       => $m['state'],
        'lga'         => $m['lga'],
        'company'     => $m['company'],
        'council'     => $m['council'],
        'bsc'         => $m['bsc'],
        'bbStatus'    => $m['bb_status'],
        'status'      => $m['membership_status'],
        'joined'      => substr($m['created_at'], 0, 10),
    ]);
}

// ── PATCH (approve / suspend / reactivate / field update) ─────────────────────
if ($method === 'PATCH') {
    if (!$id) bbf_error(400, 'Member ID required.');
    $data = bbf_body();

    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch();
    if (!$member) bbf_error(404, 'Member not found.');

    $action = $data['action'] ?? null;

    // ── Status actions ────────────────────────────────────────────────────────
    if ($action === 'approve') {
        if ($member['membership_status'] !== 'Pending') bbf_error(409, 'Member is not in Pending status.');

        // Generate temp password
        $tempPass = 'BBF@' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $hash     = password_hash($tempPass, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

        $pdo->prepare("UPDATE members SET membership_status = 'Active', password_hash = ? WHERE id = ?")
            ->execute([$hash, $id]);

        // Welcome email with credentials
        $loginUrl  = APP_URL . '/login.html';
        $firstName = $member['first_name'];
        $memberId  = $member['member_id'];
        $email     = $member['email'];

        $emailBody = "
<h2>Welcome to BBF, {$firstName}! 🎉</h2>
<p>Your Brigade Brotherhood Fellowship membership has been <strong style='color:#16a34a;'>approved</strong>.</p>
<p>You can now log in to your member portal using the credentials below:</p>
<div style='background:#f7f8fa;border:1px solid #eef0f4;border-left:4px solid #c9a84c;border-radius:0 8px 8px 0;padding:16px 20px;margin:20px 0;'>
  <div style='margin-bottom:8px;'><span style='color:#8a92a0;font-size:12px;text-transform:uppercase;letter-spacing:0.06em;'>Member ID</span><br><strong style='color:#0a1628;font-size:16px;'>{$memberId}</strong></div>
  <div style='margin-bottom:8px;'><span style='color:#8a92a0;font-size:12px;text-transform:uppercase;letter-spacing:0.06em;'>Email</span><br><strong style='color:#0a1628;'>{$email}</strong></div>
  <div><span style='color:#8a92a0;font-size:12px;text-transform:uppercase;letter-spacing:0.06em;'>Temporary Password</span><br><strong style='color:#0a1628;font-size:16px;font-family:monospace;'>{$tempPass}</strong></div>
</div>
<p style='color:#e53e3e;font-size:13px;'><strong>Important:</strong> Please log in and change your password immediately.</p>
<p style='text-align:center;margin:28px 0;'>
  <a href='{$loginUrl}' class='btn'>Log In to Member Portal</a>
</p>
<p>Welcome to the Brotherhood! One Brigade · One Brotherhood · One Purpose.</p>
";
        bbf_mail($email, "$firstName {$member['last_name']}", 'BBF Membership Approved — Welcome!', $emailBody);

        bbf_success(['status' => 'Active'], 'Member approved and welcome email sent.');
    }

    if ($action === 'suspend') {
        $pdo->prepare("UPDATE members SET membership_status = 'Suspended' WHERE id = ?")
            ->execute([$id]);

        $emailBody = "
<h2>Account Suspended</h2>
<p>Hi {$member['first_name']},</p>
<p>Your BBF member account has been temporarily suspended by the Secretariat.</p>
<p>If you believe this is an error, please contact us at <a href='mailto:" . BBF_EMAIL . "'>" . BBF_EMAIL . "</a> or call <strong>" . BBF_PHONE . "</strong>.</p>
";
        bbf_mail($member['email'], $member['first_name'], 'BBF Account Suspended', $emailBody);
        bbf_success(['status' => 'Suspended'], 'Member suspended.');
    }

    if ($action === 'reactivate') {
        $pdo->prepare("UPDATE members SET membership_status = 'Active' WHERE id = ?")
            ->execute([$id]);

        $emailBody = "
<h2>Account Reactivated</h2>
<p>Hi {$member['first_name']},</p>
<p>Your BBF member account has been <strong style='color:#16a34a;'>reactivated</strong>. You may now log in to the member portal.</p>
<p><a href='" . APP_URL . "/login.html' class='btn'>Log In</a></p>
";
        bbf_mail($member['email'], $member['first_name'], 'BBF Account Reactivated', $emailBody);
        bbf_success(['status' => 'Active'], 'Member reactivated.');
    }

    bbf_error(422, 'Unknown action. Use approve, suspend, or reactivate.');
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($method === 'DELETE') {
    if (!$id) bbf_error(400, 'Member ID required.');
    $pdo->prepare("DELETE FROM members WHERE id = ?")->execute([$id]);
    bbf_success([], 'Member deleted.');
}

bbf_error(405, 'Method not allowed.');
