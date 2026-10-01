<?php
require_once 'config.php';
require_login();

$data = read_json('data.json', []);

$role = $_SESSION['user']['role'] ?? 'user';
$nama = $_SESSION['user']['name'] ?? 'Pengguna';

$total = count($data);
$laki = 0;
$perempuan = 0;

foreach ($data as $item) {
    $gender = strtoupper($item['gender'] ?? '');
    if ($gender === 'MALE') { $laki++; }
    if ($gender === 'FEMALE') { $perempuan++; }
}

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
$isAdmin = ($role === 'admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Sistem Data Siswa</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body { font-family: "Segoe UI", Arial, sans-serif; background: #f5f7fb; color: #1e293b; transition: background 0.3s, color 0.3s; }

/* SIDEBAR */
.sidebar { position: fixed; top: 0; left: 0; width: 250px; height: 100vh; padding: 24px 16px; background: linear-gradient(180deg, #111827, #172554, #1e1b4b); color: white; z-index: 100; box-shadow: 8px 0 30px rgba(15,23,42,0.08); }
.logo { display: flex; align-items: center; gap: 12px; padding: 4px 10px 24px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.logo-icon { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 13px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); font-size: 21px; }
.logo-text strong { display: block; color: white; font-size: 15px; }
.logo-text span { display: block; color: #94a3b8; font-size: 10px; margin-top: 3px; }
.menu-title { color: #64748b; font-size: 10px; font-weight: 700; letter-spacing: 1px; margin: 20px 12px 9px; text-transform: uppercase; }
.menu a { display: flex; align-items: center; gap: 12px; padding: 12px 13px; margin-bottom: 5px; border-radius: 10px; color: #cbd5e1; text-decoration: none; font-size: 13px; transition: 0.25s; }
.menu a:hover { background: rgba(255,255,255,0.08); color: white; transform: translateX(3px); }
.menu a.active { color: white; background: linear-gradient(135deg, #2563eb, #6366f1); }
.menu-icon { width: 24px; text-align: center; font-size: 16px; }
.logout { color: #fca5a5 !important; }
.logout:hover { background: rgba(239,68,68,0.12) !important; color: #fecaca !important; }

/* MAIN */
.main { min-height: 100vh; margin-left: 250px; }

/* TOPBAR */
.topbar { position: sticky; top: 0; z-index: 50; height: 74px; display: flex; align-items: center; justify-content: space-between; padding: 0 32px; background: rgba(255,255,255,0.94); backdrop-filter: blur(15px); border-bottom: 1px solid #e8edf5; }
.topbar-left h1 { color: #0f172a; font-size: 20px; margin-bottom: 2px; }
.topbar-left span { color: #94a3b8; font-size: 11px; }
.user-info { display: flex; align-items: center; gap: 11px; }
.user-text { text-align: right; }
.user-name { color: #1e293b; font-size: 13px; font-weight: 700; }
.user-role { display: inline-block; padding: 3px 8px; margin-top: 3px; border-radius: 20px; background: #eef2ff; color: #4f46e5; font-size: 9px; font-weight: 700; text-transform: uppercase; }
.avatar { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 12px; color: white; font-weight: 800; background: linear-gradient(135deg, #2563eb, #7c3aed); }

/* KONTEN */
.content { padding: 30px; }
.welcome { margin-bottom: 25px; }
.welcome h2 { color: #0f172a; font-size: 25px; margin-bottom: 7px; }
.welcome p { color: #64748b; font-size: 13px; }

/* ALERT */
.alert { display: flex; align-items: center; gap: 10px; padding: 14px 17px; margin-bottom: 20px; border-radius: 12px; font-size: 13px; font-weight: 600; }
.alert-success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

/* STATS */
.stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 25px; }
.stat-card { position: relative; overflow: hidden; padding: 22px; border: 1px solid #e8edf5; border-radius: 16px; background: white; box-shadow: 0 5px 20px rgba(15,23,42,0.04); transition: 0.25s; }
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(15,23,42,0.08); }
.stat-top { display: flex; align-items: center; justify-content: space-between; }
.stat-title { color: #64748b; font-size: 12px; font-weight: 600; margin-bottom: 7px; }
.stat-number { color: #0f172a; font-size: 30px; font-weight: 800; }
.stat-icon { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 20px; }
.icon-blue { background: #eff6ff; }
.icon-purple { background: #f5f3ff; }
.icon-pink { background: #fdf2f8; }

/* DATA */
.data-card { overflow: hidden; border: 1px solid #e8edf5; border-radius: 16px; background: white; box-shadow: 0 5px 20px rgba(15,23,42,0.04); scroll-margin-top: 95px; }
.data-header { display: flex; align-items: center; justify-content: space-between; gap: 15px; padding: 20px 22px; border-bottom: 1px solid #edf1f6; }
.data-title { display: flex; align-items: center; gap: 12px; }
.data-title-icon { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 11px; background: #eef2ff; font-size: 19px; }
.data-header h3 { color: #0f172a; font-size: 16px; margin-bottom: 3px; }
.data-header p { color: #94a3b8; font-size: 11px; }
.header-actions { display: flex; gap: 8px; flex-wrap: wrap; }

/* TOMBOL */
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 14px; border: none; border-radius: 9px; font-size: 12px; font-weight: 700; text-decoration: none; cursor: pointer; }
.btn-primary { color: white; background: linear-gradient(135deg, #2563eb, #6366f1); }
.btn-dark { color: #334155; background: #f1f5f9; border: 1px solid #e2e8f0; }

/* FILTER */
.filters { display: flex; gap: 10px; flex-wrap: wrap; padding: 17px 22px; background: #fafbfe; border-bottom: 1px solid #edf1f6; }
.search-box { position: relative; flex: 1; min-width: 250px; }
.search-box span { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
.filters input, .filters select { height: 40px; border: 1px solid #dbe2ea; border-radius: 9px; outline: none; background: white; color: #334155; font-size: 12px; }
.filters input { width: 100%; padding: 0 12px 0 35px; }
.filters select { padding: 0 12px; cursor: pointer; }

/* TABEL */
.table-wrapper { width: 100%; overflow-x: auto; }
table { width: 100%; min-width: 1100px; border-collapse: collapse; }
th { padding: 14px; background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 800; text-align: left; text-transform: uppercase; white-space: nowrap; border-bottom: 1px solid #e8edf5; }
td { padding: 14px; color: #475569; font-size: 12px; border-bottom: 1px solid #eef2f7; }
tbody tr:hover { background: #fafbff; }
td strong { color: #1e293b; }
th:last-child, td:last-child { position: sticky; right: 0; z-index: 1; min-width: 132px; background: white; box-shadow: -8px 0 12px rgba(15,23,42,0.05); }
th:last-child { z-index: 2; background: #f8fafc; }
tbody tr:hover td:last-child { background: #fafbff; }

/* BADGE */
.badge { display: inline-flex; padding: 6px 9px; border-radius: 20px; font-size: 10px; font-weight: 700; }
.badge.male { background: #eff6ff; color: #2563eb; }
.badge.female { background: #fdf2f8; color: #db2777; }

/* AKSI */
.actions { display: flex; gap: 5px; white-space: nowrap; }
.actions a { display: inline-flex; padding: 7px 9px; border-radius: 7px; font-size: 10px; font-weight: 700; text-decoration: none; }
.edit { background: #eff6ff; color: #2563eb; }
.delete { background: #fef2f2; color: #dc2626; }

/* PAGINASI */
.pagination { display: flex; align-items: center; justify-content: center; gap: 5px; padding: 18px; border-top: 1px solid #edf1f6; }
.page-btn { min-width: 34px; height: 34px; padding: 0 10px; border: 1px solid #dbe2ea; border-radius: 8px; background: white; color: #475569; cursor: pointer; font-weight: 700; }
.page-btn.active { background: linear-gradient(135deg, #2563eb, #6366f1); color: white; border-color: #4f46e5; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* FOOTER */
.footer { padding: 25px; color: #94a3b8; font-size: 11px; text-align: center; }

/* MODE GELAP */
body.dark { background: #080d1a; color: #e2e8f0; }
body.dark .topbar { background: #0f172a; border-color: #1e293b; }
body.dark .topbar-left h1, body.dark .welcome h2, body.dark .data-header h3 { color: #f8fafc; }
body.dark .user-name { color: #f1f5f9; }
body.dark .stat-card, body.dark .data-card { background: #0f172a; border-color: #1e293b; }
body.dark .stat-number { color: #f8fafc; }
body.dark .filters { background: #111827; border-color: #1e293b; }
body.dark .filters input, body.dark .filters select { background: #0f172a; color: #e2e8f0; border-color: #334155; }
body.dark th { background: #111827; color: #94a3b8; border-color: #1e293b; }
body.dark td { color: #cbd5e1; border-color: #1e293b; }
body.dark td:last-child { background: #0f172a; }
body.dark th:last-child { background: #111827; }
body.dark td strong { color: #f1f5f9; }
body.dark tbody tr:hover { background: #111c30; }
body.dark tbody tr:hover td:last-child { background: #111c30; }
body.dark .page-btn { background: #0f172a; color: #cbd5e1; border-color: #334155; }
body.dark .btn-dark { background: #1e293b; color: #e2e8f0; border-color: #334155; }

/* RESPONSIVE */
@media (max-width: 800px) {
  .sidebar { position: relative; width: 100%; height: auto; }
  .main { margin-left: 0; }
  .stats { grid-template-columns: 1fr; }
  .topbar { padding: 0 18px; }
  .content { padding: 18px; }
}
@media (max-width: 550px) {
  .user-text { display: none; }
  .data-header { align-items: flex-start; flex-direction: column; }
  .search-box { min-width: 100%; }
  .filters select { width: 100%; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="logo">
    <div class="logo-icon">🎓</div>
    <div class="logo-text">
      <strong>DATA SISWA</strong>
      <span>Manajemen Siswa</span>
    </div>
  </div>

  <div class="menu">
    <div class="menu-title">Menu Utama</div>

    <a href="dashboard.php" class="active"><span class="menu-icon">🏠</span> Dashboard</a>
    <a href="dashboard.php#data-siswa"><span class="menu-icon">👨‍🎓</span> Data Siswa</a>

    <?php if ($isAdmin): ?>
    <a href="tambah.php"><span class="menu-icon">➕</span> Tambah Data</a>
    <?php endif; ?>

    <a href="notifications.php"><span class="menu-icon">🔔</span> Notifikasi</a>
    <a href="profile.php"><span class="menu-icon">👤</span> Profil</a>

    <div class="menu-title">Akun</div>

    <a href="logout.php" class="logout" onclick="return confirm('Yakin ingin logout?')">
      <span class="menu-icon">🚪</span> Logout
    </a>
  </div>
</aside>

<!-- MAIN -->
<main class="main">

  <!-- TOPBAR -->
  <header class="topbar">
    <div class="topbar-left">
      <h1>Dashboard</h1>
      <span>Sistem Informasi Data Siswa</span>
    </div>
    <div class="user-info">
      <div class="user-text">
        <div class="user-name"><?= e($nama) ?></div>
        <div class="user-role"><?= e($role) ?></div>
      </div>
      <div class="avatar"><?= strtoupper(substr($nama, 0, 1)) ?></div>
    </div>
  </header>

  <!-- CONTENT -->
  <section class="content">

    <div class="welcome">
      <h2>Selamat datang, <?= e($nama) ?> 👋</h2>
      <p>Kelola dan pantau data siswa dengan mudah melalui dashboard ini.</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success"><span>✅</span> <?= e($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-error"><span>❌</span> <?= e($error) ?></div>
    <?php endif; ?>

    <!-- STATISTIK -->
    <div class="stats">
      <div class="stat-card">
        <div class="stat-top">
          <div>
            <div class="stat-title">TOTAL SISWA</div>
            <div class="stat-number"><?= $total ?></div>
          </div>
          <div class="stat-icon icon-blue">👨‍🎓</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-top">
          <div>
            <div class="stat-title">LAKI-LAKI</div>
            <div class="stat-number"><?= $laki ?></div>
          </div>
          <div class="stat-icon icon-purple">👦</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-top">
          <div>
            <div class="stat-title">PEREMPUAN</div>
            <div class="stat-number"><?= $perempuan ?></div>
          </div>
          <div class="stat-icon icon-pink">👧</div>
        </div>
      </div>
    </div>

    <!-- DATA SISWA -->
    <div class="data-card" id="data-siswa">
      <div class="data-header">
        <div class="data-title">
          <div class="data-title-icon">📋</div>
          <div>
            <h3>Data Siswa</h3>
            <p>Daftar seluruh data siswa yang tersimpan dalam sistem.</p>
          </div>
        </div>
        <div class="header-actions">
          <button type="button" class="btn btn-dark" id="darkButton" onclick="toggleDarkMode()">🌙 Gelap</button>
          <?php if ($isAdmin): ?>
          <a href="tambah.php" class="btn btn-primary">➕ Tambah Data</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- FILTER -->
      <div class="filters">
        <div class="search-box">
          <span>🔍</span>
          <input type="text" id="search" placeholder="Cari nama atau NISN..." oninput="filterData()">
        </div>
        <select id="genderFilter" onchange="filterData()">
          <option value="">Semua Gender</option>
          <option value="MALE">👦 Laki-laki</option>
          <option value="FEMALE">👧 Perempuan</option>
        </select>
      </div>

      <!-- TABEL -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>NISN</th>
              <th>Tempat / Tanggal Lahir</th>
              <th>Jenis Kelamin</th>
              <th>Email</th>
              <th>Telepon</th>
              <th>Alamat</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="hasil">
            <tr>
              <td colspan="9" style="text-align:center; padding:35px;">⏳ Memuat data...</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- PAGINASI -->
      <div id="pagination" class="pagination"></div>
    </div>

    <div class="footer">
      © <?= date('Y') ?> Sistem Informasi Data Siswa • Data JSON
    </div>

  </section>
</main>

<script>
let allData = [];
let currentPage = 1;
const rowsPerPage = 5;
const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
const dashboardLink = document.querySelector('.menu a[href="dashboard.php"]');
const dataLink = document.querySelector('.menu a[href="dashboard.php#data-siswa"]');

function updateActiveMenu() {
  const dataIsActive = window.location.hash === "#data-siswa";
  dashboardLink.classList.toggle("active", !dataIsActive);
  dataLink.classList.toggle("active", dataIsActive);
}

window.addEventListener("hashchange", updateActiveMenu);
updateActiveMenu();

/* MUAT DATA */
function loadData() {
  fetch("data_json.php")
    .then(function (response) {
      if (!response.ok) { throw new Error("Gagal mengambil data"); }
      return response.json();
    })
    .then(function (data) {
      allData = data;
      currentPage = 1;
      render();
    })
    .catch(function (error) {
      document.getElementById("hasil").innerHTML =
        '<tr><td colspan="9" style="text-align:center; padding:35px; color:#dc2626;">❌ Gagal mengambil data JSON.</td></tr>';
      console.error(error);
    });
}

/* DAPATKAN DATA TERFILTER */
function getFiltered() {
  const keyword = document.getElementById("search").value.toLowerCase().trim();
  const gender = document.getElementById("genderFilter").value;

  return allData.filter(function (item) {
    const name = String(item.name || "").toLowerCase();
    const nisn = String(item.nisn || "").toLowerCase();
    const searchMatch = name.includes(keyword) || nisn.includes(keyword);
    const genderMatch = gender === "" || String(item.gender || "").toUpperCase() === gender;
    return searchMatch && genderMatch;
  });
}

/* FILTER (kembali ke halaman 1) */
function filterData() {
  currentPage = 1;
  render();
}

/* RENDER */
function render() {
  renderTable(getFiltered());
}

/* TABEL */
function renderTable(data) {
  const tbody = document.getElementById("hasil");

  if (data.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="9" style="text-align:center; padding:40px; color:#94a3b8;">🔍<br><br>Data tidak ditemukan.</td></tr>';
    renderPagination(0);
    return;
  }

  const totalPages = Math.ceil(data.length / rowsPerPage);
  if (currentPage > totalPages) { currentPage = totalPages; }

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const pageData = data.slice(start, end);

  let html = "";

  pageData.forEach(function (item) {
    const gender = String(item.gender || "").toUpperCase();
    let genderClass = "female";
    let genderText = "👧 Perempuan";

    if (gender === "MALE") {
      genderClass = "male";
      genderText = "👦 Laki-laki";
    }

    let actionHTML = "";

    if (isAdmin) {
      actionHTML =
        '<a href="edit.php?id=' + encodeURIComponent(item.id) + '" class="edit">✏️ Edit</a>' +
        '<a href="hapus.php?id=' + encodeURIComponent(item.id) + '" class="delete" ' +
        'onclick="return confirmDelete(\'' + escapeJS(escapeHTML(item.name)) + '\')">🗑️ Hapus</a>';
    } else {
      actionHTML = '<span style="color:#94a3b8; font-size:10px;">Lihat saja</span>';
    }

    html +=
      "<tr>" +
        "<td>#" + escapeHTML(item.id) + "</td>" +
        "<td><strong>" + escapeHTML(item.name) + "</strong></td>" +
        "<td>" + escapeHTML(item.nisn) + "</td>" +
        "<td>" + escapeHTML(item.ttl) + "</td>" +
        '<td><span class="badge ' + genderClass + '">' + genderText + "</span></td>" +
        "<td>" + escapeHTML(item.email) + "</td>" +
        "<td>" + escapeHTML(item.phone || "-") + "</td>" +
        "<td>" + escapeHTML(item.address || item.alamat) + "</td>" +
        '<td><div class="actions">' + actionHTML + "</div></td>" +
      "</tr>";
  });

  tbody.innerHTML = html;
  renderPagination(data.length);
}

/* PAGINASI */
function renderPagination(totalData) {
  const pagination = document.getElementById("pagination");
  const totalPages = Math.max(1, Math.ceil(totalData / rowsPerPage));

  let html = "";

  html += '<button class="page-btn" onclick="changePage(' + (currentPage - 1) + ')"' +
          (currentPage === 1 ? " disabled" : "") + ">‹</button>";

  for (let i = 1; i <= totalPages; i++) {
    html += '<button class="page-btn' + (i === currentPage ? " active" : "") +
            '" onclick="changePage(' + i + ')">' + i + "</button>";
  }

  html += '<button class="page-btn" onclick="changePage(' + (currentPage + 1) + ')"' +
          (currentPage === totalPages ? " disabled" : "") + ">›</button>";

  pagination.innerHTML = html;
}

/* GANTI HALAMAN */
function changePage(page) {
  const totalPages = Math.ceil(getFiltered().length / rowsPerPage);

  if (page < 1 || page > totalPages) { return; }

  currentPage = page;
  render();

  document.getElementById("data-siswa").scrollIntoView({
    behavior: "smooth",
    block: "start"
  });
}

/* HAPUS */
function confirmDelete(name) {
  return confirm('Yakin ingin menghapus data "' + name + '"?');
}

/* ESCAPE HTML */
function escapeHTML(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

/* ESCAPE JS */
function escapeJS(value) {
  return String(value ?? "")
    .replace(/\\/g, "\\\\")
    .replace(/'/g, "\\'")
    .replace(/"/g, '\\"')
    .replace(/\r/g, "\\r")
    .replace(/\n/g, "\\n");
}

/* MODE GELAP */
function toggleDarkMode() {
  document.body.classList.toggle("dark");
  const dark = document.body.classList.contains("dark");
  document.getElementById("darkButton").innerHTML = dark ? "☀️ Terang" : "🌙 Gelap";
  localStorage.setItem("darkMode", dark ? "on" : "off");
}

/* MUAT MODE GELAP */
if (localStorage.getItem("darkMode") === "on") {
  document.body.classList.add("dark");
  document.getElementById("darkButton").innerHTML = "☀️ Terang";
}

/* MULAI */
loadData();
</script>

</body>
</html>