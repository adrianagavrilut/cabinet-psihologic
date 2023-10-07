<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserAdmin()) {
    header("Location:/admin/login.php");
}

$articole = Articol::getAll();

$template = $twig->load('admin/blog/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'articole' => $articole]);
