<?php

require_once 'config.php';

require_admin();


// Ambil ID dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$data = read_json('data.json', []);

$index = -1;


// Cari data berdasarkan ID
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


$error = '';


// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['name'] ?? '');
    $nisn    = trim($_POST['nisn'] ?? '');
    $ttl     = trim($_POST['ttl'] ?? '');
    $gender  = trim($_POST['gender'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');


    // Validasi NISN, HP, dan email
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

        // Cek NISN agar tidak sama dengan siswa lain
        $nisnExists = false;

        foreach ($data as $key => $item) {

            // Lewati data yang sedang diedit
            if ($key === $index) {
                continue;
            }

            if (($item['nisn'] ?? '') === $nisn) {
                $nisnExists = true;
                break;
            }
        }


        if ($nisnExists) {

            $error = 'NISN tersebut sudah digunakan oleh siswa lain.';

        } else {

            // Simpan data lama untuk notifikasi
            $namaLama = $data[$index]['name'] ?? 'Data siswa';


            // Update data
            $data[$index] = [
                'id'      => $id,
                'name'    => $name,
                'nisn'    => $nisn,
                'ttl'     => $ttl,
                'gender'  => $gender,
                'email'   => $email,
                'phone'   => $phone,
                'address' => $address
            ];


            // Simpan ke data.json
            write_json('data.json', $data);


            // Tambahkan notifikasi
            add_notification(
                'update',
                'Data siswa "' . $namaLama .
                '" berhasil diperbarui menjadi "' . $name .
                '" oleh ' . $_SESSION['user']['name']
            );


            // Kembali ke data siswa
            header('Location: index.html?success=Data berhasil diperbarui');
            exit;
        }
    }
}


// Data yang ditampilkan di form
$item = $data[$index];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Data - Ujian ASTS</title>


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


        .container {
            width: 100%;
            max-width: 850px;

            margin: 40px auto;

            padding: 20px;
        }


        .card {
            background: white;

            border-radius: 18px;

            border: 1px solid #e2e8f0;

            overflow: hidden;
        }


        .header {
            background: linear-gradient(
                135deg,
                #2563eb,
                #1d4ed8
            );

            color: white;

            padding: 28px;
        }


        .header h1 {
            margin-bottom: 7px;
        }


        .header p {
            color: #dbeafe;
        }


        .body {
            padding: 30px;
        }


        .error {
            background: #fee2e2;

            color: #b91c1c;

            padding: 13px 15px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        .form-grid {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 18px;
        }


        .form-group {
            margin-bottom: 5px;
        }


        .full {
            grid-column: 1 / -1;
        }


        label {
            display: block;

            margin-bottom: 7px;

            font-weight: 600;

            color: #334155;
        }


        .required {
            color: #dc2626;
        }


        input,
        select,
        textarea {
            width: 100%;

            padding: 13px 14px;

            border: 1px solid #cbd5e1;

            border-radius: 10px;

            font-size: 14px;

            outline: none;
        }


        input:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.12);
        }


        textarea {
            min-height: 110px;

            resize: vertical;
        }


        .help {
            margin-top: 5px;

            font-size: 12px;

            color: #64748b;
        }


        .buttons {
            display: flex;

            gap: 10px;

            margin-top: 25px;
        }


        .btn {
            flex: 1;

            padding: 13px;

            border: none;

            border-radius: 10px;

            text-decoration: none;

            text-align: center;

            font-weight: bold;

            cursor: pointer;

            font-size: 14px;
        }


        .btn-save {
            background: #2563eb;

            color: white;
        }


        .btn-save:hover {
            background: #1d4ed8;
        }


        .btn-back {
            background: #e2e8f0;

            color: #334155;
        }


        @media (max-width: 650px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
            }

            .container {
                margin: 20px auto;
            }

            .body {
                padding: 20px;
            }

        }

    </style>

</head>


<body>


<div class="container">

    <div class="card">


        <div class="header">

            <h1>
                ✏️ Edit Data Siswa
            </h1>

            <p>
                Perbarui informasi data siswa.
            </p>

        </div>


        <div class="body">


            <?php if ($error): ?>

                <div class="error">
                    ❌ <?= e($error) ?>
                </div>

            <?php endif; ?>


            <form method="POST">


                <div class="form-grid">


                    <!-- NAMA -->

                    <div class="form-group">

                        <label>
                            Nama Lengkap
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="<?= e($_POST['name'] ?? $item['name'] ?? '') ?>"
                            placeholder="Masukkan nama lengkap"
                            required
                        >

                    </div>


                    <!-- NISN -->

                    <div class="form-group">

                        <label>
                            NISN
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="nisn"
                            value="<?= e($_POST['nisn'] ?? $item['nisn'] ?? '') ?>"
                            placeholder="10 angka"
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
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="ttl"
                            value="<?= e($_POST['ttl'] ?? $item['ttl'] ?? '') ?>"
                            placeholder="Contoh: Ponorogo, 12 Agustus 2010"
                            required
                        >

                    </div>


                    <!-- JENIS KELAMIN -->

                    <div class="form-group">

                        <label>
                            Jenis Kelamin
                            <span class="required">*</span>
                        </label>

                        <?php
                        $genderValue = $_POST['gender'] ?? ($item['gender'] ?? '');
                        ?>

                        <select
                            name="gender"
                            required
                        >

                            <option value="">
                                -- Pilih Jenis Kelamin --
                            </option>

                            <option
                                value="MALE"
                                <?= $genderValue === 'MALE' ? 'selected' : '' ?>
                            >
                                Laki-laki
                            </option>

                            <option
                                value="FEMALE"
                                <?= $genderValue === 'FEMALE' ? 'selected' : '' ?>
                            >
                                Perempuan
                            </option>

                        </select>

                    </div>


                    <!-- EMAIL -->

                    <div class="form-group">

                        <label>
                            Email
                            <span class="required">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?= e($_POST['email'] ?? $item['email'] ?? '') ?>"
                            placeholder="contoh@email.com"
                            required
                        >

                    </div>


                    <!-- PHONE -->

                    <div class="form-group">

                        <label>
                            Nomor HP
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="<?= e($_POST['phone'] ?? $item['phone'] ?? '') ?>"
                            placeholder="10-15 angka"
                            maxlength="15"
                            minlength="10"
                            pattern="[0-9]{10,15}"
                            inputmode="numeric"
                            required
                        >

                        <div class="help">
                            Nomor HP harus 10-15 angka.
                        </div>

                    </div>


                    <!-- ALAMAT -->

                    <div class="form-group full">

                        <label>
                            Alamat
                            <span class="required">*</span>
                        </label>

                        <textarea
                            name="address"
                            placeholder="Masukkan alamat lengkap"
                            required
                        ><?= e($_POST['address'] ?? $item['address'] ?? '') ?></textarea>

                    </div>


                </div>


                <div class="buttons">

                    <a
                        href="index.html"
                        class="btn btn-back"
                    >
                        ← Kembali
                    </a>

                    <button
                        type="submit"
                        class="btn btn-save"
                    >
                        💾 Simpan Perubahan
                    </button>

                </div>


            </form>


        </div>

    </div>

</div>


</body>

</html>