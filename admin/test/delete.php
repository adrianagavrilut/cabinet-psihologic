<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}

Test::delete($_SESSION['userId'], $_GET['id']);
header('Location: /admin/test');
exit();
