<?php

require_once 'config.php';


// Jika sudah login, langsung ke dashboard
if (is_login()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';


// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $accounts = read_json('accounts.json', []);

    $found = null;

    foreach ($accounts as $account) {

        if (
            $account['username'] === $username &&
            $account['password'] === $password
        ) {
            $found = $account;
            break;
        }
    }


    if ($found) {

        // Simpan session
        $_SESSION['user'] = [
            'username' => $found['username'],
            'name' => $found['name'],
            'role' => $found['role']
        ];


        // Notifikasi login
        add_notification(
            'login',
            $found['name'] . ' berhasil login sebagai ' . strtoupper($found['role'])
        );


        header('Location: dashboard.php');
        exit;

    } else {

        $error = 'Username atau password salah.';
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Ujian ASTS</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }


        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;

            background:
                linear-gradient(
                    135deg,
                    #eef2ff,
                    #dbeafe,
                    #f8fafc
                );
        }


        .login-container {
            width: 100%;
            max-width: 430px;
            padding: 20px;
        }


        .login-card {
            background: white;
            border-radius: 22px;
            padding: 35px;

            box-shadow:
                0 20px 50px rgba(0, 0, 0, 0.12);
        }


        .logo {
            width: 75px;
            height: 75px;

            margin: 0 auto 20px;

            border-radius: 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #2563eb;

            color: white;

            font-size: 32px;
            font-weight: bold;
        }


        h1 {
            text-align: center;
            color: #1e293b;
            margin-bottom: 8px;
        }


        .subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 28px;
        }


        .form-group {
            margin-bottom: 18px;
        }


        label {
            display: block;
            margin-bottom: 7px;

            color: #334155;
            font-weight: 600;
        }


        input {
            width: 100%;

            padding: 13px 15px;

            border: 1px solid #cbd5e1;

            border-radius: 10px;

            font-size: 15px;

            outline: none;

            transition: 0.2s;
        }


        input:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.12);
        }


        .btn-login {
            width: 100%;

            border: none;

            padding: 14px;

            border-radius: 10px;

            background: #2563eb;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.2s;
        }


        .btn-login:hover {
            background: #1d4ed8;
        }


        .error {
            background: #fee2e2;

            color: #b91c1c;

            padding: 12px 15px;

            border-radius: 10px;

            margin-bottom: 18px;

            font-size: 14px;
        }


        .demo {
            margin-top: 25px;

            padding: 15px;

            background: #f8fafc;

            border-radius: 12px;

            font-size: 13px;

            color: #475569;
        }


        .demo-title {
            font-weight: bold;

            margin-bottom: 8px;

            color: #1e293b;
        }


        .account {
            margin-top: 5px;
        }


        .footer {
            text-align: center;

            margin-top: 20px;

            color: #94a3b8;

            font-size: 13px;
        }

    </style>

</head>


<body>

<div class="login-container">

    <div class="login-card">

        <div class="logo">
            A
        </div>


        <h1>Ujian ASTS</h1>

        <p class="subtitle">
            Sistem Pengelolaan Data Siswa
        </p>


        <?php if ($error): ?>

            <div class="error">
                <?= e($error) ?>
            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    required
                    autofocus
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-login"
            >
                Login
            </button>


        </form>


        <div class="demo">

            <div class="demo-title">
                Akun Demo
            </div>

            <div class="account">
                👑 Admin:
                <strong>admin</strong> /
                <strong>admin123</strong>
            </div>

            <div class="account">
                👤 User:
                <strong>user</strong> /
                <strong>user123</strong>
            </div>

        </div>


    </div>


    <div class="footer">
        Ujian ASTS &copy; 2026
    </div>

</div>

</body>

</html>