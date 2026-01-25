<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../../login.php");
    exit;
}

if ($_SESSION['role'] !== 'warga') {
    header("Location: ../../login.php");
    exit;
}
