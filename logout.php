<?php

require_once 'config.php';


// Simpan nama sebelum session dihapus
$nama = $_SESSION['user']['name'] ?? 'User';


// Tambahkan notifikasi logout
add_notification(
    'logout',
    $nama . ' telah logout dari sistem'
);


// Hapus session
$_SESSION = [];


// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}


// Hancurkan session
session_destroy();


// Kembali ke login
header('Location: login.php');
exit;