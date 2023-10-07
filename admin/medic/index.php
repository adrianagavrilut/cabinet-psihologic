<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserAdmin()) {
    header("Location:/admin/login.php");
}

$doctors = Medic::getAll();

$template = $twig->load('admin/medic/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'doctors' => $doctors]);
