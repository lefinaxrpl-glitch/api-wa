<?php

require_once 'config.php';

require_admin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['name'] ?? '');
    $nisn    = trim($_POST['nisn'] ?? '');
    $ttl     = trim($_POST['ttl'] ?? '');
    $gender  = trim($_POST['gender'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');


    // =========================
    // VALIDASI
    // =========================

    $validation = validate_user_data($_POST);

    if ($validation !== '') {

        $error = $validation;

    } elseif ($name === '') {

        $error = 'Nama wajib diisi.';

    } elseif ($ttl === '') {

        $error = 'TTL wajib diisi.';

    } elseif ($gender === '') {

        $error = 'Jenis kelamin wajib dipilih.';

    } elseif ($address === '') {

        $error = 'Alamat wajib diisi.';

    } else {

        // =========================
        // BACA DATA
        // =========================

        $data = read_json('data.json', []);


        // =========================
        // CEK NISN DUPLIKAT
        // =========================

        $nisnExists = false;

        foreach ($data as $item) {

            if (($item['nisn'] ?? '') === $nisn) {

                $nisnExists = true;

                break;
            }
        }


        if ($nisnExists) {

            $error = 'NISN tersebut sudah terdaftar.';

        } else {

            // =========================
            // BUAT ID BARU
            // =========================

            $ids = array_column($data, 'id');

            $newId = empty($ids)
                ? 1
                : max($ids) + 1;


            // =========================
            // TAMBAH DATA
            // =========================

            $data[] = [

                'id'      => $newId,

                'name'    => $name,

                'nisn'    => $nisn,

                'ttl'     => $ttl,

                'gender'  => $gender,

                'email'   => $email,

                'phone'   => $phone,

                'address' => $address

            ];


            // =========================
            // SIMPAN JSON
            // =========================

            write_json(
                'data.json',
                $data
            );


            // =========================
            // NOTIFIKASI
            // =========================

            add_notification(

                'add',

                'Data siswa "' .
                $name .
                '" berhasil ditambahkan oleh ' .
                $_SESSION['user']['name']

            );


            // =========================
            // KEMBALI KE DASHBOARD
            // =========================

            header(
                'Location: dashboard.php?success=' .
                urlencode('Data siswa berhasil ditambahkan')
            );

            exit;
        }
    }
}

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Tambah Data Siswa
    </title>


    <style>

        * {
            box-sizing: border-box;

            margin: 0;

            padding: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;
        }


        body {

            background:
                linear-gradient(
                    135deg,
                    #eff6ff,
                    #f8fafc
                );

            min-height: 100vh;

            color: #1e293b;
        }


        .container {

            width: 100%;

            max-width: 900px;

            margin: 40px auto;

            padding: 20px;
        }


        .card {

            background: white;

            border-radius: 20px;

            overflow: hidden;

            border:
                1px solid #e2e8f0;

            box-shadow:
                0 10px 30px
                rgba(15, 23, 42, 0.08);
        }


        /* =========================
           HEADER
        ========================= */

        .header {

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );

            color: white;

            padding: 30px;
        }


        .header-icon {

            font-size: 35px;

            margin-bottom: 10px;
        }


        .header h1 {

            font-size: 26px;

            margin-bottom: 8px;
        }


        .header p {

            color: #dbeafe;

            font-size: 14px;
        }


        /* =========================
           BODY
        ========================= */

        .body {

            padding: 30px;
        }


        /* =========================
           ERROR
        ========================= */

        .error {

            background: #fee2e2;

            color: #b91c1c;

            padding: 14px 16px;

            border-radius: 10px;

            margin-bottom: 22px;

            font-size: 14px;

            border:
                1px solid #fecaca;
        }


        /* =========================
           FORM
        ========================= */

        .form-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 20px;
        }


        .form-group {

            margin-bottom: 3px;
        }


        .full {

            grid-column:
                1 / -1;
        }


        label {

            display: block;

            margin-bottom: 8px;

            font-weight: 600;

            color: #334155;

            font-size: 14px;
        }


        .required {

            color: #dc2626;
        }


        input,
        select,
        textarea {

            width: 100%;

            padding: 13px 14px;

            border:
                1px solid #cbd5e1;

            border-radius: 10px;

            font-size: 14px;

            outline: none;

            background: white;

            color: #1e293b;

            transition: 0.2s;
        }


        input:focus,
        select:focus,
        textarea:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(
                    37,
                    99,
                    235,
                    0.12
                );
        }


        textarea {

            min-height: 120px;

            resize: vertical;
        }


        .help {

            margin-top: 6px;

            font-size: 12px;

            color: #64748b;
        }


        /* =========================
           BUTTON
        ========================= */

        .buttons {

            display: flex;

            gap: 12px;

            margin-top: 30px;
        }


        .btn {

            flex: 1;

            padding: 14px;

            border: none;

            border-radius: 10px;

            text-decoration: none;

            text-align: center;

            font-weight: bold;

            cursor: pointer;

            font-size: 14px;

            transition: 0.2s;
        }


        .btn-back {

            background: #e2e8f0;

            color: #334155;
        }


        .btn-back:hover {

            background: #cbd5e1;
        }


        .btn-save {

            background: #2563eb;

            color: white;
        }


        .btn-save:hover {

            background: #1d4ed8;

            transform:
                translateY(-1px);
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 650px) {

            .container {

                margin: 15px auto;

                padding: 10px;
            }


            .header {

                padding: 22px;
            }


            .header h1 {

                font-size: 22px;
            }


            .body {

                padding: 20px;
            }


            .form-grid {

                grid-template-columns: 1fr;
            }


            .full {

                grid-column: auto;
            }


            .buttons {

                flex-direction: column;
            }

        }

    </style>

