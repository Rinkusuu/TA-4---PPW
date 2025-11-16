<?php
session_start();

if (!isset($_SESSION['contacts'])) {
    $_SESSION['contacts'] = [];
}

function contact_all() {
    return $_SESSION['contacts'];
}
function contact_get(int $id) {
    return $_SESSION['contacts'][$id] ?? null;
}
function contact_add(array $data) {
    $_SESSION['contacts'][] = $data;
    return count($_SESSION['contacts']) - 1;
}
function contact_update(int $id, array $data) {
    if (!isset($_SESSION['contacts'][$id])) return false;
    $_SESSION['contacts'][$id] = $data;
    return true;
}
function contact_delete(int $id) {
    if (!isset($_SESSION['contacts'][$id])) return false;
    unset($_SESSION['contacts'][$id]);
    $_SESSION['contacts'] = array_values($_SESSION['contacts']);
    return true;
}