<?php
session_start();
if (!isset($_SESSION['oase_admin'])) {
    header('Location: index.php');
    exit;
}