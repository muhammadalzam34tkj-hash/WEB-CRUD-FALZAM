<?php
include 'db.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM kata_kata WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['update'])) {
    $teks = trim($_POST['teks']);
    $nama_gambar = $data['gambar'];

    if (!empty($_FILES['gambar']['name'])) {
        // Hapus foto lama dari direktori uploads
        if ($data['gambar'] && file_exists("uploads/" . $data['gambar'])) {
            unlink("uploads/" . $data['gambar']);
        }

        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_gambar = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "uploads/" . $nama_gambar);
    }

    $stmt_update = $conn->prepare("UPDATE kata_kata SET teks = ?, gambar = ? WHERE id = ?");
    $stmt_update->bind_param("ssi", $teks, $nama_gambar, $id);
    $stmt_update->execute();

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Postingan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fa-solid fa-quote-left me-2"></i> Galeri Kata & Foto
        </a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-warning">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Edit Postingan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kata-Kata / Kutipan</label>
                            <textarea name="teks" class="form-control" rows="4" required><?= htmlspecialchars($data['teks']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Foto Saat Ini</label>
                            <div>
                                <?php if ($data['gambar']): ?>
                                    <div class="position-relative d-inline-block mb-2">
                                        <img src="uploads/<?= htmlspecialchars($data['gambar']) ?>" class="img-fluid rounded-3 border" style="max-height: 200px;" alt="Preview">
                                        <!-- Tombol Download di Halaman Edit -->
                                        <a href="uploads/<?= htmlspecialchars($data['gambar']) ?>" download class="btn btn-sm btn-success position-absolute bottom-0 end-0 m-2">
                                            <i class="fa-solid fa-download me-1"></i> Download
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted italic"><i class="fa-regular fa-image me-1"></i> Tidak ada foto terlampir.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Ganti Foto (Opsional)</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update" class="btn btn-warning fw-semibold text-white flex-grow-1">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                            </button>
                            <a href="index.php" class="btn btn-secondary fw-semibold">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
