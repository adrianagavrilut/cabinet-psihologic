<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserAdmin()) {
    header("Location:/admin/login.php");
}

Medic::delete($_GET['id']);
header('Location: /admin/medic');
exit();
