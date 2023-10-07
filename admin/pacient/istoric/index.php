<?php
require_once '../../../config.php';

if (!Utilizator::checkUserAuth() || !Utilizator::checkUserMedic()) {
    header("Location:/admin/login.php");
}
try {
    $istoric = Pacient::getHistory($_GET['id_pacient']);
    $pacient = Pacient::get($_SESSION['userId'], $_GET['id_pacient']);
} catch (Exception $e) {
    // log or otherwise register the error
    header('HTTP/1.1 500 Internal Server Error');
}

$template = $twig->load('admin/pacient/istoric/index.html.twig');

echo $template->render(['userRole' => $_SESSION['rol'], 'istoric' => $istoric, 'numePacient' => $pacient->nume]);
