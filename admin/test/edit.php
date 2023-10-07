<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = Test::update($_SESSION['userId'], $_GET['id'], $_POST['denumire'], $_POST['descriere']);
        if ($result === true) {
            header('Location: /admin/test');
            exit();
        }
        $template = $twig->load('admin/test/edit.html.twig');

        echo $template->render(['userRole' => $_SESSION['rol'], 'test' => $result['test'], 'id' => $_GET['id'], 'errors' => $result['errors']]);
        exit();
    }

    $template = $twig->load('admin/test/edit.html.twig');

    $test = Test::get($_SESSION['userId'], $_GET['id']);
    echo $template->render(['userRole' => $_SESSION['rol'], 'test' => $test, 'id' => $_GET['id']]);
} catch (Exception $e) {
    // header('HTTP/1.1 500 Internal Server Error');
}
