<?php
require_once __DIR__ . '/contact_store.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($id === null) {
    $_SESSION['flash_errors'] = ['Invalid ID.'];
    header('Location: contact_list.php'); exit;
}

if (contact_delete($id)) {
    $_SESSION['flash_success'] = 'Contact successfully deleted.';
} else {
    $_SESSION['flash_errors'] = ['Contact not found.'];
}
header('Location: contact_list.php');
exit;