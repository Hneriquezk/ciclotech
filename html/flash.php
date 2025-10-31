<?php
function flash_set(string $msg) {
    $_SESSION['flash'] = $msg;
}
function flash_get() {
    if (!empty($_SESSION['flash'])) {
        $m = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $m;
    }
    return null;
}
