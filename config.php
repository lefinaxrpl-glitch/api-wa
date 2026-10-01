```php
<?php

session_start();

/*
|--------------------------------------------------------------------------
| KONFIGURASI DATABASE
|--------------------------------------------------------------------------
*/

$host = "localhost";
$user = "root";
$password = "";
$database = "api_wa_fonnte";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


/*
|--------------------------------------------------------------------------
| KONFIGURASI FONNTE
|--------------------------------------------------------------------------
*/

// Masukkan token Fonnte kamu di sini
$fonnte_token = "GANTI_DENGAN_TOKEN_FONNTE_KAMU";


/*
|--------------------------------------------------------------------------
| BASE PATH
|--------------------------------------------------------------------------
*/

define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);


/*
|--------------------------------------------------------------------------
| BACA DATA JSON
|--------------------------------------------------------------------------
*/

function read_json($file, $default = [])
{
    $path = BASE_PATH . $file;

    if (!file_exists($path)) {
        return $default;
    }

    $content = file_get_contents($path);

    if ($content === false || trim($content) === '') {
        return $default;
    }

    $data = json_decode($content, true);

    return is_array($data) ? $data : $default;
}


/*
|--------------------------------------------------------------------------
| SIMPAN DATA JSON
|--------------------------------------------------------------------------
*/

function write_json($file, $data)
{
    $path = BASE_PATH . $file;

    file_put_contents(
        $path,
        json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
    );
}


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| CEK LOGIN
|--------------------------------------------------------------------------
*/

function is_login()
{
    return isset($_SESSION['user']);
}


/*
|--------------------------------------------------------------------------
| WAJIB LOGIN
|--------------------------------------------------------------------------
*/

function require_login()
{
    if (!is_login()) {
        header('Location: login.php');
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| WAJIB ADMIN
|--------------------------------------------------------------------------
*/

function require_admin()
{
    require_login();

    if (
        !isset($_SESSION['user']['role']) ||
        $_SESSION['user']['role'] !== 'admin'
    ) {
        header('Location: dashboard.php');
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| NOTIFIKASI
|--------------------------------------------------------------------------
*/

function add_notification($type, $message)
{
    $notifications = read_json('notifications.json', []);

    array_unshift($notifications, [
        'id' => time() . rand(100, 999),
        'type' => $type,
        'message' => $message,
        'time' => date('d-m-Y H:i:s')
    ]);

    // Maksimal 30 notifikasi
    $notifications = array_slice($notifications, 0, 30);

    write_json('notifications.json', $notifications);
}


/*
|--------------------------------------------------------------------------
| VALIDASI DATA USER
|--------------------------------------------------------------------------
*/

function validate_user_data($post)
{
    $nisn = trim($post['nisn'] ?? '');
    $phone = trim($post['phone'] ?? '');
    $email = trim($post['email'] ?? '');

    // NISN wajib 10 angka
    if (!preg_match('/^\d{10}$/', $nisn)) {
        return 'NISN harus tepat 10 angka.';
    }

    // Nomor HP 10-15 angka
    if (!preg_match('/^\d{10,15}$/', $phone)) {
        return 'Nomor telepon harus 10-15 angka.';
    }

    // Email harus valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Format email tidak valid.';
    }

    return '';
}


/*
|--------------------------------------------------------------------------
| FORMAT NOMOR WHATSAPP
|--------------------------------------------------------------------------
|
| Contoh:
| 082335918905
| menjadi
| 6282335918905
|
*/

function format_whatsapp_number($phone)
{
    // Hilangkan spasi, tanda -, dan karakter lainnya
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // 08xxxxxxxx menjadi 628xxxxxxxx
    if (substr($phone, 0, 2) === '08') {
        return '62' . substr($phone, 1);
    }

    // Jika sudah 62
    if (substr($phone, 0, 2) === '62') {
        return $phone;
    }

    // Jika diawali 8
    if (substr($phone, 0, 1) === '8') {
        return '62' . $phone;
    }

    return $phone;
}


/*
|--------------------------------------------------------------------------
| KIRIM WHATSAPP FONNTE
|--------------------------------------------------------------------------
*/

function send_whatsapp($target, $message)
{
    global $fonnte_token;

    $target = format_whatsapp_number($target);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'target' => $target,
            'message' => $message
        ],
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . $fonnte_token
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($curl);

    if ($response === false) {
        $error = curl_error($curl);

        curl_close($curl);

        return [
            'success' => false,
            'message' => 'cURL Error: ' . $error,
            'response' => null
        ];
    }

    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $result = json_decode($response, true);

    if (
        is_array($result) &&
        isset($result['status']) &&
        $result['status'] === true
    ) {
        return [
            'success' => true,
            'message' => 'WhatsApp berhasil dikirim.',
            'response' => $result,
            'http_code' => $http_code
        ];
    }

    return [
        'success' => false,
        'message' => 'WhatsApp gagal dikirim.',
        'response' => $result ?? $response,
        'http_code' => $http_code
    ];
}
?>
```
