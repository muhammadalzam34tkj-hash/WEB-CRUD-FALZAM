<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cari nama gambar sebelum menghapus baris dari database
    $stmt_select = $conn->prepare("SELECT gambar FROM kata_kata WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $res = $stmt_select->get_result()->fetch_assoc();

    // Hapus file foto dari folder uploads jika file ada
    if ($res && $res['gambar'] && file_exists("uploads/" . $res['gambar'])) {
        unlink("uploads/" . $res['gambar']);
    }

    // Hapus baris dari database
    $stmt = $conn->prepare("DELETE FROM kata_kata WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: index.php");
exit();
?>
