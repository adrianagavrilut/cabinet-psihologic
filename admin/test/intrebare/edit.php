<?php
require_once '../../../config.php';
if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = Intrebare::update($_SESSION['userId'], $_GET['id_test'], $_GET['id'], $_POST['continut']);
        if ($result === true) {
            header('Location: /admin/test/intrebare?id_test=' . $_GET['id_test']);
            exit();
        }
        $template = $twig->load('admin/test/intrebare/edit.html.twig');

        echo $template->render(['userRole' => $_SESSION['rol'], 'id_test' => $_GET['id_test'], 'intrebare' => $result['intrebare'], 'id' => $_GET['id'], 'errors' => $result['errors']]);
        exit();
    }

    $template = $twig->load('admin/test/intrebare/edit.html.twig');

    $intrebare = Intrebare::get($_SESSION['userId'], $_GET['id_test'], $_GET['id']);
    echo $template->render(['userRole' => $_SESSION['rol'], 'id_test' => $_GET['id_test'], 'intrebare' => $intrebare, 'id' => $_GET['id']]);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
}
