<?php
require_once '../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}
try {
    $tests = Test::getAll($_SESSION['userId']);
} catch (Exception $e) {
    // log or otherwise register the error
    header('HTTP/1.1 500 Internal Server Error');
}

$template = $twig->load('admin/test/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'tests' => $tests]);
