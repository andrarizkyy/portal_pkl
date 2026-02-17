<?php require 'config.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Siswa Pelamar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Sidebar sama seperti dashboard.php -->

<div class="content">
  <h3>Siswa Pelamar</h3>

  <?php
  // Proses terima / tolak
  if (isset($_GET['terima'])) {
      $id = (int)$_GET['terima'];
      if ($pdo) {
          $pdo->prepare("UPDATE pendaftaran_pkl SET status = 'diterima' WHERE id_pendaftaran = ?")
              ->execute([$id]);
      }
  }
  if (isset($_GET['tolak'])) {
      $id = (int)$_GET['tolak'];
      if ($pdo) {
          $pdo->prepare("UPDATE pendaftaran_pkl SET status = 'ditolak' WHERE id_pendaftaran = ?")
              ->execute([$id]);
      }
  }

  // Ambil data pelamar
  $pelamar = [];
  if ($pdo) {
      $stmt = $pdo->prepare("
          SELECT p.*, l.judul_lowongan 
          FROM pendaftaran_pkl p
          JOIN lowongan l ON p.id_lowongan = l.id_lowongan
          WHERE l.id_perusahaan = ?
          ORDER BY p.tanggal_lamar DESC
      ");
      $stmt->execute([TEMP_PERUSAHAAN_ID]);
      $pelamar = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  ?>

  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>Nama Siswa</th>
          <th>Sekolah</th>
          <th>Jurusan</th>
          <th>Lowongan</th>
          <th>Tanggal Lamar</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($pelamar)): ?>
          <tr><td colspan="7" class="text-center py-4">Belum ada pelamar</td></tr>
        <?php else: ?>
          <?php foreach ($pelamar as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['nama_siswa']) ?></td>
            <td><?= htmlspecialchars($p['sekolah']) ?></td>
            <td><?= htmlspecialchars($p['jurusan']) ?></td>
            <td><?= htmlspecialchars($p['judul_lowongan']) ?></td>
            <td><?= date('d M Y H:i', strtotime($p['tanggal_lamar'])) ?></td>
            <td>
              <?php
              $st = $p['status'];
              $class = $st=='diterima' ? 'bg-success' : ($st=='ditolak' ? 'bg-danger' : 'bg-warning text-dark');
              ?>
              <span class="badge <?= $class ?>"><?= ucfirst($st) ?></span>
            </td>
            <td>
              <?php if ($p['status'] === 'menunggu'): ?>
                <a href="?terima=<?= $p['id_pendaftaran'] ?>" class="btn btn-sm btn-success">Terima</a>
                <a href="?tolak=<?= $p['id_pendaftaran'] ?>" class="btn btn-sm btn-danger">Tolak</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>