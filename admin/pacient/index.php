<?php
require_once '../../config.php';
//because of GDPR ADMIN cannot see patients
if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}
try {
    $pacients = Pacient::getAll($_SESSION['userId']);
} catch (Exception $e) {
    // log or otherwise register the error
    header('HTTP/1.1 500 Internal Server Error');
}

$template = $twig->load('admin/pacient/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'pacients' => $pacients]);