</head>


<body>


<div class="container">


    <div class="card">


        <!-- HEADER -->

        <div class="header">

            <div class="header-icon">
                ➕
            </div>

            <h1>
                Tambah Data Siswa
            </h1>

            <p>
                Masukkan data siswa baru ke dalam sistem.
            </p>

        </div>


        <!-- BODY -->

        <div class="body">


            <?php if ($error): ?>

                <div class="error">

                    ❌
                    <?= e($error) ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                autocomplete="off"
            >


                <div class="form-grid">


                    <!-- NAMA -->

                    <div class="form-group">

                        <label>

                            Nama Lengkap

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            name="name"
                            value="<?= e($_POST['name'] ?? '') ?>"
                            placeholder="Masukkan nama lengkap"
                            required
                        >

                    </div>


                    <!-- NISN -->

                    <div class="form-group">

                        <label>

                            NISN

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            name="nisn"
                            value="<?= e($_POST['nisn'] ?? '') ?>"
                            placeholder="Masukkan 10 angka NISN"
                            maxlength="10"
                            minlength="10"
                            pattern="[0-9]{10}"
                            inputmode="numeric"
                            required
                        >

                        <div class="help">

                            NISN harus tepat 10 angka.

                        </div>

                    </div>


                    <!-- TTL -->

                    <div class="form-group">

                        <label>

                            Tempat, Tanggal Lahir

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            name="ttl"
                            value="<?= e($_POST['ttl'] ?? '') ?>"
                            placeholder="Contoh: Ponorogo, 12 Agustus 2010"
                            required
                        >

                    </div>


                    <!-- GENDER -->

                    <div class="form-group">

                        <label>

                            Jenis Kelamin

                            <span class="required">
                                *
                            </span>

                        </label>

                        <select
                            name="gender"
                            required
                        >

                            <option value="">
                                -- Pilih Jenis Kelamin --
                            </option>

                            <option
                                value="MALE"
                                <?= (
                                    ($_POST['gender'] ?? '') === 'MALE'
                                )
                                ? 'selected'
                                : ''
                                ?>
                            >
                                👦 Laki-laki
                            </option>

                            <option
                                value="FEMALE"
                                <?= (
                                    ($_POST['gender'] ?? '') === 'FEMALE'
                                )
                                ? 'selected'
                                : ''
                                ?>
                            >
                                👧 Perempuan
                            </option>

                        </select>

                    </div>


                    <!-- EMAIL -->

                    <div class="form-group">

                        <label>

                            Email

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?= e($_POST['email'] ?? '') ?>"
                            placeholder="contoh@email.com"
                            required
                        >

                    </div>


                    <!-- PHONE -->

                    <div class="form-group">

                        <label>

                            Nomor HP

                            <span class="required">
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="<?= e($_POST['phone'] ?? '') ?>"
                            placeholder="Contoh: 081234567890"
                            maxlength="15"
                            minlength="10"
                            pattern="[0-9]{10,15}"
                            inputmode="numeric"
                            required
                        >

                        <div class="help">

                            Nomor HP harus 10–15 angka.

                        </div>

                    </div>


                    <!-- ALAMAT -->

                    <div class="form-group full">

                        <label>

                            Alamat

                            <span class="required">
                                *
                            </span>

                        </label>

                        <textarea
                            name="address"
                            placeholder="Masukkan alamat lengkap"
                            required
                        ><?= e($_POST['address'] ?? '') ?></textarea>

                    </div>


                </div>


                <!-- BUTTON -->

                <div class="buttons">


                    <a
                        href="dashboard.php"
                        class="btn btn-back"
                    >

                        ← Kembali ke Dashboard

                    </a>


                    <button
                        type="submit"
                        class="btn btn-save"
                    >

                        💾 Simpan Data

                    </button>


                </div>


            </form>


        </div>

    </div>

</div>


</body>

</html>