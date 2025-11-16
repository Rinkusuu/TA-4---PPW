<?php
require_once __DIR__ . '/contact_store.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact_list.php'); exit;
}

$mode = $_POST['mode'] ?? 'create';
$idx  = isset($_POST['index']) ? (int)$_POST['index'] : null;

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');

$errors = [];
if ($name === '') $errors[] = 'Full name is required.';
if ($phone === '') $errors[] = 'Phone number is required.';
elseif (!preg_match('/^[0-9+ ]+$/', $phone)) $errors[] = 'Phone number can only contain digits, spaces, and +.';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email format is invalid.';

if (!empty($errors)) {
    // Save errors and old input to session then redirect back to form
    $_SESSION['flash_errors'] = $errors;
    $_SESSION['old_input'] = ['name'=>$name,'phone'=>$phone,'email'=>$email,'address'=>$address];
    $redirect = $mode === 'edit' ? "contact_form.php?id={$idx}" : "contact_form.php";
    header("Location: {$redirect}");
    exit;
}

$data = ['name'=>$name,'phone'=>$phone,'email'=>$email,'address'=>$address];

if ($mode === 'edit' && $idx !== null) {
    if (contact_update($idx, $data)) {
        $_SESSION['flash_success'] = 'Contact successfully updated.';
    } else {
        $_SESSION['flash_errors'] = ['Contact not found.'];
    }
} else {
    contact_add($data);
    $_SESSION['flash_success'] = 'New contact successfully added.';
}

header('Location: contact_list.php');
exit;