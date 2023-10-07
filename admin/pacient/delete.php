<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}

Pacient::delete($_SESSION['userId'], $_GET['id']);
header('Location: /admin/pacient');
exit();
