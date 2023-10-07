<?php
require_once '../../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}

Intrebare::delete($_SESSION['userId'], $_GET['id_test'], $_GET['id']);
header('Location: /admin/test/intrebare?id_test=' . $_GET['id_test']);
exit();
