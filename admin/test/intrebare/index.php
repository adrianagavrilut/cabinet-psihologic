<?php
require_once '../../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}
try {
    $intrebari = Intrebare::getAll($_SESSION['userId'], $_GET['id_test']);
    $test = Test::get($_SESSION['userId'], $_GET['id_test']);
} catch (Exception $e) {
    // log or otherwise register the error
    header('HTTP/1.1 500 Internal Server Error');
}

$template = $twig->load('admin/test/intrebare/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'test' => $test, 'id_test' => $_GET['id_test'], 'intrebari' => $intrebari]);
