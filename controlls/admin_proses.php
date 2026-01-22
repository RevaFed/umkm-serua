<?php
session_start();
require_once "../config/database.php";
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') exit;

$aksi = $_POST['aksi'];

if ($aksi === 'tambah') {
    $nama = $_POST['nama'];
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $jab  = $_POST['jabatan'];
    $hp   = $_POST['no_hp'];

    mysqli_query($conn,"
      INSERT INTO tbl_admin (nama, username, password, jabatan, no_hp)
      VALUES ('$nama','$user','$pass','$jab','$hp')
    ");
}

if ($aksi === 'edit') {
    $id     = (int)$_POST['id_admin'];
    $nama   = $_POST['nama'];
    $user   = $_POST['username'];
    $jab    = $_POST['jabatan'];
    $hp     = $_POST['no_hp'];

    mysqli_query($conn, "
      UPDATE tbl_admin SET
      nama='$nama',
      username='$user',
      jabatan='$jab',
      no_hp='$hp'
      WHERE id_admin='$id'
    ");
}


header("Location: ../public/admin/admin.php");
exit;
