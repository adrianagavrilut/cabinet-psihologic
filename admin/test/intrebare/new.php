<?php
require_once '../../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = Intrebare::create($_SESSION['userId'], $_GET['id_test'], $_POST['continut']);
    if ($result === true) {
        header('Location: /admin/test/intrebare?id_test=' . $_GET['id_test']);
        exit();
    }
    $template = $twig->load('admin/test/intrebare/new.html.twig');

    echo $template->render(['userRole' => $_SESSION['rol'], 'id_test' => $_GET['id_test'], 'intrebare' => $result['intrebare'], 'errors' => $result['errors']]);
    exit();
}

$template = $twig->load('admin/test/intrebare/new.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'id_test' => $_GET['id_test']]);
