<?php

require_once 'config.php';

require_login();

$nama = $_SESSION['user']['name'];
$username = $_SESSION['user']['username'];
$role = $_SESSION['user']['role'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profil - Ujian ASTS</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f1f5f9;
            color: #1e293b;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #0f172a;
            color: white;
            padding: 25px 18px;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            padding: 10px 12px 30px;
            border-bottom: 1px solid #334155;
            margin-bottom: 20px;
        }

        .brand span {
            color: #60a5fa;
        }

        .menu-title {
            color: #94a3b8;
            font-size: 12px;
            margin: 20px 12px 8px;
            text-transform: uppercase;
        }

        .menu a {
            display: block;
            text-decoration: none;
            color: #cbd5e1;
            padding: 12px 14px;
            border-radius: 9px;
            margin-bottom: 5px;
            transition: 0.2s;
        }

        .menu a:hover,
        .menu a.active {
            background: #2563eb;
            color: white;
        }

        .logout {
            margin-top: 25px !important;
            background: #7f1d1d !important;
            color: white !important;
        }

        .main {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            height: 75px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-title {
            font-size: 22px;
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 42px;
            height: 42px;
            background: #dbeafe;
            color: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .user-name {
            font-weight: bold;
        }

        .user-role {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
        }

        .content {
            padding: 30px;
        }

        .profile-card {
            max-width: 700px;
            margin: auto;
            background: white;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(
                135deg,
                #2563eb,
                #1d4ed8
            );

            color: white;
            padding: 35px;
            text-align: center;
        }

        .big-avatar {
            width: 90px;
            height: 90px;
            margin: 0 auto 15px;

            background: white;
            color: #2563eb;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 35px;
            font-weight: bold;
        }

        .profile-header h1 {
            margin-bottom: 7px;
        }

        .profile-header p {
            color: #dbeafe;
        }

        .profile-body {
            padding: 30px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;

            padding: 18px 0;

            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 14px;
        }

        .info-value {
            font-weight: 600;
            text-align: right;
        }

        .role-badge {
            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            background: #dbeafe;
            color: #2563eb;

            font-size: 12px;
            font-weight: bold;

            text-transform: uppercase;
        }

        .buttons {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;

            padding: 13px;

            border-radius: 10px;

            text-align: center;

            text-decoration: none;

            font-weight: bold;
        }

        .btn-blue {
            background: #2563eb;
            color: white;
        }

        .btn-red {
            background: #fee2e2;
            color: #b91c1c;
        }

        @media (max-width: 768px) {

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .menu {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .menu-title {
                width: 100%;
            }

            .menu a {
                margin: 0;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 0 20px;
            }

        }

    </style>

</head>

<body>


<!-- SIDEBAR -->

<aside class="sidebar">

    <div class="brand">
        Ujian <span>ASTS</span>
    </div>


    <div class="menu-title">
        Menu Utama
    </div>


    <div class="menu">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="index.html">
            👥 Data Siswa
        </a>


        <?php if ($role === 'admin'): ?>

            <a href="tambah.php">
                ➕ Tambah Data
            </a>

        <?php endif; ?>


        <a href="notifications.php">
            🔔 Notifikasi
        </a>


        <div class="menu-title">
            Akun
        </div>

        <a href="profile.php" class="active">
            👤 Profil
        </a>

        <a href="logout.php" class="logout">
            🚪 Logout
        </a>

    </div>

</aside>


<!-- MAIN -->

<main class="main">


    <header class="topbar">

        <div class="page-title">
            Profil Saya
        </div>


        <div class="user-info">

            <div class="avatar">
                <?= strtoupper(substr($nama, 0, 1)) ?>
            </div>

            <div>

                <div class="user-name">
                    <?= e($nama) ?>
                </div>

                <div class="user-role">
                    <?= e($role) ?>
                </div>

            </div>

        </div>

    </header>


    <div class="content">


        <div class="profile-card">


            <div class="profile-header">

                <div class="big-avatar">
                    <?= strtoupper(substr($nama, 0, 1)) ?>
                </div>

                <h1>
                    <?= e($nama) ?>
                </h1>

                <p>
                    Akun Sistem Ujian ASTS
                </p>

            </div>


            <div class="profile-body">


                <div class="info-row">

                    <div class="info-label">
                        Nama
                    </div>

                    <div class="info-value">
                        <?= e($nama) ?>
                    </div>

                </div>


                <div class="info-row">

                    <div class="info-label">
                        Username
                    </div>

                    <div class="info-value">
                        <?= e($username) ?>
                    </div>

                </div>


                <div class="info-row">

                    <div class="info-label">
                        Role
                    </div>

                    <div class="info-value">

                        <span class="role-badge">
                            <?= e($role) ?>
                        </span>

                    </div>

                </div>


                <div class="info-row">

                    <div class="info-label">
                        Status
                    </div>

                    <div class="info-value">
                        Aktif
                    </div>

                </div>


                <div class="buttons">

                    <a
                        href="dashboard.php"
                        class="btn btn-blue"
                    >
                        ← Kembali
                    </a>

                    <a
                        href="logout.php"
                        class="btn btn-red"
                    >
                        Logout
                    </a>

                </div>


            </div>


        </div>


    </div>

</main>


</body>

</html>