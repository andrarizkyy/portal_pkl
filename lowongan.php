<?php
session_start();
require 'config.php';

// Proteksi halaman
if (!isset($_SESSION['id_perusahaan']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'perusahaan') {
    header("Location: login.php");
    exit;
}

$id_perusahaan = $_SESSION['id_perusahaan'];
$message = '';

// Proses CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id_lowongan = !empty($_POST['id_lowongan']) ? (int)$_POST['id_lowongan'] : 0;
    $judul_lowongan = trim($_POST['judul_lowongan'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $persyaratan = trim($_POST['persyaratan'] ?? '');
    $kuota = (int)($_POST['kuota'] ?? 1);
    $status = $_POST['status'] ?? 'aktif';

    if (empty($judul_lowongan) || empty($deskripsi) || $kuota < 1) {
        $message = '<div class="alert alert-danger">Judul, deskripsi, dan kuota wajib diisi dengan benar.</div>';
    } else {
        try {
            if ($id_lowongan > 0) {
                // Edit
                $stmt = $pdo->prepare("
                    UPDATE lowongan SET 
                        judul_lowongan = ?, 
                        deskripsi = ?, 
                        persyaratan = ?, 
                        kuota = ?, 
                        status = ?
                    WHERE id_lowongan = ? AND id_perusahaan = ?
                ");
                $stmt->execute([$judul_lowongan, $deskripsi, $persyaratan, $kuota, $status, $id_lowongan, $id_perusahaan]);
                $message = '<div class="alert alert-success">Lowongan berhasil diedit.</div>';
            } else {
                // Tambah baru
                $stmt = $pdo->prepare("
                    INSERT INTO lowongan (
                        id_perusahaan, judul_lowongan, deskripsi, persyaratan, kuota, status
                    ) VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$id_perusahaan, $judul_lowongan, $deskripsi, $persyaratan, $kuota, $status]);
                $message = '<div class="alert alert-success">Lowongan baru berhasil ditambahkan.</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Hapus
if (isset($_GET['hapus'])) {
    $id_lowongan = (int)$_GET['hapus'];
    try {
        $stmt = $pdo->prepare("DELETE FROM lowongan WHERE id_lowongan = ? AND id_perusahaan = ?");
        $stmt->execute([$id_lowongan, $id_perusahaan]);
        $message = '<div class="alert alert-success">Lowongan berhasil dihapus.</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger">Gagal menghapus: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    header("Location: lowongan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lowongan PKL - PKL Hub</title>
  
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
  <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="lowongan.php" class="active"><i class="bi bi-briefcase-fill"></i> Lowongan PKL</a>
  <a href="pelamar.php"><i class="bi bi-people-fill"></i> Siswa Pelamar</a>
  <a href="riwayat.php"><i class="bi bi-clock-history"></i> Riwayat PKL</a>
  <a href="profil.php"><i class="bi bi-building-fill"></i> Profil Perusahaan</a>
  <a href="bantuan.php"><i class="bi bi-question-circle-fill"></i> Bantuan</a>
</nav>

<!-- MAIN CONTENT -->
<div class="main-content">

  <div class="page-header">
    <h1>Lowongan PKL</h1>
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalLowongan" data-mode="add">
      <i class="bi bi-plus-lg me-2"></i> Tambah Lowongan
    </button>
  </div>

  <?= $message ?>

  <!-- STATISTIK CARDS -->
  <div class="stats-grid">
    <?php
    $stats = ['aktif' => 0, 'terpasang' => 0, 'berakhir' => 0, 'total_pelamar' => 0];

    if ($pdo) {
      try {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS aktif FROM lowongan WHERE id_perusahaan = ? AND status = 'aktif'");
        $stmt->execute([$id_perusahaan]);
        $stats['aktif'] = $stmt->fetchColumn() ?? 0;

        // Karena tidak ada created_at, terpasang bulan ini kita hitung semua aktif sebagai contoh
        $stats['terpasang'] = $stats['aktif'];

        $stmt = $pdo->prepare("SELECT COUNT(*) AS berakhir FROM lowongan WHERE id_perusahaan = ? AND status = 'tutup'");
        $stmt->execute([$id_perusahaan]);
        $stats['berakhir'] = $stmt->fetchColumn() ?? 0;

        $stmt = $pdo->prepare("
          SELECT COUNT(*) AS total_pelamar
          FROM pendaftaran_pkl p
          JOIN lowongan l ON p.id_lowongan = l.id_lowongan
          WHERE l.id_perusahaan = ?
        ");
        $stmt->execute([$id_perusahaan]);
        $stats['total_pelamar'] = $stmt->fetchColumn() ?? 0;
      } catch (Exception $e) {}
    }
    ?>

    <div class="stat-card aktif">
      <div class="stat-icon"><i class="bi bi-briefcase-fill"></i></div>
      <h2><?= number_format($stats['aktif']) ?></h2>
      <p>Lowongan Aktif</p>
    </div>

    <div class="stat-card terpasang">
      <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
      <h2><?= number_format($stats['terpasang']) ?></h2>
      <p>Lowongan Terpasang Bulan Ini</p>
    </div>

    <div class="stat-card berakhir">
      <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
      <h2><?= number_format($stats['berakhir']) ?></h2>
      <p>Lowongan Berakhir Bulan Ini</p>
    </div>

    <div class="stat-card pelamar">
      <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
      <h2><?= number_format($stats['total_pelamar']) ?></h2>
      <p>Total Pelamar</p>
    </div>
  </div>

  <!-- SEARCH & FILTER -->
  <div class="filter-bar mb-4">
    <div class="input-group flex-grow-1">
      <input type="text" class="form-control" placeholder="Cari lowongan..." id="search">
      <button class="btn btn-primary" type="button"><i class="bi bi-search"></i> Cari</button>
    </div>
    <select class="form-select ms-2">
      <option>Semua Jurusan</option>
    </select>
    <select class="form-select ms-2">
      <option>Semua Status</option>
      <option value="aktif">Aktif</option>
      <option value="tutup">Tutup</option>
    </select>
    <select class="form-select ms-2">
      <option>Semua Sekolah</option>
    </select>
  </div>

  <!-- DAFTAR LOWONGAN -->
  <div class="lowongan-list">
    <?php
    $lowongan = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM lowongan WHERE id_perusahaan = ? ORDER BY id_lowongan DESC");
        $stmt->execute([$id_perusahaan]);
        $lowongan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}

    if (empty($lowongan)) {
        echo '<div class="alert alert-info">Belum ada lowongan yang dibuat.</div>';
    } else {
        foreach ($lowongan as $l):
            $status_text = $l['status'] === 'aktif' ? 'Berlangsung' : ucfirst($l['status']);
            $status_class = $l['status'] === 'aktif' ? 'bg-success' : 'bg-secondary';
    ?>
    <div class="lowongan-card">
      <div class="lowongan-icon">
        <i class="bi bi-laptop"></i>
      </div>
      <div class="lowongan-info">
        <h5><?= htmlspecialchars($l['judul_lowongan']) ?></h5>
        <p class="text-muted"><?= htmlspecialchars(substr($l['deskripsi'], 0, 80)) . '...' ?></p>
        <div class="meta">
          <i class="bi bi-people"></i> Kuota: <?= $l['kuota'] ?>
        </div>
      </div>
      <span class="badge <?= $status_class ?> status-badge"><?= $status_text ?></span>
      <div class="lowongan-actions">
        <button class="btn btn-sm btn-outline-primary edit-btn"
                data-bs-toggle="modal" data-bs-target="#modalLowongan"
                data-id="<?= $l['id_lowongan'] ?>"
                data-judul="<?= htmlspecialchars($l['judul_lowongan']) ?>"
                data-deskripsi="<?= htmlspecialchars($l['deskripsi']) ?>"
                data-persyaratan="<?= htmlspecialchars($l['persyaratan'] ?? '') ?>"
                data-kuota="<?= $l['kuota'] ?>"
                data-status="<?= $l['status'] ?>">
          <i class="bi bi-pencil"></i> Edit
        </button>
        <a href="?hapus=<?= $l['id_lowongan'] ?>" class="btn btn-sm btn-outline-danger"
           onclick="return confirm('Yakin ingin menghapus lowongan ini?');">
          <i class="bi bi-trash"></i>
        </a>
      </div>
    </div>
    <?php endforeach; ?>
    <?php } ?>
  </div>

</div>

<!-- MODAL TAMBAH / EDIT -->
<div class="modal fade" id="modalLowongan" tabindex="-1" aria-labelledby="modalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Tambah Lowongan Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="id_lowongan" id="id_lowongan">

          <div class="mb-3">
            <label class="form-label">Judul Lowongan</label>
            <input type="text" name="judul_lowongan" id="judul_lowongan" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Persyaratan</label>
            <textarea name="persyaratan" id="persyaratan" class="form-control" rows="3"></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Kuota</label>
              <input type="number" name="kuota" id="kuota" class="form-control" min="1" value="1" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" id="status" class="form-select">
                <option value="aktif">Aktif</option>
                <option value="tutup">Tutup</option>
                <option value="dibatalkan">Dibatalkan</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Isi modal saat tombol Edit ditekan
document.querySelectorAll('.edit-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.getElementById('modalLabel').textContent = 'Edit Lowongan';
    document.getElementById('id_lowongan').value = this.dataset.id;
    document.getElementById('judul_lowongan').value = this.dataset.judul;
    document.getElementById('deskripsi').value = this.dataset.deskripsi;
    document.getElementById('persyaratan').value = this.dataset.persyaratan;
    document.getElementById('kuota').value = this.dataset.kuota;
    document.getElementById('status').value = this.dataset.status;
  });
});

// Reset modal untuk tambah baru
document.getElementById('modalLowongan').addEventListener('show.bs.modal', function (e) {
  if (!e.relatedTarget.classList.contains('edit-btn')) {
    document.getElementById('modalLabel').textContent = 'Tambah Lowongan Baru';
    document.getElementById('id_lowongan').value = '';
    document.getElementById('judul_lowongan').value = '';
    document.getElementById('deskripsi').value = '';
    document.getElementById('persyaratan').value = '';
    document.getElementById('kuota').value = '1';
    document.getElementById('status').value = 'aktif';
  }
});
</script>

</body>
</html>