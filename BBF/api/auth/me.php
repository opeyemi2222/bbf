<?php
/**
 * GET /api/auth/me
 * Return current logged-in member from session
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';

bbf_headers();
bbf_method('GET');

session_name(SESSION_NAME_MEMBER);
session_start();

if (empty($_SESSION['logged_in'])) {
    bbf_error(401, 'Not authenticated.');
}

$pdo  = bbf_db();
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$_SESSION['member_id']]);
$member = $stmt->fetch();

if (!$member) {
    session_destroy();
    bbf_error(401, 'Session expired. Please log in again.');
}

bbf_success([
    'id'               => $member['member_id'],
    'name'             => $member['first_name'] . ' ' . $member['last_name'],
    'firstName'        => $member['first_name'],
    'lastName'         => $member['last_name'],
    'email'            => $member['email'],
    'phone'            => $member['phone'],
    'location'         => $member['state'],
    'state'            => $member['state'],
    'lga'              => $member['lga'],
    'company'          => $member['company'],
    'council'          => $member['council'],
    'bbStatus'         => $member['bb_status'],
    'brigadeSchoolCert'=> $member['bsc'] === 'yes',
    'memberSince'      => $member['created_at'],
    'membershipStatus' => $member['membership_status'],
    'profilePhoto'     => $member['profile_photo'] ?? null,
    'notifications'    => [],
]);
