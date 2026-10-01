<?php

require_once 'config.php';

require_admin();


// Ambil ID dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;


// Baca data JSON
$data = read_json('data.json', []);


// Cari data yang akan dihapus
$index = -1;

foreach ($data as $key => $item) {

    if ((int)($item['id'] ?? 0) === $id) {
        $index = $key;
        break;
    }
}


// Jika data tidak ditemukan
if ($index === -1) {

    header('Location: index.html?error=Data tidak ditemukan');
    exit;
}


// Simpan nama sebelum dihapus
$nama = $data[$index]['name'] ?? 'Data siswa';


// Hapus data
array_splice($data, $index, 1);


// Simpan kembali ke data.json
write_json('data.json', $data);


// Tambahkan notifikasi
add_notification(
    'delete',
    'Data siswa "' . $nama . '" berhasil dihapus oleh ' .
    $_SESSION['user']['name']
);


// Kembali ke halaman data
header('Location: index.html?success=Data berhasil dihapus');
exit;