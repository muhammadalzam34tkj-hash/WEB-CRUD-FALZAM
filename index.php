<?php
include 'db.php';

// Proses Simpan Data
if (isset($_POST['submit'])) {
    $teks = trim($_POST['teks']);
    $nama_gambar = NULL;

    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array(strtolower($ext), $allowed)) {
            $nama_gambar = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], "uploads/" . $nama_gambar);
        }
    }

    if (!empty($teks)) {
        $stmt = $conn->prepare("INSERT INTO kata_kata (teks, gambar) VALUES (?, ?)");
        $stmt->bind_param("ss", $teks, $nama_gambar);
        $stmt->execute();
    }

    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM kata_kata ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Kata & Foto Premium</title>
    <!-- Bootstrap 5 CSS CDN (Tanpa File CSS Terpisah) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fa-solid fa-quote-left me-2"></i> Galeri Kata & Foto
        </a>
    </div>
</nav>

<div class="container pb-5">
    <!-- Form Upload / Input -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Tambah Postingan Baru
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kata-Kata / Kutipan</label>
                            <textarea name="teks" class="form-control form-control-lg fs-6" rows="3" required placeholder="Tulis kata-kata atau inspirasi Anda di sini..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Upload Foto (Opsional)</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                            <div class="form-text">Format didukung: JPG, PNG, GIF, WEBP.</div>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary btn-lg w-100 fw-semibold rounded-3 shadow-sm">
                            <i class="fa-solid fa-paper-plane me-2"></i>Publikasikan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Daftar Postingan -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-layer-group me-2"></i>Daftar Postingan</h4>
        <span class="badge bg-primary rounded-pill fs-6 px-3 py-2"><?= $result->num_rows ?> Postingan</span>
    </div>

    <!-- Tampilan Card Grid (Tanpa Kolom ID) -->
    <?php if ($result->num_rows == 0): ?>
        <div class="alert alert-info text-center py-4 rounded-4 shadow-sm">
            <i class="fa-solid fa-circle-info fa-2x mb-2"></i>
            <p class="mb-0 fs-5">Belum ada postingan. Silakan tambah postingan pertama Anda!</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden d-flex flex-column">
                        <?php if ($row['gambar']): ?>
                            <div class="position-relative">
                                <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" class="card-img-top" style="height: 220px; object-fit: cover;" alt="Foto">
                                <!-- Tombol Download Foto -->
                                <a href="uploads/<?= htmlspecialchars($row['gambar']) ?>" download="<?= htmlspecialchars($row['gambar']) ?>" class="btn btn-light btn-sm rounded-pill shadow-sm position-absolute top-0 end-0 m-3 fw-semibold text-dark">
                                    <i class="fa-solid fa-download me-1 text-success"></i> Download
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="bg-secondary bg-opacity-10 text-muted d-flex align-items-center justify-content-center" style="height: 140px;">
                                <i class="fa-regular fa-image fa-2x me-2"></i> Tanpa Foto
                            </div>
                        <?php endif; ?>

                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="card-text fs-6 text-secondary mb-3" style="white-space: pre-wrap;"><?= htmlspecialchars($row['teks']) ?></p>
                            </div>
                            <div class="pt-3 border-top mt-auto">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> <?= date('d M Y, H:i', strtotime($row['created_at'])) ?></small>
                                </div>
                                <div class="btn-group w-100" role="group">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning btn-sm fw-semibold">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm fw-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
