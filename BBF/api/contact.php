<?php
/**
 * POST /api/contact.php
 */

require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/db.php';

bbf_headers();
bbf_method('POST');

$data = bbf_body();
bbf_require($data, ['name', 'email', 'message']);

$name    = bbf_clean($data['name']);
$email   = bbf_email($data['email'] ?? '');
$phone   = bbf_clean($data['phone']   ?? '');
$subject = bbf_clean($data['subject'] ?? 'General Enquiry');
$message = bbf_clean($data['message']);

if (!$email)              bbf_error(422, 'Please enter a valid email address.');
if (strlen($name)    < 2) bbf_error(422, 'Please enter your name.');
if (strlen($message) < 5) bbf_error(422, 'Please enter a message.');

$pdo = bbf_db();
$pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)")
    ->execute([$name, $email, $phone, $subject, $message]);

// bbf_mail() returns instantly when MAIL_PASSWORD is empty — no delay
bbf_mail(BBF_EMAIL, 'BBF Secretariat',
    "BBF Contact: {$subject} — {$name}",
    "<h2>New Contact Message</h2>
    <p><strong>From:</strong> {$name}<br>
    <strong>Email:</strong> <a href='mailto:{$email}'>{$email}</a><br>
    <strong>Phone:</strong> {$phone}<br>
    <strong>Subject:</strong> {$subject}</p>
    <hr style='border:none;border-top:1px solid #eee;margin:16px 0;'>
    <p>{$message}</p>"
);

bbf_mail($email, $name,
    'We received your message — Brigade Brotherhood Fellowship',
    "<h2>Message Received</h2>
    <p>Hi {$name},</p>
    <p>Thank you for contacting Brigade Brotherhood Fellowship. We will respond as soon as possible.</p>
    <p><strong>Your message:</strong><br>{$message}</p>
    <p>Email: <a href='mailto:" . BBF_EMAIL . "'>" . BBF_EMAIL . "</a> | Phone: " . BBF_PHONE . "</p>"
);

bbf_success([], 'Message sent successfully. We will get back to you shortly.');
