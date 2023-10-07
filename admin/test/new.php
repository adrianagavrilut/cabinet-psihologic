<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = Test::create($_SESSION['userId'], $_POST['denumire'], $_POST['descriere']);
    if ($result === true) {
        header('Location: /admin/test');
        exit();
    }
    $template = $twig->load('admin/test/new.html.twig');

    echo $template->render(['userRole' => $_SESSION['rol'], 'test' => $result['test'], 'errors' => $result['errors']]);
    exit();
}

$template = $twig->load('admin/test/new.html.twig');

echo $template->render(['userRole' => $_SESSION['rol']]);
