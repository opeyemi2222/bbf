<?php
/**
 * GET  /api/member/profile  — fetch own profile
 * PATCH /api/member/profile  — update own profile
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';

bbf_headers();

session_name(SESSION_NAME_MEMBER);
session_start();

if (empty($_SESSION['logged_in'])) bbf_error(401, 'Not authenticated.');

$pdo      = bbf_db();
$memberId = (int) $_SESSION['member_id'];

// ── GET ───────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$memberId]);
    $m = $stmt->fetch();
    if (!$m) bbf_error(404, 'Member not found.');

    bbf_success([
        'id'               => $m['member_id'],
        'name'             => $m['first_name'] . ' ' . $m['last_name'],
        'firstName'        => $m['first_name'],
        'lastName'         => $m['last_name'],
        'email'            => $m['email'],
        'phone'            => $m['phone'],
        'dob'              => $m['dob'],
        'gender'           => $m['gender'],
        'state'            => $m['state'],
        'lga'              => $m['lga'],
        'company'          => $m['company'],
        'council'          => $m['council'],
        'bbStatus'         => $m['bb_status'],
        'brigadeSchoolCert'=> $m['bsc'],
        'chapter'          => $m['chapter'],
        'memberSince'      => $m['created_at'],
        'membershipStatus' => $m['membership_status'],
        'profilePhoto'     => $m['profile_photo'],
    ]);
}

// ── PATCH ─────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $data = bbf_body();

    $allowed = ['first_name', 'last_name', 'phone', 'state', 'lga', 'company', 'council'];
    $map     = [
        'firstName' => 'first_name',
        'lastName'  => 'last_name',
        'phone'     => 'phone',
        'state'     => 'state',
        'lga'       => 'lga',
        'company'   => 'company',
        'council'   => 'council',
    ];

    $sets   = [];
    $values = [];

    foreach ($map as $input => $column) {
        if (isset($data[$input])) {
            $sets[]   = "`{$column}` = ?";
            $values[] = bbf_clean($data[$input]);
        }
    }

    if (empty($sets)) bbf_error(422, 'No updatable fields provided.');

    $values[] = $memberId;
    $pdo->prepare("UPDATE members SET " . implode(', ', $sets) . " WHERE id = ?")
        ->execute($values);

    bbf_success([], 'Profile updated successfully.');
}

bbf_error(405, 'Method not allowed.');
