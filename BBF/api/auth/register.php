<?php
/**
 * POST /api/auth/register
 * Register a new BBF member (status = Pending, awaits admin approval)
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/db.php';

bbf_headers();
bbf_method('POST');

$data = bbf_body();

bbf_require($data, ['firstName','lastName','email','phone','state','company','bbStatus','brigadeSchoolCert','agreeConstitution','agreeValues','agreeAccurate']);

$firstName = bbf_clean($data['firstName']);
$lastName  = bbf_clean($data['lastName']);
$email     = bbf_email($data['email'] ?? '');
$phone     = bbf_clean($data['phone']);
$state     = bbf_clean($data['state']);
$lga       = bbf_clean($data['lga'] ?? '');
$company   = bbf_clean($data['company']);
$council   = bbf_clean($data['council'] ?? '');
$bbStatus  = in_array($data['bbStatus'], ['active','former']) ? $data['bbStatus'] : 'active';
$bsc       = in_array($data['brigadeSchoolCert'], ['yes','in-progress','no']) ? $data['brigadeSchoolCert'] : 'in-progress';
$chapter   = bbf_clean($data['chapter'] ?? '');
$referral  = bbf_clean($data['referral'] ?? '');
$dob       = bbf_clean($data['dob'] ?? '');
$gender    = in_array($data['gender'] ?? '', ['male','female','prefer-not']) ? $data['gender'] : null;

if (!$email)                bbf_error(422, 'Please enter a valid email address.');
if (strlen($firstName) < 2) bbf_error(422, 'First name must be at least 2 characters.');
if (strlen($lastName)  < 2) bbf_error(422, 'Last name must be at least 2 characters.');

if (empty($data['agreeConstitution']) || empty($data['agreeValues']) || empty($data['agreeAccurate'])) {
    bbf_error(422, 'You must agree to the required terms to register.');
}

$pdo = bbf_db();

// Check duplicate email
$stmt = $pdo->prepare("SELECT id, membership_status FROM members WHERE email = ?");
$stmt->execute([$email]);
$existing = $stmt->fetch();
if ($existing) {
    if ($existing['membership_status'] === 'Pending')
        bbf_error(409, 'This email is already registered and is awaiting admin approval.');
    bbf_error(409, 'This email address is already registered. Please login or reset your password.');
}

// Generate member ID & password hash
$memberId     = bbf_generate_member_id();
$tempPassword = 'BBF@' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
$passwordHash = password_hash($tempPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

// Insert member
$pdo->prepare("
    INSERT INTO members
        (member_id, first_name, last_name, email, phone, password_hash,
         dob, gender, state, lga, company, council, bb_status, bsc,
         chapter, referral, membership_status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
")->execute([
    $memberId, $firstName, $lastName, $email, $phone, $passwordHash,
    $dob ?: null, $gender, $state, $lga, $company, $council,
    $bbStatus, $bsc, $chapter, $referral
]);

// Send emails — bbf_mail() returns instantly if MAIL_PASSWORD is empty
bbf_mail($email, "$firstName $lastName",
    'BBF Membership Application Received — ' . $memberId,
    "<h2>Thank you for registering, {$firstName}!</h2>
    <p>Your BBF membership application has been received.</p>
    <p><strong>Member ID:</strong> {$memberId}<br>
    <strong>Name:</strong> {$firstName} {$lastName}<br>
    <strong>Status:</strong> Pending Approval</p>
    <p>You will receive login credentials once your application is approved by the BBF Secretariat.</p>
    <p>Contact: <a href='mailto:" . BBF_EMAIL . "'>" . BBF_EMAIL . "</a> | " . BBF_PHONE . "</p>"
);

bbf_mail(BBF_EMAIL, 'BBF Admin',
    'New Membership Application — ' . $memberId,
    "<h2>New Application</h2>
    <p><strong>{$firstName} {$lastName}</strong> ({$memberId}) has applied for membership.</p>
    <p>Email: {$email} | Phone: {$phone} | State: {$state}<br>Company: {$company} | BSC: {$bsc}</p>
    <p><a href='" . APP_URL . "/admin/members.html'>Review in Admin Panel</a></p>"
);

bbf_success(['memberId' => $memberId],
    'Registration submitted successfully. You will receive a confirmation email shortly.'
);
