<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserAdmin()) {
    header("Location:/admin/login.php");
}

Articol::delete($_GET['id']);
header('Location: /admin/blog');
exit();
