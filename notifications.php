<?php

require_once 'config.php';

require_login();

$notifications = read_json('notifications.json', []);

$nama = $_SESSION['user']['name'];
$role = $_SESSION['user']['role'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notifikasi - Ujian ASTS</title>


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

        .header-card {
            background: linear-gradient(
                135deg,
                #2563eb,
                #1d4ed8
            );

            color: white;

            padding: 28px;

            border-radius: 18px;

            margin-bottom: 25px;
        }

        .header-card h1 {
            margin-bottom: 8px;
        }

        .header-card p {
            color: #dbeafe;
        }

        .notification-list {
            background: white;

            border-radius: 15px;

            border: 1px solid #e2e8f0;

            overflow: hidden;
        }

        .notification {
            display: flex;

            align-items: center;

            gap: 15px;

            padding: 20px;

            border-bottom: 1px solid #e2e8f0;
        }

        .notification:last-child {
            border-bottom: none;
        }

        .notification-icon {
            width: 48px;
            height: 48px;

            flex-shrink: 0;

            border-radius: 12px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 21px;

            background: #dbeafe;
        }

        .notification-content {
            flex: 1;
        }

        .notification-message {
            font-size: 15px;
            font-weight: 600;

            margin-bottom: 5px;
        }

        .notification-time {
            color: #94a3b8;
            font-size: 12px;
        }

        .badge {
            display: inline-block;

            padding: 4px 9px;

            border-radius: 20px;

            background: #eff6ff;

            color: #2563eb;

            font-size: 11px;

            font-weight: bold;

            text-transform: uppercase;

            margin-bottom: 7px;
        }

        .empty {
            padding: 60px 20px;

            text-align: center;

            color: #94a3b8;
        }

        .empty-icon {
            font-size: 45px;
            margin-bottom: 15px;
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


        <a href="notifications.php" class="active">
            🔔 Notifikasi
        </a>


        <div class="menu-title">
            Akun
        </div>

        <a href="profile.php">
            👤 Profil
        </a>

        <a href="logout.php" class="logout">
            🚪 Logout
        </a>

    </div>

</aside>


<!-- MAIN -->

<main class="main">


    <!-- TOPBAR -->

    <header class="topbar">

        <div class="page-title">
            Notifikasi
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


    <!-- CONTENT -->

    <div class="content">


        <div class="header-card">

            <h1>
                🔔 Notifikasi Aktivitas
            </h1>

            <p>
                Riwayat aktivitas yang terjadi pada sistem.
            </p>

        </div>


        <div class="notification-list">


            <?php if (empty($notifications)): ?>

                <div class="empty">

                    <div class="empty-icon">
                        🔔
                    </div>

                    <h3>
                        Belum Ada Notifikasi
                    </h3>

                    <p>
                        Aktivitas sistem akan muncul di sini.
                    </p>

                </div>


            <?php else: ?>


                <?php foreach ($notifications as $notification): ?>


                    <?php

                    $type = $notification['type'] ?? 'default';

                    switch ($type) {

                        case 'login':
                            $icon = '🔐';
                            break;

                        case 'logout':
                            $icon = '🚪';
                            break;

                        case 'add':
                            $icon = '➕';
                            break;

                        case 'update':
                            $icon = '✏️';
                            break;

                        case 'delete':
                            $icon = '🗑️';
                            break;

                        default:
                            $icon = '🔔';
                            break;
                    }

                    ?>


                    <div class="notification">


                        <div class="notification-icon">
                            <?= $icon ?>
                        </div>


                        <div class="notification-content">

                            <div class="badge">
                                <?= e($type) ?>
                            </div>

                            <div class="notification-message">

                                <?= e($notification['message'] ?? '') ?>

                            </div>

                            <div class="notification-time">

                                <?= e($notification['time'] ?? '') ?>

                            </div>

                        </div>


                    </div>


                <?php endforeach; ?>


            <?php endif; ?>


        </div>


    </div>

</main>


</body>

</html>