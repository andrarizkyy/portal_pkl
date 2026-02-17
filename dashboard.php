<?php
session_start();
require 'config.php';

// Proteksi halaman: harus login sebagai perusahaan
if (!isset($_SESSION['id_perusahaan']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'perusahaan') {
    header("Location: login.php");
    exit;
}

$id_perusahaan = $_SESSION['id_perusahaan'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Perusahaan - PKL Hub</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- SIDEBAR -->
<nav class="sidebar">
  <div class="sidebar-logo">
    <i class="bi bi-cube"></i> PKL Hub
  </div>
  <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="lowongan.php"><i class="bi bi-briefcase-fill"></i> Lowongan PKL</a>
  <a href="pelamar.php"><i class="bi bi-people-fill"></i> Siswa Pelamar</a>
  <a href="riwayat.php"><i class="bi bi-clock-history"></i> Riwayat PKL</a>
  <a href="profil.php"><i class="bi bi-building-fill"></i> Profil Perusahaan</a>
  <a href="bantuan.php"><i class="bi bi-question-circle-fill"></i> Bantuan</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</nav>

<!-- MAIN CONTENT -->
<div class="main-content">

  <div class="page-header">
    <h1>Dashboard Perusahaan</h1>
    <a href="lowongan.php" class="btn-add"><i class="bi bi-plus-lg me-2"></i> Pasang Lowongan</a>
  </div>

 <!-- STATISTIK CARDS -->
<div class="stats-grid">
  <?php
  $stats = ['total' => 0, 'diterima' => 0, 'ditolak' => 0, 'menunggu' => 0];

  try {
      $stmt = $pdo->prepare("
          SELECT 
              COUNT(*) AS total,
              SUM(CASE WHEN p.status = 'diterima' THEN 1 ELSE 0 END) AS diterima,
              SUM(CASE WHEN p.status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak,
              SUM(CASE WHEN p.status = 'menunggu' THEN 1 ELSE 0 END) AS menunggu
          FROM pendaftaran_pkl p
          JOIN lowongan l ON p.id_lowongan = l.id_lowongan
          WHERE l.id_perusahaan = ?
      ");
      $stmt->execute([$id_perusahaan]);
      $db_stats = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($db_stats) {
          $stats = $db_stats;
      }
  } catch (PDOException $e) {
      echo '<div class="alert alert-warning mb-4">
              Error fetching stats: ' . htmlspecialchars($e->getMessage()) . '
            </div>';
  }

  $max = max($stats['total'], $stats['diterima'], $stats['ditolak'], $stats['menunggu']) ?: 1;
  ?>

  <div class="stat-card total">
    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
    <h2><?= number_format($stats['total']) ?></h2>
    <p>Total Pendaftar</p>
    <div class="progress-bar-container">
      <div class="progress-bar" style="width: 100%; background: #60a5fa;"></div>
    </div>
  </div>

  <div class="stat-card diterima">
    <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
    <h2><?= number_format($stats['diterima']) ?></h2>
    <p>Diterima</p>
    <div class="progress-bar-container">
      <div class="progress-bar" style="width: <?= ($stats['diterima'] / $max) * 100 ?>%; background: #22c55e;"></div>
    </div>
  </div>

  <div class="stat-card ditolak">
    <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
    <h2><?= number_format($stats['ditolak']) ?></h2>
    <p>Ditolak</p>
    <div class="progress-bar-container">
      <div class="progress-bar" style="width: <?= ($stats['ditolak'] / $max) * 100 ?>%; background: #ef4444;"></div>
    </div>
  </div>

  <div class="stat-card menunggu">
    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
    <h2><?= number_format($stats['menunggu']) ?></h2>
    <p>Menunggu</p>
    <div class="progress-bar-container">
      <div class="progress-bar" style="width: <?= ($stats['menunggu'] / $max) * 100 ?>%; background: #f59e0b;"></div>
    </div>
  </div>
</div>

  <!-- STATISTIK PENDAFTAR MINGGUAN -->
  <div class="chart-card">
    <h3>Statistik Pendaftar</h3>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="dropdown">
      </div>
    </div>

    <?php
    // Data mingguan (ganti dengan query DB jika diperlukan)
    $mingguan = [
      ['minggu' => '4 Minggu', 'diterima' => 28, 'ditolak' => 8, 'menunggu' => 6],
      ['minggu' => '3 Minggu', 'diterima' => 38, 'ditolak' => 13, 'menunggu' => 11],
      ['minggu' => '2 Minggu', 'diterima' => 25, 'ditolak' => 19, 'menunggu' => 20],
      ['minggu' => '1 Minggu', 'diterima' => 42, 'ditolak' => 24, 'menunggu' => 35],
    ];

    $max_mingguan = 0;
    foreach ($mingguan as $m) {
      $max_mingguan = max($max_mingguan, $m['diterima'], $m['ditolak'], $m['menunggu']);
    }
    $max_mingguan = max($max_mingguan, 1);
    ?>

    <div class="weekly-bars">
      <?php foreach ($mingguan as $data): ?>
      <div class="bar-group">
        <div class="bar-label"><?= $data['minggu'] ?></div>
        <div class="bars">
          <div class="bar-item diterima" style="height: <?= ($data['diterima'] / $max_mingguan) * 260 ?>px;">
            <span><?= $data['diterima'] ?></span>
          </div>
          <div class="bar-item ditolak" style="height: <?= ($data['ditolak'] / $max_mingguan) * 260 ?>px;">
            <span><?= $data['ditolak'] ?></span>
          </div>
          <div class="bar-item menunggu" style="height: <?= ($data['menunggu'] / $max_mingguan) * 260 ?>px;">
            <span><?= $data['menunggu'] ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="bar-legend mt-4">
      <div><span class="legend-color diterima"></span> Diterima</div>
      <div><span class="legend-color ditolak"></span> Ditolak</div>
      <div><span class="legend-color menunggu"></span> Menunggu</div>
    </div>
  </div>

  <!-- SISWA PELAMAR TERBARU -->
  <div class="recent-table">
    <div class="card-header">
      <h5>Siswa Pelamar</h5>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Sekolah</th>
            <th>Jurusan</th>
            <th>Tgl. Lamar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $pelamar = [];
          if ($pdo) {
            try {
              $stmt = $pdo->prepare("
                SELECT p.id_pendaftaran, s.nama_lengkap AS nama_siswa, s.sekolah, s.jurusan, p.tanggal_lamar, p.status
                FROM pendaftaran_pkl p
                JOIN siswa s ON p.id_siswa = s.id_siswa
                JOIN lowongan l ON p.id_lowongan = l.id_lowongan
                WHERE l.id_perusahaan = ?
                ORDER BY p.tanggal_lamar DESC
                LIMIT 5
              ");
              $stmt->execute([$id_perusahaan]);
              $pelamar = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              echo '<div class="alert alert-warning">Error fetching pelamar: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
          }

          if (empty($pelamar)) {
            $pelamar = [
              ['nama_siswa'=>'Suharno', 'sekolah'=>'SMK Bina Prestasi', 'jurusan'=>'Rekayasa Perangkat Lunak', 'tanggal_lamar'=>'2025-02-02 09:15:00', 'status'=>'menunggu', 'id_pendaftaran'=>1],
              ['nama_siswa'=>'Dinda Permatasari', 'sekolah'=>'SMK Nusantara', 'jurusan'=>'Administrasi Perkantoran', 'tanggal_lamar'=>'2025-02-01 14:20:00', 'status'=>'menunggu', 'id_pendaftaran'=>2],
              ['nama_siswa'=>'Fitrianto Budi', 'sekolah'=>'SMK Negeri 1 Semarang', 'jurusan'=>'Teknologi Informasi', 'tanggal_lamar'=>'2025-01-31 10:45:00', 'status'=>'menunggu', 'id_pendaftaran'=>3],
              ['nama_siswa'=>'Rani Salsabila', 'sekolah'=>'SMK Teladan Bangsa', 'jurusan'=>'Akuntansi', 'tanggal_lamar'=>'2025-01-28 16:10:00', 'status'=>'ditolak', 'id_pendaftaran'=>4],
            ];
          }

          foreach ($pelamar as $p):
            $tgl = date('d M Y', strtotime($p['tanggal_lamar']));
            $status_class = $p['status'] == 'diterima' ? 'badge-diterima' : ($p['status'] == 'ditolak' ? 'badge-ditolak' : 'badge-menunggu');
          ?>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($p['nama_siswa']) ?>&background=random&size=40" class="rounded-circle me-3" width="40" height="40" alt="">
                <strong><?= htmlspecialchars($p['nama_siswa']) ?></strong>
              </div>
            </td>
            <td><?= htmlspecialchars($p['sekolah']) ?></td>
            <td><?= htmlspecialchars($p['jurusan']) ?></td>
            <td><?= $tgl ?> <small class="text-muted">(<?= date('H:i', strtotime($p['tanggal_lamar'])) ?>)</small></td>
            <td>
              <div class="action-buttons">
                <a href="pelamar.php?terima=<?= $p['id_pendaftaran'] ?>" class="btn btn-sm btn-success me-1 <?= $p['status'] != 'menunggu' ? 'disabled' : '' ?>">
                  <i class="bi bi-check-lg"></i> Terima
                </a>
                <a href="pelamar.php?tolak=<?= $p['id_pendaftaran'] ?>" class="btn btn-sm btn-danger <?= $p['status'] != 'menunggu' ? 'disabled' : '' ?>">
                  <i class="bi bi-x-lg"></i> Tolak
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="card-footer text-center">
      <a href="pelamar.php" class="text-primary">Lihat Selengkapnya <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>