<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../../login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}
