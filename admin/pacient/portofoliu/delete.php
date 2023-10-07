<?php
require_once '../../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}
Document::delete($_SESSION['userId'], $_GET['id']);
header('Location: /admin/pacient/portofoliu?id_pacient=' . $_GET['id_pacient']);
exit();
